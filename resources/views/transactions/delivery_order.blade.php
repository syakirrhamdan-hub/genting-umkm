<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Surat Jalan Pengiriman - {{ $transaction->no_faktur }}</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            padding: 2rem 0;
        }

        .do-sheet {
            background-color: #ffffff;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 18mm 20mm;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border-radius: 8px;
            position: relative;
        }

        .company-header {
            border-bottom: 3px double #0f172a;
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }

        .brand-title {
            font-size: 1.65rem;
            font-weight: 800;
            color: #c2410c;
            letter-spacing: -0.5px;
            line-height: 1.1;
        }

        .document-title {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: 800;
            letter-spacing: 1.5px;
            font-size: 1rem;
            padding: 0.4rem 1.25rem;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 1rem;
        }

        .info-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 0.85rem 1rem;
            font-size: 0.88rem;
            height: 100%;
        }

        .table-items {
            margin-top: 1.5rem;
            border: 1px solid #cbd5e1;
            font-size: 0.88rem;
        }

        .table-items th {
            background-color: #f1f5f9;
            border-bottom: 2px solid #cbd5e1;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.8rem;
            padding: 0.65rem 0.75rem;
        }

        .table-items td {
            padding: 0.75rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .notice-card {
            border: 1px dashed #94a3b8;
            background-color: #f8fafc;
            border-radius: 6px;
            padding: 0.75rem 1rem;
            font-size: 0.8rem;
            line-height: 1.4;
        }

        .signature-box {
            text-align: center;
            font-size: 0.85rem;
        }

        .signature-line {
            border-bottom: 1px solid #0f172a;
            width: 75%;
            margin: 4.5rem auto 0.4rem;
        }

        /* Screen Action Bar */
        .action-bar {
            max-width: 210mm;
            margin: 0 auto 1.5rem;
        }

        @media print {
            body {
                background-color: #ffffff !important;
                padding: 0 !important;
            }
            .action-bar {
                display: none !important;
            }
            .do-sheet {
                box-shadow: none !important;
                border-radius: 0 !important;
                width: 100% !important;
                min-height: auto !important;
                padding: 10mm 15mm !important;
                margin: 0 !important;
            }
        }
    </style>
</head>
<body>

    <!-- Tombol Navigasi & Cetak (Tampil di Layar Monitor Saja) -->
    <div class="action-bar d-flex justify-content-between align-items-center">
        <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-1 shadow-sm">
            <i class="bi bi-arrow-left"></i> Kembali ke Penjualan
        </a>
        <div class="d-flex gap-2">
            <a href="{{ route('transactions.invoice', $transaction->id) }}" class="btn btn-outline-primary d-flex align-items-center gap-1 shadow-sm">
                <i class="bi bi-receipt"></i> Lihat Faktur (Invoice)
            </a>
            <button onclick="window.print()" class="btn btn-warning text-dark fw-bold d-flex align-items-center gap-2 shadow-sm px-4">
                <i class="bi bi-printer-fill fs-5"></i> Cetak Surat Jalan (DO)
            </button>
        </div>
    </div>

    <!-- Lembar Dokumen Surat Jalan Fisik -->
    <div class="do-sheet">
        
        <!-- Kop Toko Genteng -->
        <div class="company-header d-flex justify-content-between align-items-start">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 bg-danger p-2 text-white d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                    <i class="bi bi-houses-fill fs-2"></i>
                </div>
                <div>
                    <div class="brand-title">GENTING JAYA</div>
                    <div class="fw-semibold text-dark" style="font-size: 0.85rem;">DISTRIBUTOR & PUSAT GENTENG BERKUALITAS</div>
                    <div class="text-muted small">Jl. Raya Genteng No. 128 | Telp / WhatsApp: 0812-3456-7890 / 0819-8765-4321</div>
                </div>
            </div>
            <div class="text-end">
                <div class="document-title">SURAT JALAN / DO</div>
                <div class="fw-bold text-dark font-monospace" style="font-size: 1.05rem;">
                    DO-{{ substr($transaction->no_faktur, 4) }}
                </div>
                <small class="text-muted">Ref Faktur: <strong>{{ $transaction->no_faktur }}</strong></small>
            </div>
        </div>

        <!-- Informasi Pengiriman (2 Kolom) -->
        <div class="row g-3 mb-3">
            <div class="col-6">
                <div class="info-box">
                    <div class="fw-bold text-uppercase text-secondary mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                        <i class="bi bi-geo-alt-fill text-danger me-1"></i>Tujuan Pengiriman:
                    </div>
                    <div class="fw-bold fs-6 text-dark mb-1">
                        {{ $transaction->nama_pelanggan }}
                    </div>
                    <div class="text-muted mb-1">
                        <i class="bi bi-telephone me-1"></i>
                        {{ $transaction->customer ? ($transaction->customer->no_hp ?: '-') : ($transaction->no_hp_pelanggan ?: '-') }}
                    </div>
                    <div class="text-dark small">
                        <strong>Alamat Proyek:</strong><br>
                        {{ $transaction->alamat_pengiriman ?: ($transaction->customer->alamat ?? 'Diambil langsung di Gudang/Toko') }}
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="info-box">
                    <div class="fw-bold text-uppercase text-secondary mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                        <i class="bi bi-truck text-primary me-1"></i>Informasi Ekspedisi / Armada:
                    </div>
                    <table class="table table-sm table-borderless mb-0 small">
                        <tr>
                            <td class="text-muted ps-0" style="width: 45%;">Tanggal Kirim</td>
                            <td class="fw-bold text-dark">: {{ \Carbon\Carbon::parse($transaction->tanggal)->translatedFormat('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Supir / Kendaraan</td>
                            <td class="fw-bold text-dark">: {{ $transaction->supir_armada ?: 'Armada Toko Genting Jaya' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Status Muatan</td>
                            <td class="fw-bold text-uppercase text-dark">: {{ $transaction->status_pengiriman ?: 'Dalam Pengiriman' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Status Pembayaran</td>
                            <td class="fw-semibold">: 
                                @if($transaction->status_pembayaran === 'lunas')
                                    <span class="text-success fw-bold">LUNAS</span>
                                @elseif($transaction->status_pembayaran === 'tempo')
                                    <span class="text-warning fw-bold">TEMPO (Ada Piutang)</span>
                                @else
                                    <span class="text-danger fw-bold">BELUM LUNAS</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tabel Rincian Genteng yang Dimuat -->
        <table class="table table-bordered table-items mb-3">
            <thead>
                <tr>
                    <th class="text-center" style="width: 45px;">No</th>
                    <th>Nama & Jenis Genteng</th>
                    <th class="text-center" style="width: 140px;">Jumlah Muat</th>
                    <th class="text-center" style="width: 100px;">Satuan</th>
                    <th class="text-center" style="width: 150px;">Kondisi Fisik</th>
                </tr>
            </thead>
            <tbody>
                @php $totalPcs = 0; @endphp
                @forelse($transaction->details as $index => $item)
                    @php $totalPcs += $item->jumlah; @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $item->nama_produk_snapshot }}</div>
                            @if($item->product && $item->product->kode_produk)
                                <small class="text-muted font-monospace">{{ $item->product->kode_produk }}</small>
                            @endif
                        </td>
                        <td class="text-center fw-bold fs-6 text-dark">
                            {{ number_format($item->jumlah, 0, ',', '.') }}
                        </td>
                        <td class="text-center text-muted">
                            {{ $item->satuan_snapshot ?: 'pcs' }}
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border">Baik / Lengkap</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-3 text-muted">Tidak ada rincian item barang.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="table-light fw-bold">
                    <td colspan="2" class="text-end text-uppercase">TOTAL MUATAN GENTENG:</td>
                    <td class="text-center fs-6 text-danger">{{ number_format($totalPcs, 0, ',', '.') }}</td>
                    <td class="text-center text-muted">Pcs</td>
                    <td class="text-center text-muted small">Cek Fisik OK</td>
                </tr>
            </tfoot>
        </table>

        <!-- Catatan Tambahan & Instruksi Muatan -->
        <div class="notice-card mb-4">
            <div class="fw-bold text-dark mb-1"><i class="bi bi-info-circle me-1"></i>Syarat & Ketentuan Pengiriman Genteng:</div>
            <ol class="mb-0 ps-3">
                <li>Mohon barang diperiksa bersama supir armada saat penurunan/bongkar di lokasi proyek.</li>
                <li>Klaim genteng pecah/retak dalam perjalanan hanya berlaku jika dicatat pada surat jalan ini sebelum supir meninggalkan lokasi.</li>
                <li>Tanda tangan pada surat jalan ini adalah bukti sah bahwa barang telah diterima sesuai jumlah dan spesifikasi di atas.</li>
            </ol>
            @if($transaction->catatan)
                <div class="mt-2 pt-2 border-top text-muted">
                    <strong>Catatan Khusus:</strong> {{ $transaction->catatan }}
                </div>
            @endif
        </div>

        <!-- Kolom Tanda Tangan Tiga Pihak -->
        <div class="row pt-2 text-center" style="margin-top: 1.5rem;">
            <div class="col-4">
                <div class="signature-box">
                    <div class="fw-semibold text-muted">Bagian Gudang / Kasir</div>
                    <div class="signature-line"></div>
                    <div class="fw-bold text-dark">Genting Jaya</div>
                </div>
            </div>
            <div class="col-4">
                <div class="signature-box">
                    <div class="fw-semibold text-muted">Supir / Pengemudi Armada</div>
                    <div class="signature-line"></div>
                    <div class="fw-bold text-dark">{{ $transaction->supir_armada ?: '( ................................... )' }}</div>
                </div>
            </div>
            <div class="col-4">
                <div class="signature-box">
                    <div class="fw-semibold text-muted">Penerima / Mandor Proyek</div>
                    <div class="signature-line"></div>
                    <div class="fw-bold text-dark">{{ $transaction->nama_pelanggan }}</div>
                </div>
            </div>
        </div>

        <!-- Cetak Waktu Dokumen -->
        <div class="text-end text-muted mt-5 pt-3 border-top" style="font-size: 0.72rem;">
            Dicetak secara sistematis oleh Toko Genting Jaya pada {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }} WIB
        </div>

    </div>

</body>
</html>
