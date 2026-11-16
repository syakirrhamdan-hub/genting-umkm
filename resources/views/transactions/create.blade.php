@extends('layouts.app')

@section('title', 'Kasir / Penjualan Genteng Baru')
@section('page_title', 'Form Penjualan & Kasir Toko Genteng')

@section('content')
    <div class="container-fluid px-0">
        <form action="{{ route('transactions.store') }}" method="POST" id="salesForm">
            @csrf

            <div class="row g-4">
                <!-- Left Column: Header Transaksi & Keranjang Barang -->
                <div class="col-lg-8">
                    <!-- Info Faktur & Pelanggan -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <div class="row g-2 align-items-center justify-content-between">
                                <div class="col-sm-6">
                                    <h6 class="mb-0 fw-bold"><i class="bi bi-receipt-cutoff text-primary me-2"></i>Informasi
                                        Faktur & Pengiriman</h6>
                                </div>
                                <div class="col-sm-6 text-sm-end">
                                    <span
                                        class="badge bg-light text-dark border font-monospace px-2 py-1">{{ $defaultNoFaktur }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">No Faktur <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="no_faktur" class="form-control font-monospace"
                                        value="{{ old('no_faktur', $defaultNoFaktur) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Tanggal Transaksi <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="tanggal" class="form-control"
                                        value="{{ old('tanggal', date('Y-m-d')) }}" required>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Pilih Pelanggan Terdaftar</label>
                                    <select name="customer_id" id="customer_id" class="form-select"
                                        onchange="toggleCustomerManual(this)">
                                        <option value="">-- Pelanggan Umum / Input Manual --</option>
                                        @foreach($customers as $c)
                                            <option value="{{ $c->id }}" data-alamat="{{ $c->alamat }}"
                                                data-hp="{{ $c->no_hp }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                                                {{ $c->nama }} ({{ $c->kode_pelanggan }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6" id="manualNameContainer">
                                    <label class="form-label fw-semibold">Nama Pembeli (Jika Pelanggan Umum)</label>
                                    <input type="text" name="nama_pelanggan_manual" id="nama_pelanggan_manual"
                                        class="form-control" placeholder="Contoh: Bpk. Ahmad"
                                        value="{{ old('nama_pelanggan_manual') }}">
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">No Telepon / WhatsApp</label>
                                    <input type="text" name="no_hp_pelanggan" id="no_hp_pelanggan" class="form-control"
                                        placeholder="08..." value="{{ old('no_hp_pelanggan') }}">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Status Pengiriman</label>
                                    <select name="status_pengiriman" class="form-select">
                                        <option value="ambil_sendiri">Diambil Sendiri di Gudang</option>
                                        <option value="menunggu_kirim" selected>Menunggu Pengiriman Truk</option>
                                        <option value="proses_kirim">Sedang Dalam Perjalanan Kirim</option>
                                        <option value="terkirim">Sudah Diterima di Lokasi</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Supir & Armada Truk</label>
                                    <input type="text" name="supir_armada" class="form-control"
                                        placeholder="Contoh: Truk Colt Diesel - Pak Joko" value="{{ old('supir_armada') }}">
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Alamat Tujuan Pengiriman (Proyek)</label>
                                    <textarea name="alamat_pengiriman" id="alamat_pengiriman" class="form-control" rows="2"
                                        placeholder="Alamat lokasi bongkar genteng...">{{ old('alamat_pengiriman') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Keranjang Item Genteng -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold"><i class="bi bi-box-seam text-primary me-2"></i>Rincian Genteng yang
                                Dipesan</h6>
                            <button type="button" class="btn btn-sm btn-outline-success fw-semibold" onclick="addItemRow()">
                                <i class="bi bi-plus-lg"></i> Tambah Baris Genteng
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle mb-0" id="itemsTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="min-width: 250px;">Produk Genteng & Stok</th>
                                            <th style="width: 140px;">Jumlah (Pcs)</th>
                                            <th style="width: 170px;">Harga Satuan (Rp)</th>
                                            <th style="width: 180px;">Subtotal (Rp)</th>
                                            <th style="width: 50px;" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemRowsContainer">
                                        <!-- Baris item akan dimasukkan di sini oleh JS -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-white text-end py-3">
                            <span class="text-muted me-3">Subtotal Produk:</span>
                            <h5 class="d-inline-block mb-0 fw-bold text-dark" id="displaySubtotalBarang">Rp 0</h5>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Ringkasan Biaya, Kas Masuk & Pembayaran -->
                <div class="col-lg-4">
                    <div class="card shadow-sm sticky-top" style="top: 85px;">
                        <div class="card-header bg-white">
                            <h6 class="mb-0 fw-bold"><i class="bi bi-cash-stack text-success me-2"></i>Kalkulasi Pembayaran
                            </h6>
                        </div>
                        <div class="card-body">
                            <!-- Biaya Tambahan -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Ongkos Kirim / Armada Truk (Rp)</label>
                                <input type="number" name="ongkos_kirim" id="ongkos_kirim"
                                    class="form-control text-end fs-6" value="0" min="0" oninput="calculateTotals()">
                                <small class="text-muted">Biaya sewa truk/solar pengantaran</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Potongan / Diskon (Rp)</label>
                                <input type="number" name="diskon" id="diskon" class="form-control text-end fs-6" value="0"
                                    min="0" oninput="calculateTotals()">
                            </div>

                            <hr>

                            <!-- Total Tagihan -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fw-bold text-dark fs-6">Grand Total:</span>
                                <h4 class="mb-0 fw-bold text-primary" id="displayGrandTotal">Rp 0</h4>
                            </div>

                            <hr>

                            <!-- Rincian Pembayaran -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Metode Pembayaran</label>
                                <select name="metode_pembayaran" class="form-select">
                                    <option value="tunai">Uang Tunai (Cash)</option>
                                    <option value="transfer">Transfer Bank</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nominal Bayar / DP (Rp) <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="bayar" id="bayar"
                                    class="form-control text-end fw-bold fs-5 text-success" value="0" min="0" required
                                    oninput="calculateTotals()">
                                <div class="d-flex gap-1 mt-2">
                                    <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2"
                                        style="font-size: 0.75rem;" onclick="setPaymentQuick('full')">Lunas (Uang
                                        Pas)</button>
                                    <button type="button" class="btn btn-xs btn-outline-warning py-0 px-2"
                                        style="font-size: 0.75rem;" onclick="setPaymentQuick('dp50')">DP 50%</button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2"
                                        style="font-size: 0.75rem;" onclick="setPaymentQuick('zero')">Nol (Tempo)</button>
                                </div>
                            </div>

                            <!-- Status Piutang -->
                            <div class="card bg-light border p-3 mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small text-muted fw-semibold">Sisa Piutang:</span>
                                    <span class="fw-bold fs-6" id="displaySisaPiutang">Rp 0</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small text-muted fw-semibold">Status:</span>
                                    <span class="badge" id="displayStatusBadge">Lunas</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Catatan Transaksi</label>
                                <textarea name="catatan" class="form-control" rows="2"
                                    placeholder="Catatan termin, perjanjian waktu kirim..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-success w-100 py-2 fw-bold fs-6 shadow-sm">
                                <i class="bi bi-check2-circle me-1"></i> Simpan Transaksi & Cetak
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        // Data produk dari backend
        const productsData = {
            @foreach($allProducts as $prod)
                    "{{ $prod->id }}": {
                    id: {{ $prod->id }},
                    nama: "{{ $prod->nama_produk }}",
                    harga_jual: {{ $prod->harga_jual }},
                    stok: {{ $prod->stok }},
                    satuan: "{{ $prod->satuan }}"
                },
            @endforeach
        };

        let rowIndex = 0;

        function addItemRow() {
            const container = document.getElementById('itemRowsContainer');
            const tr = document.createElement('tr');
            tr.id = 'itemRow_' + rowIndex;

            let optionsHtml = '<option value="">-- Pilih Jenis Genteng --</option>';
            for (const id in productsData) {
                const p = productsData[id];
                const stockBadge = p.stok > 0 ? `(Stok: ${p.stok.toLocaleString('id-ID')} ${p.satuan})` : `(STOK HABIS)`;
                const disabled = p.stok <= 0 ? 'disabled' : '';
                optionsHtml += `<option value="${p.id}" ${disabled}>${p.nama} ${stockBadge} - Rp ${p.harga_jual.toLocaleString('id-ID')}</option>`;
            }

            tr.innerHTML = `
                <td>
                    <select name="items[${rowIndex}][product_id]" class="form-select form-select-sm" onchange="onProductSelect(${rowIndex}, this)" required>
                        ${optionsHtml}
                    </select>
                    <div class="text-muted small mt-1" id="stockNote_${rowIndex}"></div>
                </td>
                <td>
                    <input type="number" name="items[${rowIndex}][jumlah]" id="qty_${rowIndex}" class="form-control form-control-sm text-center" min="1" value="1000" oninput="calculateRow(${rowIndex})" required>
                </td>
                <td>
                    <input type="number" name="items[${rowIndex}][harga_satuan]" id="price_${rowIndex}" class="form-control form-control-sm text-end" min="0" value="0" oninput="calculateRow(${rowIndex})" required>
                </td>
                <td>
                    <input type="text" id="subtotal_${rowIndex}" class="form-control form-control-sm text-end fw-bold bg-light" value="Rp 0" readonly>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2" onclick="removeRow(${rowIndex})">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;

            container.appendChild(tr);
            rowIndex++;
        }

        function removeRow(idx) {
            const row = document.getElementById('itemRow_' + idx);
            if (row) {
                row.remove();
                calculateTotals();
            }
        }

        function onProductSelect(idx, selectEl) {
            const prodId = selectEl.value;
            const priceInput = document.getElementById('price_' + idx);
            const stockNote = document.getElementById('stockNote_' + idx);
            const qtyInput = document.getElementById('qty_' + idx);

            if (prodId && productsData[prodId]) {
                const prod = productsData[prodId];
                priceInput.value = prod.harga_jual;
                stockNote.innerHTML = `<span class="badge bg-light text-dark border">Tersedia: <strong>${prod.stok.toLocaleString('id-ID')} ${prod.satuan}</strong></span>`;
                if (prod.stok > 0 && prod.stok < parseInt(qtyInput.value)) {
                    qtyInput.value = prod.stok;
                }
            } else {
                priceInput.value = 0;
                stockNote.innerText = '';
            }

            calculateRow(idx);
        }

        function calculateRow(idx) {
            const qty = parseFloat(document.getElementById('qty_' + idx)?.value) || 0;
            const price = parseFloat(document.getElementById('price_' + idx)?.value) || 0;
            const subtotal = qty * price;

            const subtotalEl = document.getElementById('subtotal_' + idx);
            if (subtotalEl) {
                subtotalEl.value = 'Rp ' + subtotal.toLocaleString('id-ID');
            }

            calculateTotals();
        }

        function calculateTotals() {
            let totalBarang = 0;
            const container = document.getElementById('itemRowsContainer');
            const rows = container.querySelectorAll('tr');

            rows.forEach(row => {
                const idx = row.id.split('_')[1];
                const qty = parseFloat(document.getElementById('qty_' + idx)?.value) || 0;
                const price = parseFloat(document.getElementById('price_' + idx)?.value) || 0;
                totalBarang += (qty * price);
            });

            const ongkir = parseFloat(document.getElementById('ongkos_kirim').value) || 0;
            const diskon = parseFloat(document.getElementById('diskon').value) || 0;
            const grandTotal = Math.max(0, (totalBarang - diskon) + ongkir);

            document.getElementById('displaySubtotalBarang').innerText = 'Rp ' + totalBarang.toLocaleString('id-ID');
            document.getElementById('displayGrandTotal').innerText = 'Rp ' + grandTotal.toLocaleString('id-ID');

            const bayarInput = document.getElementById('bayar');
            const bayar = parseFloat(bayarInput.value) || 0;
            const sisaPiutang = Math.max(0, grandTotal - bayar);

            document.getElementById('displaySisaPiutang').innerText = 'Rp ' + sisaPiutang.toLocaleString('id-ID');

            const badge = document.getElementById('displayStatusBadge');
            if (sisaPiutang <= 0 && grandTotal > 0) {
                badge.className = 'badge badge-lunas';
                badge.innerText = 'Lunas';
            } else if (bayar > 0) {
                badge.className = 'badge badge-tempo';
                badge.innerText = 'Tempo / Sebagian';
            } else {
                badge.className = 'badge badge-belum';
                badge.innerText = 'Belum Dibayar';
            }
        }

        function setPaymentQuick(type) {
            let totalBarang = 0;
            const container = document.getElementById('itemRowsContainer');
            const rows = container.querySelectorAll('tr');

            rows.forEach(row => {
                const idx = row.id.split('_')[1];
                const qty = parseFloat(document.getElementById('qty_' + idx)?.value) || 0;
                const price = parseFloat(document.getElementById('price_' + idx)?.value) || 0;
                totalBarang += (qty * price);
            });

            const ongkir = parseFloat(document.getElementById('ongkos_kirim').value) || 0;
            const diskon = parseFloat(document.getElementById('diskon').value) || 0;
            const grandTotal = Math.max(0, (totalBarang - diskon) + ongkir);

            const bayarInput = document.getElementById('bayar');
            if (type === 'full') {
                bayarInput.value = grandTotal;
            } else if (type === 'dp50') {
                bayarInput.value = Math.round(grandTotal * 0.5);
            } else if (type === 'zero') {
                bayarInput.value = 0;
            }

            calculateTotals();
        }

        function toggleCustomerManual(selectEl) {
            const selectedOpt = selectEl.options[selectEl.selectedIndex];
            const manualName = document.getElementById('nama_pelanggan_manual');
            const noHp = document.getElementById('no_hp_pelanggan');
            const alamat = document.getElementById('alamat_pengiriman');

            if (selectEl.value) {
                manualName.value = '';
                manualName.disabled = true;
                if (selectedOpt.dataset.hp) noHp.value = selectedOpt.dataset.hp;
                if (selectedOpt.dataset.alamat) alamat.value = selectedOpt.dataset.alamat;
            } else {
                manualName.disabled = false;
            }
        }

        // Tambah 1 baris awal saat halaman dibuka
        document.addEventListener('DOMContentLoaded', function () {
            addItemRow();
        });
    </script>
@endsection