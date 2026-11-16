<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Expense;
use App\Models\Cashflow;
use App\Models\Customer;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth()->toDateString();
        $endOfMonth = $now->copy()->endOfMonth()->toDateString();
        $today = $now->toDateString();

        // 1. Metrik Kas Masuk & Keluar Bulan Ini
        $kasMasukBulanIni = Cashflow::where('jenis', 'masuk')
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->sum('nominal');

        $kasKeluarBulanIni = Cashflow::where('jenis', 'keluar')
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->sum('nominal');

        // 2. Total Saldo Kas (All Time)
        $totalMasuk = Cashflow::where('jenis', 'masuk')->sum('nominal');
        $totalKeluar = Cashflow::where('jenis', 'keluar')->sum('nominal');
        $saldoKas = $totalMasuk - $totalKeluar;

        // 3. Penjualan & Piutang
        $penjualanHariIni = Transaction::whereDate('tanggal', $today)->sum('grand_total');
        $totalPiutang = Transaction::where('status_pembayaran', '!=', 'lunas')->sum('sisa_piutang');
        $countPiutang = Transaction::where('status_pembayaran', '!=', 'lunas')->count();

        // 4. Stok Genteng Menipis
        $lowStockProducts = Product::with('category')
            ->whereRaw('stok <= stok_minimum')
            ->orderBy('stok', 'asc')
            ->get();

        // 5. Total Genteng Terjual Bulan Ini
        $totalGentengTerjualBulanIni = \DB::table('transaction_details')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->whereBetween('transactions.tanggal', [$startOfMonth, $endOfMonth])
            ->sum('transaction_details.jumlah');

        // 6. Transaksi Terbaru & Pengeluaran Terbaru
        $recentTransactions = Transaction::with(['customer', 'details'])
            ->latest()
            ->take(5)
            ->get();

        $recentExpenses = Expense::with(['category', 'supplier'])
            ->latest()
            ->take(5)
            ->get();

        // 7. Grafik 6 Bulan Terakhir
        $chartLabels = [];
        $chartMasuk = [];
        $chartKeluar = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $mStart = $month->copy()->startOfMonth()->toDateString();
            $mEnd = $month->copy()->endOfMonth()->toDateString();
            $chartLabels[] = $month->translatedFormat('F Y');

            $chartMasuk[] = Cashflow::where('jenis', 'masuk')
                ->whereBetween('tanggal', [$mStart, $mEnd])
                ->sum('nominal');

            $chartKeluar[] = Cashflow::where('jenis', 'keluar')
                ->whereBetween('tanggal', [$mStart, $mEnd])
                ->sum('nominal');
        }

        return view('dashboard.index', compact(
            'kasMasukBulanIni',
            'kasKeluarBulanIni',
            'saldoKas',
            'penjualanHariIni',
            'totalPiutang',
            'countPiutang',
            'lowStockProducts',
            'totalGentengTerjualBulanIni',
            'recentTransactions',
            'recentExpenses',
            'chartLabels',
            'chartMasuk',
            'chartKeluar'
        ));
    }
}
