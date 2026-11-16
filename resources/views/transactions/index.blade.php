@extends('layouts.app')

@section('title', 'Penjualan Genteng')
@section('page_title', 'Daftar Penjualan Genteng (Faktur & Piutang)')

@section('content')
    <div class="container-fluid px-0">

        <!-- Summary Metrics -->
        <div class="row g-3 mb-4">
            <div class="col-sm-4">
                <div class="card bg-white border-0 shadow-sm p-3">
                    <span class="text-muted small text-uppercase fw-semibold">Total Nilai Penjualan</span>
                    <h4 class="fw-bold mb-0 text-dark">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</h4>
                    <small class="text-muted">Total grand total transaksi yang difilter</small>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card bg-white border-0 shadow-sm p-3">
                    <span class="text-muted small text-uppercase fw-semibold">Total Kas Diterima</span>
                    <h4 class="fw-bold mb-0 text-success">Rp {{ number_format($totalTerbayar, 0, ',', '.') }}</h4>
                    <small class="text-muted">Uang masuk tunai/transfer</small>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card bg-white border-0 shadow-sm p-3">
                    <span class="text-muted small text-uppercase fw-semibold">Total Sisa Piutang</span>
                    <h4 class="fw-bold mb-0 text-danger">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</h4>
                    <small class="text-muted">Belum lunas / tempo pembayaran</small>
                </div>
            </div>
        </div>

        <!-- Filters & Action -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('transactions.index') }}" class="row g-2 align-items-center">
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control"
                                placeholder="No faktur / nama pelanggan..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select name="status_pembayaran" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="lunas" {{ request('status_pembayaran') == 'lunas' ? 'selected' : '' }}>Lunas
                            </option>
                            <option value="tempo" {{ request('status_pembayaran') == 'tempo' ? 'selected' : '' }}>Tempo / DP
                            </option>
                            <option value="belum_lunas" {{ request('status_pembayaran') == 'belum_lunas' ? 'selected' : '' }}>
                                Belum Lunas</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}"
                            title="Dari Tanggal">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}"
                            title="Sampai Tanggal">
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                        @if(request()->anyFilled(['search', 'status_pembayaran', 'start_date', 'end_date']))
                            <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary" title="Reset"><i
                                    class="bi bi-x-lg"></i></a>
                        @endif
                        <a href="{{ route('transactions.create') }}"
                            class="btn btn-success d-inline-flex align-items-center gap-1">
                            <i class="bi bi-cart-plus"></i> Kasir
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>No Faktur & Tanggal</th>
                            <th>Pelanggan</th>
                            <th class="text-end">Total Barang</th>
                            <th class="text-end">Ongkir Armada</th>
                            <th class="text-end">Grand Total</th>
                            <th class="text-end">Bayar</th>
                            <th class="text-end">Sisa Piutang</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $t)
                            <tr>
                                <td>
                                    <a href="{{ route('transactions.show', $t->id) }}"
                                        class="fw-bold text-decoration-none text-dark d-block">
                                        {{ $t->no_faktur }}
                                    </a>
                                    <small class="text-muted"><i
                                            class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($t->tanggal)->format('d M Y') }}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold text-truncate" style="max-width: 180px;">{{ $t->nama_pelanggan }}
                                    </div>
                                    <small class="text-muted">{{ $t->details->count() }} item genteng</small>
                                </td>
                                <td class="text-end text-muted">
                                    Rp {{ number_format($t->total_barang, 0, ',', '.') }}
                                </td>
                                <td class="text-end text-muted">
                                    Rp {{ number_format($t->ongkos_kirim, 0, ',', '.') }}
                                </td>
                                <td class="text-end fw-bold text-dark">
                                    Rp {{ number_format($t->grand_total, 0, ',', '.') }}
                                </td>
                                <td class="text-end text-success fw-semibold">
                                    Rp {{ number_format($t->bayar, 0, ',', '.') }}
                                </td>
                                <td class="text-end">
                                    @if($t->sisa_piutang > 0)
                                        <span class="text-danger fw-bold">Rp
                                            {{ number_format($t->sisa_piutang, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($t->status_pembayaran === 'lunas')
                                        <span class="badge badge-lunas px-2 py-1">Lunas</span>
                                    @elseif($t->status_pembayaran === 'tempo')
                                        <span class="badge badge-tempo px-2 py-1">Tempo / DP</span>
                                    @else
                                        <span class="badge badge-belum px-2 py-1">Belum Lunas</span>
                                    @endif
                                    <div class="mt-1" style="font-size: 0.72rem;">
                                        <span
                                            class="badge bg-light text-dark border">{{ ucfirst($t->metode_pembayaran) }}</span>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="{{ route('transactions.show', $t->id) }}" class="btn btn-sm btn-outline-info"
                                            title="Lihat Faktur">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('transactions.invoice', $t->id) }}" target="_blank"
                                            class="btn btn-sm btn-outline-secondary" title="Cetak Nota Faktur">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                        <a href="{{ route('transactions.delivery_order', $t->id) }}" target="_blank"
                                            class="btn btn-sm btn-outline-warning" title="Cetak Surat Jalan Truk">
                                            <i class="bi bi-truck"></i>
                                        </a>
                                        <form action="{{ route('transactions.destroy', $t->id) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Apakah Anda yakin ingin membatalkan transaksi ini? Stok genteng akan dikembalikan ke gudang.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                title="Batalkan Transaksi">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="bi bi-receipt fs-1 d-block mb-2 text-secondary"></i>
                                    Belum ada transaksi penjualan genteng ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($transactions->hasPages())
                <div class="card-footer bg-white py-3">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection