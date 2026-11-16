@extends('layouts.app')

@section('title', 'Laporan Laba Rugi')
@section('page_title', 'Laporan Laba Rugi Usaha Genteng')

@section('content')
<div class="container-fluid px-0">

    <!-- Filter Periode -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.profit_loss') }}" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <label class="form-label small text-muted mb-1">Periode Dari</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted mb-1">Sampai Dengan</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">Tampilkan Laporan</button>
                    <a href="{{ route('reports.profit_loss') }}" class="btn btn-outline-secondary" title="Reset"><i class="bi bi-x-lg"></i></a>
                    <button type="button" onclick="window.print()" class="btn btn-outline-dark"><i class="bi bi-printer"></i></button>
                </div>
            </form>
        </div>
    </div>

    <!-- Overview Laba Bersih Banner -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card bg-white border-0 shadow-sm p-3">
                <span class="text-muted small text-uppercase fw-semibold">Penjualan Bersih</span>
                <h4 class="fw-bold mb-1 text-dark">Rp {{ number_format($penjualanBersih, 0, ',', '.') }}</h4>
                <small class="text-muted">{{ number_format($totalGentengPcs, 0, ',', '.') }} pcs genteng terjual</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-white border-0 shadow-sm p-3">
                <span class="text-muted small text-uppercase fw-semibold">Laba Kotor</span>
                <h4 class="fw-bold mb-1 {{ $labaKotor >= 0 ? 'text-success' : 'text-danger' }}">
                    Rp {{ number_format($labaKotor, 0, ',', '.') }}
                </h4>
                <small class="text-muted">Setelah dikurangi HPP Genteng</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-white border-0 shadow-sm p-3">
                <span class="text-muted small text-uppercase fw-semibold">Laba Bersih Akhir</span>
                <h4 class="fw-bold mb-1 {{ $labaBersih >= 0 ? 'text-primary' : 'text-danger' }}">
                    Rp {{ number_format($labaBersih, 0, ',', '.') }}
                </h4>
                <small class="text-muted">Laba kotor + ongkir - operasional</small>
            </div>
        </div>
    </div>

    <!-- Rincian Laporan Laba Rugi -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <div>
                <h5 class="mb-0 fw-bold">Laporan Laba & Rugi</h5>
                <small class="text-muted">Periode {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</small>
            </div>
            <span class="badge {{ $labaBersih >= 0 ? 'bg-success' : 'bg-danger' }} px-3 py-2 fs-6">
                {{ $labaBersih >= 0 ? 'Surplus / Untung' : 'Defisit / Rugi' }}
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Komponen Keuangan</th>
                            <th class="text-end" style="width: 250px;">Nominal (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Bagian 1: Pendapatan -->
                        <tr class="table-light">
                            <th colspan="2" class="text-primary text-uppercase">1. PENDAPATAN PENJUALAN GENTENG</th>
                        </tr>
                        <tr>
                            <td class="ps-4">Total Penjualan Genteng & Aksesoris</td>
                            <td class="text-end">Rp {{ number_format($totalPenjualanBarang, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="ps-4 text-muted">Potongan Diskon Penjualan</td>
                            <td class="text-end text-danger">- Rp {{ number_format($totalDiskon, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="fw-semibold">
                            <td class="ps-4">Total Penjualan Bersih</td>
                            <td class="text-end">Rp {{ number_format($penjualanBersih, 0, ',', '.') }}</td>
                        </tr>

                        <!-- Bagian 2: HPP -->
                        <tr class="table-light">
                            <th colspan="2" class="text-primary text-uppercase">2. HARGA POKOK PENJUALAN (HPP)</th>
                        </tr>
                        <tr>
                            <td class="ps-4">
                                Total HPP Genteng Terjual
                                <small class="text-muted d-block">Biaya modal beli/kulakan genteng dari {{ number_format($totalGentengPcs, 0, ',', '.') }} pcs terjual</small>
                            </td>
                            <td class="text-end text-danger">- Rp {{ number_format($totalHpp, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="fw-bold table-info">
                            <td>LABA KOTOR (GROSS PROFIT)</td>
                            <td class="text-end {{ $labaKotor >= 0 ? 'text-dark' : 'text-danger' }}">
                                Rp {{ number_format($labaKotor, 0, ',', '.') }}
                            </td>
                        </tr>

                        <!-- Bagian 3: Pendapatan Lain / Ongkir -->
                        <tr class="table-light">
                            <th colspan="2" class="text-primary text-uppercase">3. PENDAPATAN LAINNYA</th>
                        </tr>
                        <tr>
                            <td class="ps-4">Penerimaan Ongkos Kirim Armada Truk</td>
                            <td class="text-end text-success">+ Rp {{ number_format($totalOngkirDiterima, 0, ',', '.') }}</td>
                        </tr>

                        <!-- Bagian 4: Beban Operasional / Pengeluaran Toko -->
                        <tr class="table-light">
                            <th colspan="2" class="text-danger text-uppercase">4. BEBAN PENGELUARAN & OPERASIONAL</th>
                        </tr>
                        @forelse($expensesByCategory as $catName => $nom)
                            <tr>
                                <td class="ps-4">
                                    {{ $catName }}
                                </td>
                                <td class="text-end text-danger">- Rp {{ number_format($nom, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="ps-4 text-muted fst-italic">Tidak ada catatan beban operasional pada periode ini</td>
                                <td class="text-end text-muted">Rp 0</td>
                            </tr>
                        @endforelse
                        <tr class="fw-semibold">
                            <td class="ps-4">Total Beban Operasional</td>
                            <td class="text-end text-danger">- Rp {{ number_format($totalOperasional, 0, ',', '.') }}</td>
                        </tr>

                        <!-- Bagian 5: Laba Bersih Akhir -->
                        <tr class="table-dark fs-5 fw-bold">
                            <td>LABA BERSIH USAHA (NET PROFIT)</td>
                            <td class="text-end {{ $labaBersih >= 0 ? 'text-success' : 'text-danger' }}">
                                Rp {{ number_format($labaBersih, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
