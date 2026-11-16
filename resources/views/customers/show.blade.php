@extends('layouts.app')

@section('title', 'Detail Pelanggan: ' . $customer->nama)
@section('page_title', 'Profil & Riwayat Transaksi Pelanggan')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Pelanggan
        </a>
        <div class="d-flex gap-2">
            <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil"></i> Edit Pelanggan
            </a>
            <a href="{{ route('transactions.create') }}" class="btn btn-terracotta btn-sm">
                <i class="bi bi-cart-plus"></i> Buat Order Baru
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Customer Profile Card -->
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-person-badge text-primary me-2"></i>Informasi Pelanggan</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center fw-bold fs-3 mb-2" style="width: 64px; height: 64px;">
                            {{ strtoupper(substr($customer->nama, 0, 2)) }}
                        </div>
                        <h5 class="fw-bold mb-0 text-dark">{{ $customer->nama }}</h5>
                        <span class="badge bg-light text-dark border font-monospace mt-1">{{ $customer->kode_pelanggan }}</span>
                    </div>

                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item d-flex justify-content-between px-0 py-2">
                            <span class="text-muted">No WhatsApp:</span>
                            @if($customer->no_hp)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $customer->no_hp) }}" target="_blank" class="fw-semibold text-success text-decoration-none">
                                    <i class="bi bi-whatsapp me-1"></i>{{ $customer->no_hp }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </li>
                        <li class="list-group-item px-0 py-2">
                            <span class="text-muted d-block mb-1">Alamat Pengiriman Proyek:</span>
                            <div class="fw-semibold text-dark">{{ $customer->alamat ?: '-' }}</div>
                        </li>
                        <li class="list-group-item px-0 py-2">
                            <span class="text-muted d-block mb-1">Catatan Tambahan:</span>
                            <div class="text-muted">{{ $customer->keterangan ?: '-' }}</div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Customer Summary Metrics -->
        <div class="col-lg-8">
            <div class="row g-3 mb-3">
                <div class="col-sm-4">
                    <div class="card bg-white border-0 shadow-sm p-3">
                        <span class="text-muted small text-uppercase fw-semibold">Total Order</span>
                        <h3 class="fw-bold mb-0 text-dark">{{ $customer->transactions->count() }} <small class="fs-6 fw-normal text-muted">Faktur</small></h3>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card bg-white border-0 shadow-sm p-3">
                        <span class="text-muted small text-uppercase fw-semibold">Total Belanja</span>
                        <h3 class="fw-bold mb-0 text-primary">Rp {{ number_format($customer->transactions->sum('grand_total'), 0, ',', '.') }}</h3>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card bg-white border-0 shadow-sm p-3">
                        <span class="text-muted small text-uppercase fw-semibold">Sisa Piutang / Tempo</span>
                        <h3 class="fw-bold mb-0 {{ $customer->sisa_piutang > 0 ? 'text-danger' : 'text-success' }}">
                            Rp {{ number_format($customer->sisa_piutang, 0, ',', '.') }}
                        </h3>
                    </div>
                </div>
            </div>

            <!-- Transaction History Table -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history text-primary me-2"></i>Riwayat Pembelian Genteng</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>No Faktur</th>
                                <th>Tanggal</th>
                                <th>Total Belanja</th>
                                <th>Bayar</th>
                                <th>Sisa Piutang</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customer->transactions as $t)
                                <tr>
                                    <td>
                                        <a href="{{ route('transactions.show', $t->id) }}" class="fw-bold text-decoration-none text-dark">
                                            {{ $t->no_faktur }}
                                        </a>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($t->tanggal)->format('d M Y') }}</td>
                                    <td class="fw-bold">Rp {{ number_format($t->grand_total, 0, ',', '.') }}</td>
                                    <td class="text-success">Rp {{ number_format($t->bayar, 0, ',', '.') }}</td>
                                    <td class="text-danger fw-semibold">Rp {{ number_format($t->sisa_piutang, 0, ',', '.') }}</td>
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
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Belum ada riwayat pembelian genteng oleh pelanggan ini.</td>
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
