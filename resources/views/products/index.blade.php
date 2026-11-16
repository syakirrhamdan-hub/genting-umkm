@extends('layouts.app')

@section('title', 'Katalog Genteng & Stok')
@section('page_title', 'Manajemen Produk Genteng & Stok Gudang')

@section('content')
<div class="container-fluid px-0">

    <!-- Header Actions & Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-2 align-items-center justify-content-between">
                <div class="col-md-8">
                    <form method="GET" action="{{ route('products.index') }}" class="row g-2">
                        <div class="col-12 col-sm-5">
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                                <input type="text" name="search" class="form-control" placeholder="Cari nama / kode genteng..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-6 col-sm-4">
                            <select name="category_id" class="form-select">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-sm-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                            @if(request()->anyFilled(['search', 'category_id', 'filter_stok']))
                                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary" title="Reset Filter"><i class="bi bi-x-lg"></i></a>
                            @endif
                        </div>
                    </form>
                </div>
                <div class="col-md-4 text-md-end mt-2 mt-md-0">
                    <a href="{{ route('products.create') }}" class="btn btn-terracotta d-inline-flex align-items-center gap-1">
                        <i class="bi bi-plus-circle"></i> Tambah Genteng Baru
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Genteng</th>
                        <th>Kategori</th>
                        <th class="text-end">Harga Beli (HPP)</th>
                        <th class="text-end">Harga Jual</th>
                        <th class="text-end">Margin Untung</th>
                        <th class="text-center">Stok Gudang</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $p)
                        <tr>
                            <td>
                                <span class="badge bg-light text-dark border font-monospace">{{ $p->kode_produk }}</span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $p->nama_produk }}</div>
                                @if($p->deskripsi)
                                    <small class="text-muted text-truncate d-block" style="max-width: 250px;">{{ $p->deskripsi }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary border">
                                    {{ $p->category ? $p->category->nama_kategori : 'Tanpa Kategori' }}
                                </span>
                            </td>
                            <td class="text-end text-muted">
                                Rp {{ number_format($p->harga_beli, 0, ',', '.') }}
                            </td>
                            <td class="text-end fw-bold text-dark">
                                Rp {{ number_format($p->harga_jual, 0, ',', '.') }}
                            </td>
                            <td class="text-end text-success fw-semibold">
                                + Rp {{ number_format($p->margin, 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                <span class="fw-bold fs-6">{{ number_format($p->stok, 0, ',', '.') }}</span>
                                <small class="text-muted">{{ $p->satuan }}</small>
                            </td>
                            <td class="text-center">
                                @if($p->stok <= 0)
                                    <span class="badge bg-danger">Habis</span>
                                @elseif($p->is_low_stock)
                                    <span class="badge bg-warning text-dark">Menipis (Min: {{ number_format($p->stok_minimum, 0, ',', '.') }})</span>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Aman</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <!-- Button Adjust Stock -->
                                    <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#adjustStockModal{{ $p->id }}" title="Sesuaikan Stok / Genteng Rusak">
                                        <i class="bi bi-arrow-left-right"></i> Stok
                                    </button>
                                    <a href="{{ route('products.edit', $p->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('products.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus genteng ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>

                                <!-- Modal Sesuaikan Stok -->
                                <div class="modal fade text-start" id="adjustStockModal{{ $p->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('products.adjust_stock', $p->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h6 class="modal-title fw-bold">Penyesuaian Stok Genteng</h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="alert alert-light border mb-3">
                                                        <div class="fw-bold">{{ $p->nama_produk }}</div>
                                                        <small class="text-muted">Stok saat ini: <strong>{{ number_format($p->stok, 0, ',', '.') }} {{ $p->satuan }}</strong></small>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Jenis Penyesuaian</label>
                                                        <select name="tipe_penyesuaian" class="form-select" required>
                                                            <option value="kurang">Kurang Stok (Genteng Pecah / Rusak / Hilang)</option>
                                                            <option value="tambah">Tambah Stok (Stok Tambahan / Retur)</option>
                                                            <option value="set">Set Ulang Total Stok Fisik (Stock Opname)</option>
                                                        </select>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Jumlah ({{ $p->satuan }})</label>
                                                        <input type="number" name="jumlah" class="form-control" min="1" placeholder="Masukkan jumlah..." required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Alasan Penyesuaian</label>
                                                        <input type="text" name="alasan" class="form-control" placeholder="Contoh: Pecah saat bongkar muat dari truk" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-warning fw-semibold">Simpan Penyesuaian</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-box-seam fs-1 d-block mb-2 text-secondary"></i>
                                Tidak ada data genteng ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $products->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
