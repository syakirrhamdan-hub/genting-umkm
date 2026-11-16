@extends('layouts.app')

@section('title', 'Edit Pengeluaran ' . $expense->no_pengeluaran)
@section('page_title', 'Perbarui Data Pengeluaran')

@section('content')
<div class="container-fluid px-0">

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="bi bi-pencil-square me-2"></i>Edit Pengeluaran {{ $expense->no_pengeluaran }}
                    </h5>
                    <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('expenses.update', $expense->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <!-- No Pengeluaran & Tanggal -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">No Bukti Pengeluaran <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-hash"></i></span>
                                    <input type="text" name="no_pengeluaran" class="form-control font-monospace @error('no_pengeluaran') is-invalid @enderror" value="{{ old('no_pengeluaran', $expense->no_pengeluaran) }}" required>
                                </div>
                                @error('no_pengeluaran')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal Transaksi <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-calendar3"></i></span>
                                    <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', \Carbon\Carbon::parse($expense->tanggal)->format('Y-m-d')) }}" required>
                                </div>
                                @error('tanggal')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Kategori Pengeluaran & Rekanan/Supplier -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold d-flex justify-content-between">
                                    <span>Kategori Pengeluaran <span class="text-danger">*</span></span>
                                    <a href="{{ route('expense-categories.index') }}" target="_blank" class="text-decoration-none small text-primary">
                                        + Kelola Kategori
                                    </a>
                                </label>
                                <select name="expense_category_id" class="form-select @error('expense_category_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Kategori Pengeluaran --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('expense_category_id', $expense->expense_category_id) == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('expense_category_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Pemasok / Rekanan <small class="text-muted fw-normal">(Opsional)</small></label>
                                <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror">
                                    <option value="">-- Bukan Pemasok / Umum --</option>
                                    @foreach($suppliers as $sup)
                                        <option value="{{ $sup->id }}" {{ old('supplier_id', $expense->supplier_id) == $sup->id ? 'selected' : '' }}>
                                            {{ $sup->nama_supplier }} ({{ $sup->kode_supplier }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Keperluan / Deskripsi Singkat -->
                            <div class="col-12">
                                <label class="form-label fw-semibold">Nama Keperluan / Uraian Biaya <span class="text-danger">*</span></label>
                                <input type="text" name="nama_keperluan" class="form-control @error('nama_keperluan') is-invalid @enderror" value="{{ old('nama_keperluan', $expense->nama_keperluan) }}" required>
                                @error('nama_keperluan')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Nominal & Metode Pembayaran -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nominal Biaya (Rp) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light fw-bold">Rp</span>
                                    <input type="number" name="nominal" class="form-control form-control-lg fw-bold text-danger @error('nominal') is-invalid @enderror" min="1" step="any" value="{{ old('nominal', $expense->nominal) }}" required>
                                </div>
                                @error('nominal')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Metode Pembayaran <span class="text-danger">*</span></label>
                                <div class="d-flex gap-3 pt-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="metode_pembayaran" id="pay_tunai" value="tunai" {{ old('metode_pembayaran', $expense->metode_pembayaran) == 'tunai' ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="pay_tunai">
                                            <i class="bi bi-cash me-1 text-success"></i>Kas Tunai
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="metode_pembayaran" id="pay_transfer" value="transfer" {{ old('metode_pembayaran', $expense->metode_pembayaran) == 'transfer' ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="pay_transfer">
                                            <i class="bi bi-bank me-1 text-info"></i>Transfer Bank
                                        </label>
                                    </div>
                                </div>
                                @error('metode_pembayaran')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Catatan Tambahan -->
                            <div class="col-12">
                                <label class="form-label fw-semibold">Catatan / Keterangan Tambahan <small class="text-muted fw-normal">(Opsional)</small></label>
                                <textarea name="catatan" class="form-control @error('catatan') is-invalid @enderror" rows="3">{{ old('catatan', $expense->catatan) }}</textarea>
                                @error('catatan')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Info Box -->
                            <div class="col-12">
                                <div class="alert alert-warning d-flex align-items-center mb-0" role="alert">
                                    <i class="bi bi-exclamation-triangle-fill fs-5 me-2 flex-shrink-0"></i>
                                    <div class="small">
                                        Perubahan nominal atau tanggal akan <strong>otomatis memperbarui pencatatan kas keluar</strong> di Buku Kas.
                                    </div>
                                </div>
                            </div>

                            <!-- Tombol Submit -->
                            <div class="col-12 d-flex justify-content-end gap-2 pt-3 border-top">
                                <a href="{{ route('expenses.index') }}" class="btn btn-light border">Batal</a>
                                <button type="submit" class="btn btn-primary px-4 fw-semibold">
                                    <i class="bi bi-check-circle me-1"></i>Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
