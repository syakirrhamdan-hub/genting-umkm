@extends('layouts.app')

@section('title', 'Pengeluaran Toko')
@section('page_title', 'Biaya Operasional & Kas Keluar')

@section('content')
<div class="container-fluid px-0">

    <!-- Ringkasan Metrik Pengeluaran -->
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="card bg-white border-0 shadow-sm p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small text-uppercase fw-semibold">Total Pengeluaran</span>
                    <div class="rounded-circle bg-danger-subtle text-danger p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="bi bi-wallet2 fs-6"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-1 text-danger">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h4>
                <small class="text-muted">Total biaya keluar pada data terpilih</small>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card bg-white border-0 shadow-sm p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small text-uppercase fw-semibold">Jumlah Catatan</span>
                    <div class="rounded-circle bg-warning-subtle text-warning p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="bi bi-receipt-cutoff fs-6"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-1 text-dark">{{ number_format($expenses->total(), 0, ',', '.') }} <span class="fs-6 fw-normal text-muted">transaksi</span></h4>
                <small class="text-muted">Item pengeluaran terdata di sistem</small>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card bg-white border-0 shadow-sm p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small text-uppercase fw-semibold">Kelola Kategori</span>
                    <div class="rounded-circle bg-primary-subtle text-primary p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="bi bi-tags-fill fs-6"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-1 text-dark">{{ $categories->count() }} <span class="fs-6 fw-normal text-muted">kategori</span></h4>
                <small>
                    <a href="{{ route('expense-categories.index') }}" class="text-decoration-none text-primary fw-semibold">
                        Atur Kategori Pengeluaran <i class="bi bi-arrow-right"></i>
                    </a>
                </small>
            </div>
        </div>
    </div>

    <!-- Filter & Tombol Aksi -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('expenses.index') }}" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="No exp / nama keperluan..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="category_id" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" title="Dari Tanggal">
                </div>
                <div class="col-md-2">
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" title="Sampai Tanggal">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                    @if(request()->anyFilled(['search', 'category_id', 'start_date', 'end_date']))
                        <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary" title="Reset"><i class="bi bi-x-lg"></i></a>
                    @endif
                    <a href="{{ route('expenses.create') }}" class="btn btn-danger d-inline-flex align-items-center gap-1 text-nowrap">
                        <i class="bi bi-plus-circle"></i> Catat Biaya
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Pengeluaran -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold"><i class="bi bi-table text-danger me-2"></i>Daftar Pengeluaran & Biaya Operasional</h6>
            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2">
                Total: Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>No Bukti & Tanggal</th>
                        <th>Kategori</th>
                        <th>Keperluan / Uraian</th>
                        <th>Pemasok / Rekanan</th>
                        <th class="text-center">Pembayaran</th>
                        <th class="text-end">Nominal (Rp)</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $exp)
                        <tr>
                            <td>
                                <span class="badge bg-light text-dark border font-monospace d-block mb-1" style="width: fit-content;">
                                    {{ $exp->no_pengeluaran }}
                                </span>
                                <small class="text-muted">
                                    <i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($exp->tanggal)->translatedFormat('d M Y') }}
                                </small>
                            </td>
                            <td>
                                @if($exp->category)
                                    <span class="badge bg-secondary-subtle text-secondary border">
                                        {{ $exp->category->nama_kategori }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border">Tanpa Kategori</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $exp->nama_keperluan }}</div>
                                @if($exp->catatan)
                                    <small class="text-muted d-block text-truncate" style="max-width: 300px;">
                                        <i class="bi bi-info-circle me-1"></i>{{ $exp->catatan }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                @if($exp->supplier)
                                    <span class="fw-semibold text-dark"><i class="bi bi-truck me-1 text-muted"></i>{{ $exp->supplier->nama_supplier }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($exp->metode_pembayaran == 'transfer')
                                    <span class="badge bg-info-subtle text-info border">Transfer</span>
                                @else
                                    <span class="badge bg-success-subtle text-success border">Tunai</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <span class="fw-bold text-danger fs-6">
                                    Rp {{ number_format($exp->nominal, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#detailModal{{ $exp->id }}" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <a href="{{ route('expenses.edit', $exp->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('expenses.destroy', $exp->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus catatan pengeluaran {{ $exp->no_pengeluaran }}? Saldo buku kas akan otomatis disesuaikan.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>

                                <!-- Modal Detail Pengeluaran -->
                                <div class="modal fade text-start" id="detailModal{{ $exp->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header bg-light">
                                                <h6 class="modal-title fw-bold">
                                                    <i class="bi bi-receipt text-danger me-2"></i>Rincian Pengeluaran {{ $exp->no_pengeluaran }}
                                                </h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="text-center mb-4 pb-3 border-bottom">
                                                    <small class="text-muted text-uppercase fw-semibold">Nominal Pengeluaran</small>
                                                    <h3 class="fw-bold text-danger mb-0 mt-1">Rp {{ number_format($exp->nominal, 0, ',', '.') }}</h3>
                                                    <span class="badge {{ $exp->metode_pembayaran == 'transfer' ? 'bg-info-subtle text-info' : 'bg-success-subtle text-success' }} border mt-2">
                                                        Metode: {{ ucfirst($exp->metode_pembayaran) }}
                                                    </span>
                                                </div>

                                                <table class="table table-sm table-borderless mb-0">
                                                    <tr>
                                                        <td class="text-muted" style="width: 40%;">No Bukti:</td>
                                                        <td class="fw-bold font-monospace">{{ $exp->no_pengeluaran }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted">Tanggal:</td>
                                                        <td class="fw-semibold">{{ \Carbon\Carbon::parse($exp->tanggal)->translatedFormat('d F Y') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted">Kategori:</td>
                                                        <td>
                                                            <span class="badge bg-secondary-subtle text-secondary border">
                                                                {{ $exp->category ? $exp->category->nama_kategori : 'Tanpa Kategori' }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted">Keperluan:</td>
                                                        <td class="fw-bold text-dark">{{ $exp->nama_keperluan }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted">Pemasok / Rekanan:</td>
                                                        <td>{{ $exp->supplier ? $exp->supplier->nama_supplier : '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted">Catatan:</td>
                                                        <td>{{ $exp->catatan ?: '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted">Tercatat di Kas:</td>
                                                        <td><span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Otomatis Tercatat (Kas Keluar)</span></td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <a href="{{ route('expenses.edit', $exp->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-pencil me-1"></i>Edit Data
                                                </a>
                                                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                    <p class="mb-1 fw-semibold">Belum ada data pengeluaran yang tercatat.</p>
                                    <small>Gunakan tombol "Catat Biaya" di atas untuk mencatat pengeluaran operasional toko.</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($expenses->hasPages())
            <div class="card-footer bg-white py-3">
                <div class="d-flex justify-content-center">
                    {{ $expenses->links() }}
                </div>
            </div>
        @endif
    </div>

</div>
@endsection
