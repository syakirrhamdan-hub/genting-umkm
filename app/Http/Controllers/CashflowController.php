<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Cashflow;
use App\Models\Transaction;
use App\Models\Expense;
use App\Models\ExpenseCategory;

class CashflowController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        // Hitung saldo awal sebelum tanggal mulai
        $masukSebelumnya = Cashflow::where('jenis', 'masuk')->where('tanggal', '<', $startDate)->sum('nominal');
        $keluarSebelumnya = Cashflow::where('jenis', 'keluar')->where('tanggal', '<', $startDate)->sum('nominal');
        $saldoAwal = $masukSebelumnya - $keluarSebelumnya;

        $query = Cashflow::whereBetween('tanggal', [$startDate, $endDate]);

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        $cashflows = $query->orderBy('tanggal', 'asc')->orderBy('id', 'asc')->get();

        $totalMasukPeriode = $cashflows->where('jenis', 'masuk')->sum('nominal');
        $totalKeluarPeriode = $cashflows->where('jenis', 'keluar')->sum('nominal');
        $saldoAkhir = $saldoAwal + $totalMasukPeriode - $totalKeluarPeriode;

        return view('cashflows.index', compact(
            'cashflows',
            'startDate',
            'endDate',
            'saldoAwal',
            'totalMasukPeriode',
            'totalKeluarPeriode',
            'saldoAkhir'
        ));
    }

    public function profitAndLoss(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        // 1. Penjualan
        $transactions = Transaction::with('details')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get();

        $totalPenjualanBarang = $transactions->sum('total_barang');
        $totalDiskon = $transactions->sum('diskon');
        $totalOngkirDiterima = $transactions->sum('ongkos_kirim');
        $penjualanBersih = ($totalPenjualanBarang - $totalDiskon);

        // 2. HPP Genteng yang Terjual
        $totalHpp = 0;
        $totalGentengPcs = 0;
        foreach ($transactions as $trx) {
            foreach ($trx->details as $item) {
                $totalHpp += ($item->harga_beli_snapshot * $item->jumlah);
                $totalGentengPcs += $item->jumlah;
            }
        }

        $labaKotor = $penjualanBersih - $totalHpp;

        // 3. Biaya Operasional (Pengeluaran per Kategori)
        $expenses = Expense::with('category')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get();

        // Kecualikan 'Kulakan Stok Genteng' dari biaya operasional langsung jika sudah dihitung HPP,
        // atau tampilkan rincian lengkap untuk kejelasan kas toko
        $expensesByCategory = $expenses->groupBy(function($item) {
            return $item->category ? $item->category->nama_kategori : 'Lain-lain';
        })->map(function($group) {
            return $group->sum('nominal');
        });

        $totalOperasional = $expenses->sum('nominal');
        $labaBersih = $labaKotor + $totalOngkirDiterima - $totalOperasional;

        return view('cashflows.profit_loss', compact(
            'startDate',
            'endDate',
            'totalPenjualanBarang',
            'totalDiskon',
            'penjualanBersih',
            'totalOngkirDiterima',
            'totalHpp',
            'totalGentengPcs',
            'labaKotor',
            'expensesByCategory',
            'totalOperasional',
            'labaBersih'
        ));
    }
}
