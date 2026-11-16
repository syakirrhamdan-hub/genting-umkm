@extends('layouts.app')

@section('title', 'Tambah Genteng Baru')
@section('page_title', 'Tambah Master Genteng Baru')

@section('content')
<div class="container-fluid px-0" style="max-width: 900px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Genteng
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h6 class="mb-0 fw-bold"><i class="bi bi-plus-circle text-primary me-2"></i>Formulir Data Produk Genteng</h6>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('products.store') }}" method="POST">
                @csrf

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Kode Produk <span class="text-danger">*</span></label>
                        <input type="text" name="kode_produk" class="form-control @error('kode_produk') is-invalid @enderror" value="{{ old('kode_produk', $defaultKode) }}" required>
                        @error('kode_produk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Nama Genteng / Aksesoris <span class="text-danger">*</span></label>
                        <input type="text" name="nama_produk" class="form-control @error('nama_produk') is-invalid @enderror" value="{{ old('nama_produk') }}" placeholder="Contoh: Genteng Morando Glazur Natural" required>
                        @error('nama_produk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Kategori Genteng</label>
                        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Satuan Barang <span class="text-danger">*</span></label>
                        <select name="satuan" class="form-select" required>
                            <option value="pcs" {{ old('satuan') == 'pcs' ? 'selected' : '' }}>pcs (biji / buah)</option>
                            <option value="kodi" {{ old('satuan') == 'kodi' ? 'selected' : '' }}>kodi (20 pcs)</option>
                            <option value="m2" {{ old('satuan') == 'm2' ? 'selected' : '' }}>m² (meter persegi)</option>
                            <option value="box" {{ old('satuan') == 'box' ? 'selected' : '' }}>box / dus</option>
                        </select>
                    </div>
                </div>

                <!-- Harga & Margin Calculator -->
                <div class="card bg-light border p-3 mb-3">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Harga Beli / HPP (Rp) <span class="text-danger">*</span></label>
                            <input type="number" id="harga_beli" name="harga_beli" class="form-control @error('harga_beli') is-invalid @enderror" value="{{ old('harga_beli', 0) }}" min="0" required>
                            <small class="text-muted">Harga kulakan ke pabrik/pengrajin</small>
                            @error('harga_beli') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Harga Jual (Rp) <span class="text-danger">*</span></label>
                            <input type="number" id="harga_jual" name="harga_jual" class="form-control @error('harga_jual') is-invalid @enderror" value="{{ old('harga_jual', 0) }}" min="0" required>
                            <small class="text-muted">Harga jual retail/proyek per biji</small>
                            @error('harga_jual') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <div class="p-2 rounded bg-white border text-center">
                                <span class="text-muted small text-uppercase fw-semibold d-block">Estimasi Keuntungan</span>
                                <h5 class="mb-0 fw-bold text-success" id="marginDisplay">Rp 0</h5>
                                <small class="text-muted" id="marginPct">0%</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Stok Awal <span class="text-danger">*</span></label>
                        <input type="number" name="stok" class="form-control @error('stok') is-invalid @enderror" value="{{ old('stok', 0) }}" min="0" required>
                        <small class="text-muted">Jumlah genteng yang ada di gudang saat ini</small>
                        @error('stok') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Batas Minimum Stok <span class="text-danger">*</span></label>
                        <input type="number" name="stok_minimum" class="form-control @error('stok_minimum') is-invalid @enderror" value="{{ old('stok_minimum', 500) }}" min="0" required>
                        <small class="text-muted">Peringatan akan muncul jika stok di bawah angka ini</small>
                        @error('stok_minimum') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Deskripsi / Catatan Produk</label>
                    <textarea name="deskripsi" class="form-control" rows="3" placeholder="Informasi ukuran, spesifikasi tanah liat/beton, produsen...">{{ old('deskripsi') }}</textarea>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('products.index') }}" class="btn btn-light border">Batal</a>
                    <button type="submit" class="btn btn-terracotta px-4 fw-semibold">Simpan Genteng</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function updateMargin() {
        const beli = parseFloat(document.getElementById('harga_beli').value) || 0;
        const jual = parseFloat(document.getElementById('harga_jual').value) || 0;
        const margin = jual - beli;
        const pct = beli > 0 ? ((margin / beli) * 100).toFixed(1) : 0;

        document.getElementById('marginDisplay').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(margin);
        document.getElementById('marginPct').innerText = pct + '% margin keuntungan';
        
        if (margin < 0) {
            document.getElementById('marginDisplay').className = 'mb-0 fw-bold text-danger';
        } else {
            document.getElementById('marginDisplay').className = 'mb-0 fw-bold text-success';
        }
    }

    document.getElementById('harga_beli').addEventListener('input', updateMargin);
    document.getElementById('harga_jual').addEventListener('input', updateMargin);
    updateMargin();
</script>
@endsection
