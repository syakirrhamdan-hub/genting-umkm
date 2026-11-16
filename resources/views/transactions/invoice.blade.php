<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Faktur Penjualan - {{ $transaction->no_faktur }}</title>

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

        .invoice-sheet {
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
            background-color: #c2410c;
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

        .signature-box {
            text-align: center;
            font-size: 0.85rem;
        }

        .signature-line {
            border-bottom: 1px solid #0f172a;
            width: 75%;
            margin: 4.5rem auto 0.4rem;
        }

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
            .invoice-sheet {
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

    <!-- Tombol Aksi Layar -->
    <div class="action-bar d-flex justify-content-between align-items-center">
        <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-1 shadow-sm">
            <i class="bi bi-arrow-left"></i> Kembali ke Penjualan
        </a>
        <div class="d-flex gap-2">
            <a href="{{ route('transactions.delivery_order', $transaction->id) }}" class="btn btn-outline-warning text-dark d-flex align-items-center gap-1 shadow-sm">
                <i class="bi bi-truck"></i> Cetak Surat Jalan (DO)
            </a>
            <button onclick="window.print()" class="btn btn-terracotta btn-danger fw-bold d-flex align-items-center gap-2 shadow-sm px-4">
                <i class="bi bi-printer-fill fs-5"></i> Cetak Faktur (Invoice)
            </button>
        </div>
    </div>

    <!-- Lembar Dokumen Faktur -->
    <div class="invoice-sheet">
        
        <!-- Kop Toko -->
        <div class="company-header d-flex justify-content-between align-items-start">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 bg-danger p-2 text-white d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                    <i class="bi bi-houses-fill fs-2"></i>
                </div>
                <div>
                    <div class="brand-title">GENTING JAYA</div>
                    <div class="fw-semibold text-dark" style="font-size: 0.85rem;">DISTRIBUTOR & PUSAT GENTENG BERKUALITAS</div>
                    <div class="text-muted small">Jl. Raya Genteng No. 128 | Telp / WhatsApp: 0812-3456-7890</div>
                </div>
            </div>
            <div class="text-end">
                <div class="document-title">FAKTUR PENJUALAN</div>
                <div class="fw-bold text-dark font-monospace" style="font-size: 1.15rem;">
                    {{ $transaction->no_faktur }}
                </div>
                <small class="text-muted">Tanggal: <strong>{{ \Carbon\Carbon::parse($transaction->tanggal)->translatedFormat('d F Y') }}</strong></small>
            </div>
        </div>

        <!-- Info Pembeli & Faktur -->
        <div class="row g-3 mb-3">
            <div class="col-6">
                <div class="info-box">
                    <div class="fw-bold text-uppercase text-secondary mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                        <i class="bi bi-person-fill text-primary me-1"></i>Tagihan Kepada:
                    </div>
                    <div class="fw-bold fs-6 text-dark mb-1">
                        {{ $transaction->nama_pelanggan }}
                    </div>
                    <div class="text-muted mb-1">
                        <i class="bi bi-telephone me-1"></i>
                        {{ $transaction->customer ? ($transaction->customer->no_hp ?: '-') : ($transaction->no_hp_pelanggan ?: '-') }}
                    </div>
                    <div class="text-dark small">
                        <strong>Alamat Kirim:</strong> {{ $transaction->alamat_pengiriman ?: ($transaction->customer->alamat ?? '-') }}
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="info-box">
                    <div class="fw-bold text-uppercase text-secondary mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                        <i class="bi bi-wallet2 text-success me-1"></i>Status Pembayaran & Kirim:
                    </div>
                    <table class="table table-sm table-borderless mb-0 small">
                        <tr>
                            <td class="text-muted ps-0" style="width: 45%;">Status Bayar</td>
                            <td class="fw-bold">: 
                                @if($transaction->status_pembayaran === 'lunas')
                                    <span class="badge bg-success">LUNAS</span>
                                @elseif($transaction->status_pembayaran === 'tempo')
                                    <span class="badge bg-warning text-dark">TEMPO / DP</span>
                                @else
                                    <span class="badge bg-danger">BELUM LUNAS</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Metode Bayar</td>
                            <td class="fw-semibold text-dark">: {{ ucfirst($transaction->metode_pembayaran) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Status Kirim</td>
                            <td class="fw-semibold text-dark">: {{ ucfirst($transaction->status_pengiriman ?: 'Dalam Pengiriman') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Armada Truk</td>
                            <td class="text-dark">: {{ $transaction->supir_armada ?: 'Armada Toko' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tabel Item Barang -->
        <table class="table table-bordered table-items mb-3">
            <thead>
                <tr>
                    <th class="text-center" style="width: 45px;">No</th>
                    <th>Nama Produk Genteng</th>
                    <th class="text-center" style="width: 90px;">Jumlah</th>
                    <th class="text-end" style="width: 140px;">Harga Satuan</th>
                    <th class="text-end" style="width: 160px;">Subtotal (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transaction->details as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $item->nama_produk_snapshot }}</div>
                        </td>
                        <td class="text-center fw-semibold">
                            {{ number_format($item->jumlah, 0, ',', '.') }} {{ $item->satuan_snapshot ?: 'pcs' }}
                        </td>
                        <td class="text-end">
                            Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}
                        </td>
                        <td class="text-end fw-bold">
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-3 text-muted">Tidak ada rincian item barang.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Ringkasan Perhitungan Keuangan (2 Kolom Bawah) -->
        <div class="row g-3 mb-4">
            <div class="col-7">
                <div class="border rounded p-3 bg-light h-100">
                    <div class="fw-bold small text-dark mb-1"><i class="bi bi-bank me-1"></i>Rekening Pembayaran Toko:</div>
                    <div class="small text-muted mb-2">
                        BCA: <strong>123-456-7890</strong> a/n Toko Genting Jaya<br>
                        Mandiri: <strong>137-00-9876543-2</strong> a/n Toko Genting Jaya
                    </div>
                    @if($transaction->catatan)
                        <div class="small pt-2 border-top text-muted">
                            <strong>Catatan:</strong> {{ $transaction->catatan }}
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-5">
                <table class="table table-sm table-borderless mb-0 small">
                    <tr>
                        <td class="text-muted">Total Barang:</td>
                        <td class="text-end fw-semibold">Rp {{ number_format($transaction->total_barang, 0, ',', '.') }}</td>
                    </tr>
                    @if($transaction->diskon > 0)
                        <tr>
                            <td class="text-muted">Potongan Diskon:</td>
                            <td class="text-end text-danger">- Rp {{ number_format($transaction->diskon, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="text-muted">Ongkos Kirim Armada:</td>
                        <td class="text-end fw-semibold">+ Rp {{ number_format($transaction->ongkos_kirim, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="border-top">
                        <td class="fw-bold fs-6 text-dark">Grand Total:</td>
                        <td class="text-end fw-bold fs-6 text-dark">Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jumlah Dibayar:</td>
                        <td class="text-end fw-bold text-success">Rp {{ number_format($transaction->bayar, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="border-top">
                        <td class="fw-bold text-danger">Sisa Piutang:</td>
                        <td class="text-end fw-bold fs-6 text-danger">Rp {{ number_format($transaction->sisa_piutang, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Kolom Tanda Tangan -->
        <div class="row pt-2 text-center" style="margin-top: 2rem;">
            <div class="col-6">
                <div class="signature-box">
                    <div class="fw-semibold text-muted">Penerima / Pembeli</div>
                    <div class="signature-line"></div>
                    <div class="fw-bold text-dark">{{ $transaction->nama_pelanggan }}</div>
                </div>
            </div>
            <div class="col-6">
                <div class="signature-box">
                    <div class="fw-semibold text-muted">Hormat Kami, Kasir Toko</div>
                    <div class="signature-line"></div>
                    <div class="fw-bold text-dark">Genting Jaya</div>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
