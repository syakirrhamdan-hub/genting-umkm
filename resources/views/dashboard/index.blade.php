@extends('layouts.app')

@section('title', 'Dashboard Toko Genting')
@section('page_title', 'Ringkasan & Dashboard Finansial')

@section('content')
<div class="container-fluid px-0">

    <!-- Top Summary Cards -->
    <div class="row g-3 mb-4">
        <!-- Kas Masuk Bulan Ini -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card metric-card shadow-sm border-0 h-100 bg-white">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Pemasukan (Bulan Ini)</span>
                        <h4 class="mb-0 fw-bold text-success mt-1">Rp {{ number_format($kasMasukBulanIni, 0, ',', '.') }}</h4>
                        <small class="text-muted"><i class="bi bi-arrow-up-right text-success"></i> Dari penjualan & piutang</small>
                    </div>
                    <div class="icon-bubble bg-success-subtle text-success">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kas Keluar Bulan Ini -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card metric-card shadow-sm border-0 h-100 bg-white">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Pengeluaran (Bulan Ini)</span>
                        <h4 class="mb-0 fw-bold text-danger mt-1">Rp {{ number_format($kasKeluarBulanIni, 0, ',', '.') }}</h4>
                        <small class="text-muted"><i class="bi bi-arrow-down-right text-danger"></i> Kulakan, armada, operasional</small>
                    </div>
                    <div class="icon-bubble bg-danger-subtle text-danger">
                        <i class="bi bi-wallet2"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Saldo Kas Toko -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card metric-card shadow-sm border-0 h-100 bg-white">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Saldo Kas Saat Ini</span>
                        <h4 class="mb-0 fw-bold text-primary mt-1">Rp {{ number_format($saldoKas, 0, ',', '.') }}</h4>
                        <small class="text-muted"><i class="bi bi-safe text-primary"></i> Kas fisik & rekening toko</small>
                    </div>
                    <div class="icon-bubble bg-primary-subtle text-primary">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sisa Piutang Pelanggan -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card metric-card shadow-sm border-0 h-100 bg-white">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Total Piutang Belum Lunas</span>
                        <h4 class="mb-0 fw-bold text-warning-emphasis mt-1">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</h4>
                        <small class="text-muted"><i class="bi bi-clock-history text-warning"></i> {{ $countPiutang }} transaksi tempo / DP</small>
                    </div>
                    <div class="icon-bubble bg-warning-subtle text-warning-emphasis">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Banner Quick Action / Status -->
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="bi bi-bar-chart-line-fill text-primary me-2"></i>Tren Arus Kas (6 Bulan Terakhir)</span>
                    <a href="{{ route('cashflows.index') }}" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 0.75rem;">Lihat Buku Kas</a>
                </div>
                <div class="card-body">
                    <canvas id="cashflowChart" height="110"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Warning Stok Genteng Menipis -->
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i>Peringatan Stok Menipis</span>
                    <span class="badge bg-danger rounded-pill">{{ $lowStockProducts->count() }} Produk</span>
                </div>
                <div class="card-body p-0">
                    @if($lowStockProducts->isEmpty())
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-check-circle-fill text-success fs-1 mb-2 d-block"></i>
                            <div class="fw-semibold">Semua Stok Genteng Aman!</div>
                            <small>Tidak ada genteng di bawah batas minimum stok.</small>
                        </div>
                    @else
                        <div class="list-group list-group-flush" style="max-height: 280px; overflow-y: auto;">
                            @foreach($lowStockProducts as $p)
                                <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.88rem;">{{ $p->nama_produk }}</div>
                                        <small class="text-muted">{{ $p->kode_produk }} &bull; Min: {{ number_format($p->stok_minimum, 0, ',', '.') }} {{ $p->satuan }}</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-danger px-2 py-1">Sisa {{ number_format($p->stok, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-light p-2 text-center">
                    <a href="{{ route('products.index', ['filter_stok' => 'menipis']) }}" class="small fw-semibold text-decoration-none">
                        Kelola Semua Stok Genteng &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Sales and Expenses -->
    <div class="row g-3">
        <!-- 5 Transaksi Penjualan Terbaru -->
        <div class="col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="bi bi-receipt text-success me-2"></i>Penjualan Genteng Terbaru</span>
                    <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-outline-success py-0 px-2" style="font-size: 0.75rem;">Semua Penjualan</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No Faktur</th>
                                <th>Pelanggan</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTransactions as $t)
                                <tr>
                                    <td>
                                        <a href="{{ route('transactions.show', $t->id) }}" class="fw-bold text-decoration-none text-dark">
                                            {{ $t->no_faktur }}
                                        </a>
                                        <div class="text-muted" style="font-size: 0.75rem;">{{ \Carbon\Carbon::parse($t->tanggal)->format('d M Y') }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-truncate" style="max-width: 180px;">{{ $t->nama_pelanggan }}</div>
                                        <small class="text-muted">{{ $t->details->count() }} item genteng</small>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">Rp {{ number_format($t->grand_total, 0, ',', '.') }}</div>
                                        @if($t->sisa_piutang > 0)
                                            <small class="text-danger">Sisa: Rp {{ number_format($t->sisa_piutang, 0, ',', '.') }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($t->status_pembayaran === 'lunas')
                                            <span class="badge badge-lunas px-2 py-1">Lunas</span>
                                        @elseif($t->status_pembayaran === 'tempo')
                                            <span class="badge badge-tempo px-2 py-1">Tempo / DP</span>
                                        @else
                                            <span class="badge badge-belum px-2 py-1">Belum Lunas</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('transactions.show', $t->id) }}" class="btn btn-sm btn-light border py-1 px-2" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('transactions.invoice', $t->id) }}" target="_blank" class="btn btn-sm btn-light border py-1 px-2" title="Cetak Nota">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada transaksi penjualan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 5 Pengeluaran Terbaru -->
        <div class="col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="bi bi-wallet2 text-danger me-2"></i>Pengeluaran Toko Terbaru</span>
                    <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-outline-danger py-0 px-2" style="font-size: 0.75rem;">Semua Pengeluaran</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Keperluan</th>
                                <th>Kategori</th>
                                <th class="text-end">Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentExpenses as $e)
                                <tr>
                                    <td>
                                        <div class="fw-semibold text-truncate" style="max-width: 170px;" title="{{ $e->nama_keperluan }}">{{ $e->nama_keperluan }}</div>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($e->tanggal)->format('d M Y') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                                            {{ $e->category ? $e->category->nama_kategori : 'Umum' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <span class="fw-bold text-danger">Rp {{ number_format($e->nominal, 0, ',', '.') }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">Belum ada catatan pengeluaran.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    // Inisialisasi Chart.js Arus Kas
    const ctx = document.getElementById('cashflowChart').getContext('2d');
    const cashflowChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [
                {
                    label: 'Pemasukan Kas',
                    data: {!! json_encode($chartMasuk) !!},
                    backgroundColor: 'rgba(34, 197, 94, 0.85)',
                    borderRadius: 6,
                },
                {
                    label: 'Pengeluaran Kas',
                    data: {!! json_encode($chartKeluar) !!},
                    backgroundColor: 'rgba(239, 68, 68, 0.85)',
                    borderRadius: 6,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        boxWidth: 12,
                        font: { family: 'Plus Jakarta Sans', size: 12 }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let val = context.parsed.y || 0;
                            return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(val);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + (value >= 1000000 ? (value / 1000000) + ' Jt' : (value / 1000) + ' Rb');
                        },
                        font: { family: 'Plus Jakarta Sans', size: 11 }
                    }
                },
                x: {
                    ticks: {
                        font: { family: 'Plus Jakarta Sans', size: 11 }
                    }
                }
            }
        }
    });
</script>
@endsection
