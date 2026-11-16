@extends('layouts.app')

@section('title', 'Edit Pelanggan: ' . $customer->nama)
@section('page_title', 'Perbarui Data Pelanggan')

@section('content')
<div class="container-fluid px-0" style="max-width: 800px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Pelanggan
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h6 class="mb-0 fw-bold"><i class="bi bi-pencil-square text-primary me-2"></i>Ubah Data Pelanggan: {{ $customer->nama }}</h6>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('customers.update', $customer->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Kode Pelanggan <span class="text-danger">*</span></label>
                        <input type="text" name="kode_pelanggan" class="form-control @error('kode_pelanggan') is-invalid @enderror" value="{{ old('kode_pelanggan', $customer->kode_pelanggan) }}" required>
                        @error('kode_pelanggan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Nama Lengkap / Instansi <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $customer->nama) }}" required>
                        @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nomor WhatsApp / HP</label>
                    <input type="text" name="no_hp" class="form-control @error('no_hp') is-invalid @enderror" value="{{ old('no_hp', $customer->no_hp) }}">
                    @error('no_hp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Alamat Rumah / Alamat Kirim Proyek</label>
                    <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3">{{ old('alamat', $customer->alamat) }}</textarea>
                    @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Catatan Tambahan</label>
                    <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $customer->keterangan) }}</textarea>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('customers.index') }}" class="btn btn-light border">Batal</a>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">Perbarui Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
