@extends('layouts.app')

@section('title', 'Detail Pengeluaran ' . $expense->no_pengeluaran)
@section('page_title', 'Rincian Catatan Pengeluaran')

@section('content')
<div class="container-fluid px-0">

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-receipt text-danger me-2"></i>Pengeluaran {{ $expense->no_pengeluaran }}
                    </h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('expenses.edit', $expense->id) }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                        <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4 pb-3 border-bottom">
                        <span class="text-muted small text-uppercase fw-semibold">Nominal Pengeluaran</span>
                        <h2 class="fw-bold text-danger mb-1 mt-1">Rp {{ number_format($expense->nominal, 0, ',', '.') }}</h2>
                        <span class="badge {{ $expense->metode_pembayaran == 'transfer' ? 'bg-info-subtle text-info' : 'bg-success-subtle text-success' }} border px-3 py-1">
                            Metode: {{ ucfirst($expense->metode_pembayaran) }}
                        </span>
                    </div>

                    <table class="table table-borderless align-middle mb-4">
                        <tr>
                            <th class="text-muted" style="width: 35%;">No Pengeluaran</th>
                            <td class="fw-bold font-monospace">{{ $expense->no_pengeluaran }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Tanggal Transaksi</th>
                            <td class="fw-semibold">{{ \Carbon\Carbon::parse($expense->tanggal)->translatedFormat('d F Y') }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Kategori</th>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary border">
                                    {{ $expense->category ? $expense->category->nama_kategori : 'Tanpa Kategori' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">Nama Keperluan</th>
                            <td class="fw-bold text-dark">{{ $expense->nama_keperluan }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Pemasok / Rekanan</th>
                            <td>{{ $expense->supplier ? $expense->supplier->nama_supplier : '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Catatan Tambahan</th>
                            <td>{{ $expense->catatan ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Waktu Dicatat</th>
                            <td class="text-muted small">{{ $expense->created_at ? $expense->created_at->format('d/m/Y H:i') : '-' }}</td>
                        </tr>
                    </table>

                    <div class="alert alert-success d-flex align-items-center mb-0">
                        <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                        <div class="small">
                            Transaksi ini tercatat di <strong>Buku Kas Toko</strong> sebagai kas keluar.
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3">
                    <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" onsubmit="return confirm('Hapus pengeluaran ini secara permanen?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash me-1"></i>Hapus Pengeluaran
                        </button>
                    </form>
                    <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-secondary">
                        Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
