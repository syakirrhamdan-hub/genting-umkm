@extends('layouts.app')

@section('title', 'Buku Kas Toko')
@section('page_title', 'Buku Kas & Riwayat Arus Kas (Cash Flow)')

@section('content')
<div class="container-fluid px-0">

    <!-- Summary Metrics Saldo & Mutasi -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card bg-white border-0 shadow-sm p-3">
                <span class="text-muted small text-uppercase fw-semibold">Saldo Awal Periode</span>
                <h4 class="fw-bold mb-1 {{ $saldoAwal < 0 ? 'text-danger' : 'text-dark' }}">
                    Rp {{ number_format($saldoAwal, 0, ',', '.') }}
                </h4>
                <small class="text-muted">Sebelum {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</small>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card bg-white border-0 shadow-sm p-3">
                <span class="text-muted small text-uppercase fw-semibold">Total Pemasukan (Masuk)</span>
                <h4 class="fw-bold mb-1 text-success">+ Rp {{ number_format($totalMasukPeriode, 0, ',', '.') }}</h4>
                <small class="text-muted">Penjualan & kas masuk</small>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card bg-white border-0 shadow-sm p-3">
                <span class="text-muted small text-uppercase fw-semibold">Total Pengeluaran (Keluar)</span>
                <h4 class="fw-bold mb-1 text-danger">- Rp {{ number_format($totalKeluarPeriode, 0, ',', '.') }}</h4>
                <small class="text-muted">Biaya & kas keluar</small>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card bg-white border-0 shadow-sm p-3">
                <span class="text-muted small text-uppercase fw-semibold">Saldo Kas Akhir</span>
                <h4 class="fw-bold mb-1 {{ $saldoAkhir < 0 ? 'text-danger' : 'text-primary' }}">
                    Rp {{ number_format($saldoAkhir, 0, ',', '.') }}
                </h4>
                <small class="text-muted">Posisi kas per {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</small>
            </div>
        </div>
    </div>

    <!-- Filter Periode & Jenis -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('cashflows.index') }}" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Jenis Arus Kas</label>
                    <select name="jenis" class="form-select">
                        <option value="">Semua (Masuk & Keluar)</option>
                        <option value="masuk" {{ request('jenis') == 'masuk' ? 'selected' : '' }}>Hanya Kas Masuk</option>
                        <option value="keluar" {{ request('jenis') == 'keluar' ? 'selected' : '' }}>Hanya Kas Keluar</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                    <a href="{{ route('cashflows.index') }}" class="btn btn-outline-secondary" title="Reset"><i class="bi bi-x-lg"></i></a>
                    <a href="{{ route('reports.profit_loss') }}" class="btn btn-outline-success text-nowrap" title="Lihat Laba Rugi">
                        <i class="bi bi-graph-up me-1"></i>Laba Rugi
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Buku Kas -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h6 class="mb-0 fw-bold">
                <i class="bi bi-journal-check text-primary me-2"></i>Buku Mutasi Kas ({{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }})
            </h6>
            <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-printer me-1"></i>Cetak Laporan
            </button>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 130px;">Tanggal</th>
                        <th>Kategori & Keterangan</th>
                        <th class="text-center" style="width: 120px;">Jenis</th>
                        <th class="text-end" style="width: 170px;">Pemasukan (Masuk)</th>
                        <th class="text-end" style="width: 170px;">Pengeluaran (Keluar)</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Saldo Awal Baris -->
                    <tr class="table-light">
                        <td class="fw-bold font-monospace text-muted">{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</td>
                        <td colspan="2">
                            <span class="fw-bold text-dark"><i class="bi bi-arrow-return-right me-1"></i>SALDO AWAL KAS</span>
                        </td>
                        <td class="text-end fw-bold text-muted" colspan="2">
                            Rp {{ number_format($saldoAwal, 0, ',', '.') }}
                        </td>
                    </tr>

                    @forelse($cashflows as $cf)
                        <tr>
                            <td>
                                <span class="text-dark font-monospace small">
                                    {{ \Carbon\Carbon::parse($cf->tanggal)->format('d/m/Y') }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $cf->kategori }}</div>
                                <small class="text-muted d-block">{{ $cf->keterangan }}</small>
                            </td>
                            <td class="text-center">
                                @if($cf->jenis == 'masuk')
                                    <span class="badge bg-success-subtle text-success border px-2 py-1">
                                        <i class="bi bi-arrow-down-left me-1"></i>Masuk
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border px-2 py-1">
                                        <i class="bi bi-arrow-up-right me-1"></i>Keluar
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($cf->jenis == 'masuk')
                                    <span class="fw-bold text-success">+ Rp {{ number_format($cf->nominal, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($cf->jenis == 'keluar')
                                    <span class="fw-bold text-danger">- Rp {{ number_format($cf->nominal, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                Tidak ada mutasi kas pada rentang tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="3" class="text-end text-uppercase">Total Mutasi Periode:</td>
                        <td class="text-end text-success">+ Rp {{ number_format($totalMasukPeriode, 0, ',', '.') }}</td>
                        <td class="text-end text-danger">- Rp {{ number_format($totalKeluarPeriode, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="table-secondary">
                        <td colspan="3" class="text-end text-uppercase fs-6">Saldo Kas Akhir:</td>
                        <td colspan="2" class="text-end fs-5 {{ $saldoAkhir < 0 ? 'text-danger' : 'text-primary' }}">
                            Rp {{ number_format($saldoAkhir, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>
@endsection
