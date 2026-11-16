@extends('layouts.app')

@section('title', 'Kategori Genteng')
@section('page_title', 'Master Kategori Genteng')

@section('content')
<div class="container-fluid px-0">

    <div class="row g-4">
        <!-- Form Tambah Kategori -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-plus-circle text-primary me-2"></i>Tambah Kategori Baru</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('categories.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" name="nama_kategori" class="form-control" placeholder="Contoh: Genteng Flat Beton" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="3" placeholder="Keterangan kategori..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-terracotta w-100 fw-semibold">Simpan Kategori</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabel Daftar Kategori -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-tags text-primary me-2"></i>Daftar Kategori Genteng</h6>
                    <span class="badge bg-primary rounded-pill">{{ $categories->count() }} Kategori</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Nama Kategori</th>
                                <th>Deskripsi</th>
                                <th class="text-center">Jumlah Produk</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $cat)
                                <tr>
                                    <td class="fw-bold text-dark">
                                        {{ $cat->nama_kategori }}
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $cat->deskripsi ?: '-' }}</small>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('products.index', ['category_id' => $cat->id]) }}" class="badge bg-info-subtle text-info border text-decoration-none px-2 py-1">
                                            {{ $cat->products_count }} Produk
                                        </a>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editCatModal{{ $cat->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('categories.destroy', $cat->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kategori ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" {{ $cat->products_count > 0 ? 'disabled title="Masih memiliki produk"' : '' }}>
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>

                                        <!-- Modal Edit Kategori -->
                                        <div class="modal fade text-start" id="editCatModal{{ $cat->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="{{ route('categories.update', $cat->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-header">
                                                            <h6 class="modal-title fw-bold">Edit Kategori</h6>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Nama Kategori</label>
                                                                <input type="text" name="nama_kategori" class="form-control" value="{{ $cat->nama_kategori }}" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Deskripsi</label>
                                                                <textarea name="deskripsi" class="form-control" rows="3">{{ $cat->deskripsi }}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-primary fw-semibold">Simpan Perubahan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Belum ada kategori genteng.</td>
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
