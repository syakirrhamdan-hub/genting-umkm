@extends('layouts.app')

@section('title', 'Pemasok & Pabrik Genteng')
@section('page_title', 'Daftar Pemasok & Sentra Pengrajin Genteng')

@section('content')
<div class="container-fluid px-0">

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-2 align-items-center justify-content-between">
                <div class="col-md-6">
                    <form method="GET" action="{{ route('suppliers.index') }}" class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari pemasok, kontak, atau no telp..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">Cari</button>
                        @if(request('search'))
                            <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary" title="Reset"><i class="bi bi-x-lg"></i></a>
                        @endif
                    </form>
                </div>
                <div class="col-md-4 text-md-end mt-2 mt-md-0">
                    <a href="{{ route('suppliers.create') }}" class="btn btn-terracotta d-inline-flex align-items-center gap-1">
                        <i class="bi bi-truck"></i> Tambah Pemasok Baru
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
                        <th>Nama Pabrik / Pengrajin</th>
                        <th>Kontak PIC</th>
                        <th>No Telepon / WA</th>
                        <th>Alamat / Asal Sentra</th>
                        <th class="text-center">Riwayat Kulakan</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $s)
                        <tr>
                            <td>
                                <span class="badge bg-light text-dark border font-monospace">{{ $s->kode_supplier }}</span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $s->nama_supplier }}</div>
                                @if($s->keterangan)
                                    <small class="text-muted d-block text-truncate" style="max-width: 250px;">{{ $s->keterangan }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="fw-semibold text-muted">{{ $s->nama_kontak ?: '-' }}</span>
                            </td>
                            <td>
                                @if($s->no_hp)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $s->no_hp) }}" target="_blank" class="text-decoration-none text-success">
                                        <i class="bi bi-whatsapp me-1"></i>{{ $s->no_hp }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted text-truncate d-block" style="max-width: 250px;">{{ $s->alamat ?: '-' }}</small>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary-subtle text-primary border px-2 py-1">
                                    {{ $s->expenses->count() }} Kali
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="{{ route('suppliers.edit', $s->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('suppliers.destroy', $s->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pemasok ini?');">
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
                            <td colspan="7" class="text-center py-5 text-muted">Belum ada pemasok genteng terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($suppliers->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $suppliers->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
