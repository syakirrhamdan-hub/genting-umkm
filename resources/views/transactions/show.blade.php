@extends('layouts.app')

@section('title', 'Detail Penjualan ' . $transaction->no_faktur)
@section('page_title', 'Rincian Penjualan & Piutang')

@section('content')
<div class="container-fluid px-0">

    <!-- Header Actions -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
            <h4 class="mb-0 fw-bold text-dark font-monospace">{{ $transaction->no_faktur }}</h4>
            @if($transaction->status_pembayaran === 'lunas')
                <span class="badge badge-lunas px-2 py-1">Lunas</span>
            @elseif($transaction->status_pembayaran === 'tempo')
                <span class="badge badge-tempo px-2 py-1">Tempo / DP</span>
            @else
                <span class="badge badge-belum px-2 py-1">Belum Lunas</span>
            @endif
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('transactions.invoice', $transaction->id) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-printer me-1"></i>Cetak Faktur
            </a>
            <a href="{{ route('transactions.delivery_order', $transaction->id) }}" target="_blank" class="btn btn-outline-warning text-dark btn-sm">
                <i class="bi bi-truck me-1"></i>Cetak Surat Jalan (DO)
            </a>
            @if($transaction->sisa_piutang > 0)
                <button type="button" class="btn btn-success btn-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#paymentModal">
                    <i class="bi bi-cash-coin me-1"></i>Catat Cicilan / Pelunasan
                </button>
            @endif
        </div>
    </div>

    <div class="row g-4">
        <!-- Kolom Kiri: Detail Pelanggan, Pengiriman & Rincian Item -->
        <div class="col-lg-8">
            <!-- Informasi Pelanggan & Pengiriman -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-info-circle text-primary me-2"></i>Informasi Pelanggan & Ekspedisi</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Pelanggan / Pembeli:</span>
                            <span class="fw-bold text-dark fs-6">{{ $transaction->nama_pelanggan }}</span>
                            @if($transaction->customer && $transaction->customer->no_hp)
                                <div class="small text-muted mt-1">
                                    <i class="bi bi-whatsapp text-success me-1"></i>{{ $transaction->customer->no_hp }}
                                </div>
                            @elseif($transaction->no_hp_pelanggan)
                                <div class="small text-muted mt-1">
                                    <i class="bi bi-telephone me-1"></i>{{ $transaction->no_hp_pelanggan }}
                                </div>
                            @endif
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Tanggal Transaksi:</span>
                            <span class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($transaction->tanggal)->translatedFormat('d F Y') }}</span>
                            <div class="small text-muted mt-1">
                                Metode Bayar: <strong>{{ ucfirst($transaction->metode_pembayaran) }}</strong>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Supir / Armada Truk:</span>
                            <span class="fw-semibold text-dark">{{ $transaction->supir_armada ?: 'Armada Toko' }}</span>
                            <div class="small text-muted mt-1">
                                Status: <span class="badge bg-light text-dark border">{{ ucfirst($transaction->status_pengiriman ?: 'Dalam Pengiriman') }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Alamat Pengiriman / Lokasi Proyek:</span>
                            <span class="text-dark small">{{ $transaction->alamat_pengiriman ?: ($transaction->customer->alamat ?? '-') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Item Genteng -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-box-seam text-danger me-2"></i>Daftar Genteng yang Dipesan</h6>
                    <span class="badge bg-secondary rounded-pill">{{ $transaction->details->count() }} Macam Produk</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Genteng</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-end">Harga Satuan</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transaction->details as $item)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $item->nama_produk_snapshot }}</div>
                                        @if($item->product)
                                            <small class="text-muted font-monospace">{{ $item->product->kode_produk }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center fw-semibold">
                                        {{ number_format($item->jumlah, 0, ',', '.') }} {{ $item->satuan_snapshot ?: 'pcs' }}
                                    </td>
                                    <td class="text-end text-muted">
                                        Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}
                                    </td>
                                    <td class="text-end fw-bold text-dark">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Riwayat Cicilan / Pembayaran Piutang -->
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-receipt-cutoff text-success me-2"></i>Riwayat Pembayaran & Kwitansi</h6>
                    @if($transaction->sisa_piutang > 0)
                        <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#paymentModal">
                            + Bayar Cicilan
                        </button>
                    @endif
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No Kwitansi</th>
                                    <th>Tanggal</th>
                                    <th>Metode</th>
                                    <th>Catatan</th>
                                    <th class="text-end">Nominal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Pembayaran Pertama / DP Saat Checkout -->
                                <tr>
                                    <td><span class="badge bg-light text-dark border">DP Awal</span></td>
                                    <td>{{ \Carbon\Carbon::parse($transaction->tanggal)->format('d/m/Y') }}</td>
                                    <td>{{ ucfirst($transaction->metode_pembayaran) }}</td>
                                    <td class="text-muted small">Pembayaran pertama saat transaksi dibuat</td>
                                    <td class="text-end fw-bold text-success">Rp {{ number_format($transaction->bayar - $transaction->paymentReceipts->sum('nominal'), 0, ',', '.') }}</td>
                                </tr>
                                @forelse($transaction->paymentReceipts as $receipt)
                                    <tr>
                                        <td><span class="badge bg-light text-dark font-monospace border">{{ $receipt->no_kwitansi }}</span></td>
                                        <td>{{ \Carbon\Carbon::parse($receipt->tanggal)->format('d/m/Y') }}</td>
                                        <td>{{ ucfirst($receipt->metode_pembayaran) }}</td>
                                        <td class="text-muted small">{{ $receipt->catatan ?: '-' }}</td>
                                        <td class="text-end fw-bold text-success">+ Rp {{ number_format($receipt->nominal, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Rincian Keuangan & Piutang -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-cash-stack text-success me-2"></i>Ringkasan Keuangan</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted">Total Barang:</td>
                            <td class="text-end fw-semibold">Rp {{ number_format($transaction->total_barang, 0, ',', '.') }}</td>
                        </tr>
                        @if($transaction->diskon > 0)
                            <tr>
                                <td class="text-muted">Diskon Penjualan:</td>
                                <td class="text-end text-danger">- Rp {{ number_format($transaction->diskon, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td class="text-muted">Ongkos Kirim Armada:</td>
                            <td class="text-end fw-semibold">+ Rp {{ number_format($transaction->ongkos_kirim, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="border-top">
                            <td class="fw-bold fs-6">Grand Total:</td>
                            <td class="text-end fw-bold fs-6 text-dark">Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Total Diterima:</td>
                            <td class="text-end fw-bold text-success">Rp {{ number_format($transaction->bayar, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="border-top table-light">
                            <td class="fw-bold text-danger">Sisa Piutang:</td>
                            <td class="text-end fw-bold fs-5 text-danger">Rp {{ number_format($transaction->sisa_piutang, 0, ',', '.') }}</td>
                        </tr>
                    </table>

                    @if($transaction->sisa_piutang > 0)
                        <div class="d-grid mt-3">
                            <button type="button" class="btn btn-success fw-semibold" data-bs-toggle="modal" data-bs-target="#paymentModal">
                                <i class="bi bi-cash-coin me-1"></i>Catat Cicilan Piutang
                            </button>
                        </div>
                    @else
                        <div class="alert alert-success d-flex align-items-center mt-3 mb-0 py-2" role="alert">
                            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                            <div class="small fw-semibold">Faktur ini telah lunas sepenuhnya!</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Card Batal Transaksi -->
            <div class="card shadow-sm border-danger-subtle">
                <div class="card-body">
                    <h6 class="fw-bold text-danger mb-2"><i class="bi bi-exclamation-triangle me-1"></i>Batalkan Transaksi</h6>
                    <small class="text-muted d-block mb-3">
                        Jika dibatalkan, stok seluruh genteng yang terjual akan otomatis dikembalikan ke gudang dan catatan kas terkait akan disesuaikan.
                    </small>
                    <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan transaksi ini? Stok genteng akan dikembalikan ke gudang.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                            <i class="bi bi-trash me-1"></i>Batalkan & Kembalikan Stok
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Catat Cicilan Piutang -->
    @if($transaction->sisa_piutang > 0)
        <div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('transactions.payment', $transaction->id) }}" method="POST">
                        @csrf
                        <div class="modal-header bg-light">
                            <h6 class="modal-title fw-bold">
                                <i class="bi bi-cash-coin text-success me-2"></i>Catat Pembayaran Cicilan Piutang
                            </h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="alert alert-info py-2 mb-3 small">
                                Sisa Piutang Saat Ini: <strong class="text-danger">Rp {{ number_format($transaction->sisa_piutang, 0, ',', '.') }}</strong>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tanggal Pembayaran <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nominal Pembayaran (Rp) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light fw-bold">Rp</span>
                                    <input type="number" name="nominal" class="form-control form-control-lg fw-bold text-success" min="1" max="{{ $transaction->sisa_piutang }}" value="{{ $transaction->sisa_piutang }}" required>
                                </div>
                                <div class="form-text text-muted">Maksimal Rp {{ number_format($transaction->sisa_piutang, 0, ',', '.') }} (Bisa bayar sebagian atau lunas).</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Metode Pembayaran <span class="text-danger">*</span></label>
                                <select name="metode_pembayaran" class="form-select" required>
                                    <option value="tunai">Kas Tunai</option>
                                    <option value="transfer">Transfer Bank</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Catatan</label>
                                <textarea name="catatan" class="form-control" rows="2" placeholder="Contoh: Cicilan termin kedua via transfer..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success btn-sm fw-semibold">
                                <i class="bi bi-check-circle me-1"></i>Simpan Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection
