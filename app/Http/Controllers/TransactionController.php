<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Product;
use App\Models\Customer;
use App\Models\PaymentReceipt;
use App\Models\Cashflow;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['customer', 'details']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_faktur', 'like', "%{$search}%")
                  ->orWhere('nama_pelanggan_manual', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($cq) use ($search) {
                      $cq->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status_pembayaran')) {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        }

        $transactions = $query->latest('tanggal')->latest('id')->paginate(10)->appends($request->all());

        // Ringkasan transaksi terpilih
        $totalPenjualan = (clone $query)->sum('grand_total');
        $totalTerbayar = (clone $query)->sum('bayar');
        $totalPiutang = (clone $query)->sum('sisa_piutang');

        return view('transactions.index', compact('transactions', 'totalPenjualan', 'totalTerbayar', 'totalPiutang'));
    }

    public function create()
    {
        $products = Product::where('stok', '>', 0)->orderBy('nama_produk', 'asc')->get();
        $allProducts = Product::orderBy('nama_produk', 'asc')->get();
        $customers = Customer::orderBy('nama', 'asc')->get();

        // Generate No Faktur INV-YYYYMMDD-XXX
        $todayStr = Carbon::now()->format('Ymd');
        $lastTrxToday = Transaction::whereDate('created_at', Carbon::today())->latest('id')->first();
        $nextNum = $lastTrxToday ? ((int)substr($lastTrxToday->no_faktur, -3) + 1) : 1;
        $defaultNoFaktur = 'INV-' . $todayStr . '-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        return view('transactions.create', compact('products', 'allProducts', 'customers', 'defaultNoFaktur'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_faktur' => 'required|unique:transactions,no_faktur',
            'tanggal' => 'required|date',
            'customer_id' => 'nullable|exists:customers,id',
            'nama_pelanggan_manual' => 'nullable|string|max:255',
            'no_hp_pelanggan' => 'nullable|string|max:50',
            'alamat_pengiriman' => 'nullable|string',
            'supir_armada' => 'nullable|string|max:255',
            'ongkos_kirim' => 'nullable|numeric|min:0',
            'diskon' => 'nullable|numeric|min:0',
            'bayar' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|in:tunai,transfer',
            'status_pengiriman' => 'required|in:ambil_sendiri,menunggu_kirim,proses_kirim,terkirim',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'catatan' => 'nullable|string',
        ]);

        return DB::transaction(function() use ($request) {
            $totalBarang = 0;
            $itemsData = [];

            // 1. Validasi stok untuk semua barang yang dipilih
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                if ($product->stok < $item['jumlah']) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', "Stok untuk {$product->nama_produk} tidak mencukupi! Tersedia: {$product->stok} {$product->satuan}, diminta: {$item['jumlah']} {$product->satuan}.");
                }

                $subtotal = $item['jumlah'] * $item['harga_satuan'];
                $totalBarang += $subtotal;

                $itemsData[] = [
                    'product' => $product,
                    'jumlah' => $item['jumlah'],
                    'harga_satuan' => $item['harga_satuan'],
                    'subtotal' => $subtotal,
                ];
            }

            $ongkosKirim = (float)($request->ongkos_kirim ?? 0);
            $diskon = (float)($request->diskon ?? 0);
            $grandTotal = ($totalBarang - $diskon) + $ongkosKirim;
            $bayar = (float)$request->bayar;

            $sisaPiutang = max(0, $grandTotal - $bayar);
            if ($sisaPiutang <= 0) {
                $statusPembayaran = 'lunas';
            } elseif ($bayar > 0) {
                $statusPembayaran = 'tempo';
            } else {
                $statusPembayaran = 'belum_lunas';
            }

            // 2. Simpan Transaksi Penjualan
            $transaction = Transaction::create([
                'no_faktur' => $request->no_faktur,
                'tanggal' => $request->tanggal,
                'customer_id' => $request->customer_id,
                'nama_pelanggan_manual' => $request->nama_pelanggan_manual,
                'no_hp_pelanggan' => $request->no_hp_pelanggan,
                'total_barang' => $totalBarang,
                'ongkos_kirim' => $ongkosKirim,
                'diskon' => $diskon,
                'grand_total' => $grandTotal,
                'bayar' => $bayar,
                'sisa_piutang' => $sisaPiutang,
                'status_pembayaran' => $statusPembayaran,
                'metode_pembayaran' => $request->metode_pembayaran,
                'status_pengiriman' => $request->status_pengiriman,
                'alamat_pengiriman' => $request->alamat_pengiriman,
                'supir_armada' => $request->supir_armada,
                'catatan' => $request->catatan,
                'user_id' => auth()->id() ?? 1,
            ]);

            // 3. Simpan detail item dan kurangi stok
            foreach ($itemsData as $data) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $data['product']->id,
                    'nama_produk_snapshot' => $data['product']->nama_produk,
                    'satuan_snapshot' => $data['product']->satuan,
                    'harga_satuan' => $data['harga_satuan'],
                    'harga_beli_snapshot' => $data['product']->harga_beli,
                    'jumlah' => $data['jumlah'],
                    'subtotal' => $data['subtotal'],
                ]);

                // Kurangi stok produk genteng
                $data['product']->decrement('stok', $data['jumlah']);
            }

            // 4. Catat arus kas masuk jika ada pembayaran tunai/transfer
            if ($bayar > 0) {
                $pelangganNama = $transaction->customer ? $transaction->customer->nama : ($transaction->nama_pelanggan_manual ?: 'Pelanggan Umum');
                Cashflow::create([
                    'tanggal' => $transaction->tanggal,
                    'jenis' => 'masuk',
                    'kategori' => $statusPembayaran === 'tempo' ? 'Penjualan Genteng (DP)' : 'Penjualan Genteng',
                    'nominal' => $bayar,
                    'referensi_tabel' => 'transactions',
                    'referensi_id' => $transaction->id,
                    'keterangan' => "Penjualan Faktur {$transaction->no_faktur} - {$pelangganNama} (Bayar: Rp " . number_format($bayar, 0, ',', '.') . ")",
                    'user_id' => auth()->id() ?? 1,
                ]);
            }

            return redirect()->route('transactions.show', $transaction->id)->with('success', 'Transaksi penjualan berhasil disimpan dan stok genteng telah diperbarui!');
        });
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['customer', 'details.product', 'paymentReceipts']);
        return view('transactions.show', compact('transaction'));
    }

    public function printInvoice(Transaction $transaction)
    {
        $transaction->load(['customer', 'details', 'paymentReceipts']);
        return view('transactions.invoice', compact('transaction'));
    }

    public function printDeliveryOrder(Transaction $transaction)
    {
        $transaction->load(['customer', 'details']);
        return view('transactions.delivery_order', compact('transaction'));
    }

    public function addPayment(Request $request, Transaction $transaction)
    {
        $request->validate([
            'nominal' => 'required|numeric|min:1|max:' . $transaction->sisa_piutang,
            'tanggal' => 'required|date',
            'metode_pembayaran' => 'required|in:tunai,transfer',
            'catatan' => 'nullable|string',
        ]);

        return DB::transaction(function() use ($request, $transaction) {
            $nominal = (float)$request->nominal;
            $todayStr = Carbon::now()->format('Ymd');
            $lastReceipt = PaymentReceipt::whereDate('created_at', Carbon::today())->latest('id')->first();
            $nextNum = $lastReceipt ? ((int)substr($lastReceipt->no_kwitansi, -3) + 1) : 1;
            $noKwitansi = 'KWT-' . $todayStr . '-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

            // 1. Simpan Payment Receipt
            $receipt = PaymentReceipt::create([
                'transaction_id' => $transaction->id,
                'no_kwitansi' => $noKwitansi,
                'tanggal' => $request->tanggal,
                'nominal' => $nominal,
                'metode_pembayaran' => $request->metode_pembayaran,
                'catatan' => $request->catatan,
                'user_id' => auth()->id() ?? 1,
            ]);

            // 2. Update Transaksi (tambah bayar, kurangi sisa piutang)
            $transaction->bayar += $nominal;
            $transaction->sisa_piutang = max(0, $transaction->grand_total - $transaction->bayar);

            if ($transaction->sisa_piutang <= 0) {
                $transaction->status_pembayaran = 'lunas';
            }

            $transaction->save();

            // 3. Catat Kas Masuk (Pelunasan Piutang)
            $pelangganNama = $transaction->customer ? $transaction->customer->nama : ($transaction->nama_pelanggan_manual ?: 'Pelanggan');
            Cashflow::create([
                'tanggal' => $request->tanggal,
                'jenis' => 'masuk',
                'kategori' => 'Pelunasan Piutang',
                'nominal' => $nominal,
                'referensi_tabel' => 'payment_receipts',
                'referensi_id' => $receipt->id,
                'keterangan' => "Cicilan Faktur {$transaction->no_faktur} ({$receipt->no_kwitansi}) - {$pelangganNama}",
                'user_id' => auth()->id() ?? 1,
            ]);

            return redirect()->route('transactions.show', $transaction->id)->with('success', "Pembayaran cicilan Rp " . number_format($nominal, 0, ',', '.') . " berhasil dicatat!");
        });
    }

    public function destroy(Transaction $transaction)
    {
        return DB::transaction(function() use ($transaction) {
            // Kembalikan stok genteng
            foreach ($transaction->details as $item) {
                if ($item->product_id) {
                    $prod = Product::find($item->product_id);
                    if ($prod) {
                        $prod->increment('stok', $item->jumlah);
                    }
                }
            }

            // Hapus cashflows terkait
            Cashflow::where('referensi_tabel', 'transactions')->where('referensi_id', $transaction->id)->delete();
            Cashflow::where('referensi_tabel', 'payment_receipts')->whereIn('referensi_id', $transaction->paymentReceipts()->pluck('id'))->delete();

            $transaction->delete();

            return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dibatalkan dan stok genteng telah dikembalikan ke gudang.');
        });
    }
}
