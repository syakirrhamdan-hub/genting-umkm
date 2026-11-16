@extends('layouts.app')

@section('title', 'Edit Pemasok: ' . $supplier->nama_supplier)
@section('page_title', 'Perbarui Data Pemasok Genteng')

@section('content')
<div class="container-fluid px-0" style="max-width: 800px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Pemasok
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h6 class="mb-0 fw-bold"><i class="bi bi-pencil-square text-primary me-2"></i>Ubah Data: {{ $supplier->nama_supplier }}</h6>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('suppliers.update', $supplier->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Kode Pemasok <span class="text-danger">*</span></label>
                        <input type="text" name="kode_supplier" class="form-control @error('kode_supplier') is-invalid @enderror" value="{{ old('kode_supplier', $supplier->kode_supplier) }}" required>
                        @error('kode_supplier') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Nama Pabrik / Usaha Genteng <span class="text-danger">*</span></label>
                        <input type="text" name="nama_supplier" class="form-control @error('nama_supplier') is-invalid @enderror" value="{{ old('nama_supplier', $supplier->nama_supplier) }}" required>
                        @error('nama_supplier') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nama Kontak Person (PIC)</label>
                        <input type="text" name="nama_kontak" class="form-control" value="{{ old('nama_kontak', $supplier->nama_kontak) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nomor WhatsApp / HP</label>
                        <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp', $supplier->no_hp) }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Alamat Pabrik / Sentra Industri</label>
                    <textarea name="alamat" class="form-control" rows="3">{{ old('alamat', $supplier->alamat) }}</textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Keterangan / Spesialisasi</label>
                    <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $supplier->keterangan) }}</textarea>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('suppliers.index') }}" class="btn btn-light border">Batal</a>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">Perbarui Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
