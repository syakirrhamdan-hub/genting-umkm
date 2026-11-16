@extends('layouts.app')

@section('title', 'Data Pelanggan')
@section('page_title', 'Buku Pelanggan & Mitra Pembeli Genteng')

@section('content')
<div class="container-fluid px-0">

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-2 align-items-center justify-content-between">
                <div class="col-md-6">
                    <form method="GET" action="{{ route('customers.index') }}" class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari nama, no telp, atau kode pelanggan..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">Cari</button>
                        @if(request('search'))
                            <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary" title="Reset"><i class="bi bi-x-lg"></i></a>
                        @endif
                    </form>
                </div>
                <div class="col-md-4 text-md-end mt-2 mt-md-0">
                    <a href="{{ route('customers.create') }}" class="btn btn-terracotta d-inline-flex align-items-center gap-1">
                        <i class="bi bi-person-plus"></i> Tambah Pelanggan Baru
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Pelanggan</th>
                        <th>No Telepon / WA</th>
                        <th>Alamat / Lokasi Proyek</th>
                        <th class="text-center">Total Transaksi</th>
                        <th class="text-end">Sisa Piutang</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $c)
                        <tr>
                            <td>
                                <span class="badge bg-light text-dark border font-monospace">{{ $c->kode_pelanggan }}</span>
                            </td>
                            <td>
                                <a href="{{ route('customers.show', $c->id) }}" class="fw-bold text-dark text-decoration-none">
                                    {{ $c->nama }}
                                </a>
                                @if($c->keterangan)
                                    <small class="text-muted d-block text-truncate" style="max-width: 250px;">{{ $c->keterangan }}</small>
                                @endif
                            </td>
                            <td>
                                @if($c->no_hp)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $c->no_hp) }}" target="_blank" class="text-decoration-none text-success">
                                        <i class="bi bi-whatsapp me-1"></i>{{ $c->no_hp }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted text-truncate d-block" style="max-width: 260px;">{{ $c->alamat ?: '-' }}</small>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                                    {{ $c->transactions->count() }} Order
                                </span>
                            </td>
                            <td class="text-end">
                                @if($c->sisa_piutang > 0)
                                    <span class="badge badge-tempo px-2 py-1">Rp {{ number_format($c->sisa_piutang, 0, ',', '.') }}</span>
                                @else
                                    <span class="badge badge-lunas px-2 py-1">Lunas</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="{{ route('customers.show', $c->id) }}" class="btn btn-sm btn-outline-info" title="Riwayat Transaksi">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('customers.edit', $c->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('customers.destroy', $c->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pelanggan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">Belum ada data pelanggan terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($customers->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $customers->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
