# BLUEPRINT SISTEM INFORMASI MANAJEMEN & KASIR UMKM TOKO GENTENG
## Proyek: **Genting Jaya** (Point of Sale, Inventory & Cashflow Management System)

---

## 1. Executive Summary & Latar Belakang

### 1.1 Profil Bisnis UMKM Toko Genteng
Usaha dagang genteng dan material atap memiliki karakteristik operasional yang khas dibandingkan ritel umum:
- **Volume dan Satuan Besar:** Transaksi melibatkan ribuan keping genteng (satuan *pcs*, *m²*, *kodi*, atau *box*), membutuhkan pengelolaan stok presisi dengan ambang batas minimum yang tinggi (ratusan hingga ribuan pcs).
- **Logistik & Pengiriman:** Barang berbobot berat dan rawan pecah sehingga membutuhkan armada truk/pikap, pencatatan supir, ongkos kirim terpisah, serta dokumen fisik legal berupa **Surat Jalan (Delivery Order)**.
- **Model Pembayaran Bertahap (Piutang & Tempo):** Mayoritas pembeli (kontraktor, pemborong, maupun pemilik rumah) membayar dengan uang muka (DP) dan sisa dilunasi saat barang tiba atau dalam termin tempo tertentu.
- **Fluktuasi HPP (Harga Pokok Penjualan):** Pembelian genteng dari pengrajin/pabrik dapat berfluktuasi seiring musim cetak dan harga bahan bakar pembakaran tanah liat.

### 1.2 Tujuan Sistem
Sistem **Genting Jaya** dibangun untuk mengotomatisasi seluruh siklus bisnis toko genteng:
1. Menghilangkan risiko selisih stok genteng secara *real-time* saat transaksi penjualan terjadi.
2. Mengontrol piutang pelanggan melalui sistem pencatatan pelunasan bertahap dan penerbitan kwitansi otomatis.
3. Mengunci HPP pada saat transaksi (*snapshot value*) agar kalkulasi Laba Kotor dan Laba Bersih historis tidak terdistorsi jika harga beli master produk berubah di kemudian hari.
4. Menyediakan pembukuan kas masuk & keluar secara otomatis tanpa perlu pencatatan manual ganda (*single source of truth*).

---

## 2. Arsitektur Sistem & Spesifikasi Teknologi

### 2.1 Pilihan Arsitektur
Sistem mengadopsi pola **Monolithic MVC (Model-View-Controller)** yang terintegrasi penuh untuk memastikan kemudahan perawatan (*maintainability*), performa tinggi di lingkungan lokal/hosting UMKM, serta latensi rendah untuk operasional kasir harian.

```
+-------------------------------------------------------------------+
|               Presentation Layer (Client Browser)                 |
|   Blade Views (Bootstrap 5.3.3 + Icons) | Chart.js | Print Sheets  |
+-------------------------------------------------------------------+
                                  ^
                                  | HTTP / HTTPS
                                  v
+-------------------------------------------------------------------+
|               Application Layer (Laravel 7.x Engine)               |
| Routing | Auth & Session | Controllers | DB::transaction Atomicity|
+-------------------------------------------------------------------+
                                  ^
                                  | Eloquent ORM
                                  v
+-------------------------------------------------------------------+
|               Persistence Layer (Database & Storage)              |
|        MySQL / MariaDB RDBMS (ACID Compliant) | Local Storage      |
+-------------------------------------------------------------------+
```

### 2.2 Spesifikasi Teknologi
| Komponen | Spesifikasi / Library | Keterangan |
| :--- | :--- | :--- |
| **Backend Framework** | PHP 7.4 / 8.0, Laravel Framework 7.29 | Framework stabil, reliabel, didukung ekosistem ORM Eloquent yang kuat |
| **Database** | MySQL 5.7+ / MariaDB 10.4+ | RDBMS transaksional dengan dukungan *foreign keys* & *ACID compliance* |
| **Frontend Framework** | Bootstrap 5.3.3 + Bootstrap Icons 1.11.3 | UI responsif, modern, ramah perangkat tablet kasir dan desktop |
| **Tipografi & Desain** | Google Fonts *Plus Jakarta Sans* | Tipografi formal & modern dengan palet warna Terracotta (`#c2410c`) & Slate (`#0f172a`) |
| **Visualisasi Data** | Chart.js 4.x | Grafik interaktif arus kas masuk & keluar bulanan |
| **Audit & Transaksi** | `DB::transaction()` & Snapshot Pattern | Menjamin atomisitas pencatatan barang, kas, stok, dan nilai historis |

---

## 3. Arsitektur Basis Data & Entity Relationship Diagram (ERD)

### 3.1 Skema Relasi Antar Tabel
- **Users** $\rightarrow$ Mencatat seluruh aktivitas pembuatan transaksi (`transactions`), kwitansi (`payment_receipts`), pengeluaran (`expenses`), dan kas (`cashflows`).
- **Categories** $\rightarrow$ Mengklasifikasikan genteng (`products`).
- **Customers** $\rightarrow$ Relasi 1-to-N ke `transactions`.
- **Suppliers** $\rightarrow$ Relasi 1-to-N ke `expenses` (pembelian stok/material).
- **Transactions** $\rightarrow$ Terdiri dari banyak `transaction_details` dan `payment_receipts`.
- **Cashflows** $\rightarrow$ Log keuangan otomatis dari penjualan langsung, pelunasan kwitansi piutang, maupun pengeluaran operasional.

### 3.2 Strategi Snapshot Data Finansial
Salah satu pilar integritas data pada sistem ini adalah **Snapshot Pattern**:
- Pada tabel `transaction_details`, sistem merekam `nama_produk_snapshot`, `satuan_snapshot`, dan `harga_beli_snapshot` saat transaksi dibuat.
- **Tujuan:** Ketika harga beli/modal produk di masa depan naik dari Rp1.800 menjadi Rp2.200 per pcs, laporan laba-rugi transaksi masa lalu tetap akurat menghitung laba berdasarkan modal riil saat genteng tersebut terjual.

---

## 4. Dekomposisi Modul & Fungsionalitas Sistem

```
Genting Jaya System
├── 1. Modul Dashboard & Analitika
├── 2. Modul Master Data Barang (Genteng & Aksesoris)
├── 3. Modul Relasi Bisnis (Pelanggan & Pemasok)
├── 4. Modul Kasir & Transaksi Penjualan (POS)
├── 5. Modul Piutang & Penerimaan Pembayaran
├── 6. Modul Logistik & Pengiriman (Surat Jalan)
├── 7. Modul Pengeluaran Operasional Toko
└── 8. Modul Buku Kas & Laporan Laba Rugi
```

### 4.1 Modul Dashboard & Analitika Eksekutif
- **Kartu Ringkasan Metrik (KPIs):**
  - Saldo Kas Berjalan (Total Kas Masuk dikurangi Total Kas Keluar).
  - Kas Masuk Bulan Ini & Kas Keluar Bulan Ini.
  - Penjualan Hari Ini & Total Nominal Piutang Belum Tertagih.
  - Jumlah keping genteng terjual dalam bulan berjalan.
- **Early Warning System (Stok Menipis):** Daftar genteng dengan kondisi `stok <= stok_minimum` untuk pencegahan kehabisan persediaan (*stockout*).
- **Grafik Tren Arus Kas 6 Bulan:** Menampilkan perbandingan performa *cash-in* vs *cash-out* untuk analisa likuiditas usaha.

### 4.2 Modul Master Data Produk & Stok
- **Penomoran Otomatis:** Format kode barang `GNT-001`, `GNT-002`, dst.
- **Kategorisasi Produk:** Genteng Tanah Liat (Morando, Mantili, Plentong), Genteng Beton/Keramik, Wuwung/Kerpus, Cat Genteng, dan Aksesoris.
- **Manajemen Harga & Margin:** Input Harga Beli (HPP) dan Harga Jual dengan kalkulasi margin keuntungan otomatis.
- **Penyesuaian Stok (Stock Adjustment):** Modul penambahan/pengurangan stok fisik (misal: genteng pecah saat bongkar muat atau barang retur) dengan alasan terdokumentasi.

### 4.3 Modul Relasi Bisnis (Customer & Supplier)
- **Pelanggan (Customer):** Format kode `PLG-xxx`, data kontak, alamat domisili/proyek, dan buku riwayat seluruh transaksi & piutang per pelanggan. Mendukung pelanggan manual tanpa simpan akun (*guest customer*).
- **Pemasok (Supplier / Pengrajin Genteng):** Format kode `SPL-xxx`, pencatatan data pengrajin tempat kulakan genteng dan relasi ke beban operasional.

### 4.4 Modul Transaksi Penjualan & POS
- **Penomoran Faktur Dinamis:** Format `INV-YYYYMMDD-XXX` (tercantum tanggal dan nomor urut harian otomatis).
- **Multi-Item Entry:** Penambahan produk fleksibel dengan validasi stok instan (*real-time stock availability check*).
- **Kalkulasi Biaya:** Mendukung potongan harga (Diskon) dan tambahan biaya transportasi (Ongkos Kirim).
- **Kondisi Pembayaran Terbuka:**
  - `lunas`: Pembayaran sama atau melebihi total tagihan.
  - `tempo`: Pembayaran uang muka (DP) dengan sisa otomatis menjadi piutang.
  - `belum_lunas`: Pengambilan tanpa pembayaran awal (piutang 100%).
- **Pengurangan Stok Otomatis:** Pemotongan stok langsung pada tabel `products` saat transaksi di-commit.

### 4.5 Modul Manajemen Piutang & Kwitansi
- **Pelunasan Bertahap:** Mendukung cicilan piutang tanpa batas hingga sisa piutang mencapai Rp0.
- **Nomor Kwitansi Otomatis:** Format `KWT-YYYYMMDD-XXX`.
- **Auto Status Update:** Perubahan otomatis dari status `tempo` atau `belum_lunas` menjadi `lunas` ketika sisa piutang habis.
- **Auto Cashflow:** Setiap pembayaran cicilan langsung terintegrasi menjadi penerimaan kas masuk (`jenis = masuk`, `kategori = Pelunasan Piutang`).

### 4.6 Modul Logistik & Cetak Dokumen Resmi
- **Faktur Penjualan (Invoice):** Desain lembar kerja A4 profesional dengan identitas toko, rincian produk, ongkir, diskon, histori pembayaran/DP, dan syarat garansi.
- **Surat Jalan (Delivery Order):** Dokumen legal pengiriman tanpa mencantumkan harga, difokuskan pada kuantitas fisik yang dikirim, tujuan alamat, nama armada/supir, dan kolom tanda tangan (Pengirim, Supir, Penerima Barang).

### 4.7 Modul Pengeluaran Operasional
- **Penomoran Pengeluaran:** Format `EXP-YYYYMMDD-XXX`.
- **Kategori Fleksibel:** Gaji & Upah Tenaga Kerja/Bongkar Muat, Solar & Operasional Truk, Perawatan Mesin/Armada, Listrik & Air, Pembelian Bahan/Kulakan.
- **Upload Bukti Transaksi:** Penyimpanan foto/nota fisik pengeluaran.

### 4.8 Modul Buku Kas & Laporan Laba Rugi
- **Buku Kas Umum:** Pencatatan arus kas berbasis tanggal dengan penarikan **Saldo Awal Periode**, total mutasi masuk, total mutasi keluar, dan **Saldo Akhir Berjalan**.
- **Laporan Laba Rugi Periodik:**
  - $\text{Penjualan Bersih} = \text{Total Penjualan Barang} - \text{Diskon}$
  - $\text{Laba Kotor} = \text{Penjualan Bersih} - \text{HPP Genteng Terjual}$
  - $\text{Laba Bersih} = \text{Laba Kotor} + \text{Pendapatan Ongkir} - \text{Total Beban Operasional}$

---

## 5. Standar Keamanan & Integritas Data

1. **Transactional Atomicity (ACID):**
   Seluruh operasi penulisan berganda (seperti Transaksi Penjualan yang menyentuh tabel `transactions`, `transaction_details`, `products`, dan `cashflows`) dibungkus dalam blok `DB::transaction()`. Jika salah satu gagal, seluruh operasi di-*rollback* untuk mencegah korupsi data kas atau stok.
2. **Audit Trail Pengguna:**
   Setiap mutasi kas, pengeluaran, faktur, dan penerimaan kwitansi menyimpan referensi `user_id` operator/kasir yang mengeksekusi operasi tersebut.
3. **Pemberian Kode Unik Terstruktur:**
   Sistem menerapkan penomoran berbasis tanggal dan sekuensial harian untuk mencegah tabrakan data:
   - Invoice: `INV-{YYYYMMDD}-{001}`
   - Kwitansi: `KWT-{YYYYMMDD}-{001}`
   - Pengeluaran: `EXP-{YYYYMMDD}-{001}`
   - Kode Master: `GNT-{001}`, `PLG-{001}`, `SPL-{001}`

---

## 6. Roadmap Pengembangan & Fitur Masa Depan

| Fase | Inisiatif Fitur | Dampak Bisnis |
| :--- | :--- | :--- |
| **Fase 1 (Segera)** | **Export PDF & Excel:** Integrasi dompdf & excel untuk seluruh laporan kas, laba rugi, dan rekapan piutang. | Memudahkan rekap bulanan ke akuntan / pemilik tanpa membuka laptop kasir. |
| **Fase 2** | **WhatsApp Notification Gateway:** Notifikasi otomatis pengiriman faktur PDF dan pengingat jatuh tempo piutang via WhatsApp API. | Mempercepat perputaran kas (*cash conversion cycle*) dan menekan piutang macet. |
| **Fase 3** | **Manajemen Multi-Gudang (Multi-Warehouse):** Pelacakan stok per pangkalan (Gudang Utama, Lapangan Produksi Pengrajin, Cabang Distribusi). | Cocok jika UMKM memiliki lebih dari satu lokasi penyimpanan genteng. |
| **Fase 4** | **QR Code Validasi Dokumen:** Penambahan dynamic QR code pada Invoice dan Surat Jalan untuk verifikasi keaslian dokumen oleh supir dan pelanggan di lapangan. | Mencegah pemalsuan surat jalan saat armada melakukan pengiriman ke luar kota. |
