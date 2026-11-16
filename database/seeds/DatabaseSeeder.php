<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\ExpenseCategory;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\PaymentReceipt;
use App\Models\Expense;
use App\Models\Cashflow;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Create Default Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@genting.com'],
            [
                'name' => 'Pemilik Toko Genting',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'phone' => '081234567890'
            ]
        );

        $kasir = User::firstOrCreate(
            ['email' => 'kasir@genting.com'],
            [
                'name' => 'Kasir Toko Genting',
                'password' => Hash::make('kasir123'),
                'role' => 'kasir',
                'phone' => '089876543210'
            ]
        );

        // 2. Categories
        $categoriesData = [
            ['nama_kategori' => 'Genteng Morando & Glazur', 'slug' => 'genteng-morando', 'deskripsi' => 'Genteng lengkung berkualitas tinggi dengan opsi lapisan glazur mengkilap dan anti lumut.'],
            ['nama_kategori' => 'Genteng Mantili & Sokka', 'slug' => 'genteng-mantili', 'deskripsi' => 'Genteng press tanah liat khas Kebumen/Sokka dengan bobot kokoh dan presisi.'],
            ['nama_kategori' => 'Genteng Flat Beton', 'slug' => 'genteng-flat-beton', 'deskripsi' => 'Genteng beton minimalis modern untuk rumah bergaya kekinian.'],
            ['nama_kategori' => 'Genteng Keramik Berglazur', 'slug' => 'genteng-keramik', 'deskripsi' => 'Genteng keramik premium seperti Kanmuri dan KIA, tahan cuaca ekstrem.'],
            ['nama_kategori' => 'Nok & Wuwung Atap', 'slug' => 'nok-wuwung', 'deskripsi' => 'Aksesoris bubungan atap, nok segitiga, dan penutup pinggir atap genteng.'],
        ];

        $categories = [];
        foreach ($categoriesData as $c) {
            $categories[$c['slug']] = Category::firstOrCreate(['slug' => $c['slug']], $c);
        }

        // 3. Products
        $productsData = [
            [
                'kode_produk' => 'GNT-MRN-01',
                'nama_produk' => 'Genteng Morando Glazur Cokelat',
                'category_id' => $categories['genteng-morando']->id,
                'satuan' => 'pcs',
                'harga_beli' => 3200,
                'harga_jual' => 4500,
                'stok' => 8500,
                'stok_minimum' => 1000,
                'deskripsi' => 'Genteng morando berlapisan glazur anti lumut, warna cokelat mengkilap.'
            ],
            [
                'kode_produk' => 'GNT-MRN-02',
                'nama_produk' => 'Genteng Morando Natural Pres',
                'category_id' => $categories['genteng-morando']->id,
                'satuan' => 'pcs',
                'harga_beli' => 2400,
                'harga_jual' => 3300,
                'stok' => 12000,
                'stok_minimum' => 1500,
                'deskripsi' => 'Genteng morando tanah liat murni tanpa pewarna, pembakaran matang sempurna.'
            ],
            [
                'kode_produk' => 'GNT-MNT-01',
                'nama_produk' => 'Genteng Mantili Super Kebumen',
                'category_id' => $categories['genteng-mantili']->id,
                'satuan' => 'pcs',
                'harga_beli' => 1800,
                'harga_jual' => 2500,
                'stok' => 15000,
                'stok_minimum' => 2000,
                'deskripsi' => 'Genteng mantili asli Sokka Kebumen, tebal, awet, dan tahan bocor.'
            ],
            [
                'kode_produk' => 'GNT-BTN-01',
                'nama_produk' => 'Genteng Flat Beton Hitam Charcoal',
                'category_id' => $categories['genteng-flat-beton']->id,
                'satuan' => 'pcs',
                'harga_beli' => 6500,
                'harga_jual' => 9000,
                'stok' => 4200,
                'stok_minimum' => 800,
                'deskripsi' => 'Genteng beton flat untuk atap rumah tropis minimalis, finishing cat tahan UV.'
            ],
            [
                'kode_produk' => 'GNT-KRM-01',
                'nama_produk' => 'Genteng Keramik Kanmuri Espanica',
                'category_id' => $categories['genteng-keramik']->id,
                'satuan' => 'pcs',
                'harga_beli' => 10500,
                'harga_jual' => 14000,
                'stok' => 3000,
                'stok_minimum' => 500,
                'deskripsi' => 'Genteng keramik impor berkualitas tinggi tipe Espanica warna natural brown.'
            ],
            [
                'kode_produk' => 'GNT-NOK-01',
                'nama_produk' => 'Wuwung / Nok Morando Glazur',
                'category_id' => $categories['nok-wuwung']->id,
                'satuan' => 'pcs',
                'harga_beli' => 7000,
                'harga_jual' => 10000,
                'stok' => 650,
                'stok_minimum' => 100,
                'deskripsi' => 'Nok genteng morando glazur untuk puncak atap rumah.'
            ],
            [
                'kode_produk' => 'GNT-NOK-02',
                'nama_produk' => 'Wuwung / Nok Segitiga Tradisional',
                'category_id' => $categories['nok-wuwung']->id,
                'satuan' => 'pcs',
                'harga_beli' => 4500,
                'harga_jual' => 6500,
                'stok' => 450,
                'stok_minimum' => 100,
                'deskripsi' => 'Wuwung segitiga tanah liat untuk genteng mantili dan plentong.'
            ],
        ];

        $products = [];
        foreach ($productsData as $p) {
            $products[$p['kode_produk']] = Product::firstOrCreate(['kode_produk' => $p['kode_produk']], $p);
        }

        // 4. Customers
        $customersData = [
            [
                'kode_pelanggan' => 'PLG-001',
                'nama' => 'Bpk. Hendra Gunawan (CV Bangun Jaya Mandiri)',
                'no_hp' => '081234567890',
                'alamat' => 'Jl. Merdeka No. 45, Proyek Klaster Harmoni',
                'keterangan' => 'Kontraktor langganan perumahan, biasa ambil 5.000 - 15.000 pcs.'
            ],
            [
                'kode_pelanggan' => 'PLG-002',
                'nama' => 'Ibu Siti Rahmawati',
                'no_hp' => '085712345678',
                'alamat' => 'Perum Griya Kencana Blok C-12',
                'keterangan' => 'Renovasi atap rumah pribadi, minta pengiriman hari Sabtu.'
            ],
            [
                'kode_pelanggan' => 'PLG-003',
                'nama' => 'Bpk. Joko Priyono (Mandor Bangunan)',
                'no_hp' => '087890123456',
                'alamat' => 'Desa Sukamaju RT 03/RW 02',
                'keterangan' => 'Proyek masjid desa, pembayaran bertahap termin 2 kali.'
            ]
        ];

        $customers = [];
        foreach ($customersData as $c) {
            $customers[$c['kode_pelanggan']] = Customer::firstOrCreate(['kode_pelanggan' => $c['kode_pelanggan']], $c);
        }

        // 5. Suppliers
        $suppliersData = [
            [
                'kode_supplier' => 'SPL-001',
                'nama_supplier' => 'Sentra Pengrajin Genteng Sokka Maju',
                'nama_kontak' => 'H. Mahfud',
                'no_hp' => '081398765432',
                'alamat' => 'Sentra Industri Genteng Sokka, Kebumen',
                'keterangan' => 'Pemasok genteng mantili dan wuwung pres.'
            ],
            [
                'kode_supplier' => 'SPL-002',
                'nama_supplier' => 'Pabrik Genteng Jatiwangi Berkah',
                'nama_kontak' => 'Bpk. Ridwan',
                'no_hp' => '081223344556',
                'alamat' => 'Jatiwangi, Majalengka',
                'keterangan' => 'Pabrik spesialis genteng morando glazur kualitas super.'
            ],
            [
                'kode_supplier' => 'SPL-003',
                'nama_supplier' => 'PT Surya Flat Roof Concrete',
                'nama_kontak' => 'Ibu Diana',
                'no_hp' => '082155667788',
                'alamat' => 'Kawasan Industri Cikarang',
                'keterangan' => 'Distributor genteng beton flat & keramik Kanmuri.'
            ]
        ];

        $suppliers = [];
        foreach ($suppliersData as $s) {
            $suppliers[$s['kode_supplier']] = Supplier::firstOrCreate(['kode_supplier' => $s['kode_supplier']], $s);
        }

        // 6. Expense Categories
        $expCats = [
            ['nama_kategori' => 'Kulakan Stok Genteng', 'deskripsi' => 'Pembelian stok genting dan wuwung dari pabrik/pengrajin.'],
            ['nama_kategori' => 'Upah Kuli Bongkar Muat', 'deskripsi' => 'Ongkos tenaga angkut saat muat ke truk kirim atau bongkar kulakan.'],
            ['nama_kategori' => 'BBM & Tol Armada Truk', 'deskripsi' => 'Solar truk/pickup dan biaya tol saat antar pesanan ke proyek.'],
            ['nama_kategori' => 'Servis & Perawatan Truk', 'deskripsi' => 'Ganti oli, ban truk, dan perbaikan kendaraan operasional.'],
            ['nama_kategori' => 'Gaji & Uang Makan Pekerja', 'deskripsi' => 'Gaji mingguan/bulanan supir, admin, dan uang makan kuli.'],
            ['nama_kategori' => 'Listrik, Air & Toko', 'deskripsi' => 'Tagihan PLN, PDAM, pulsa, dan ATK faktur.'],
            ['nama_kategori' => 'Lain-lain / Tak Terduga', 'deskripsi' => 'Biaya tambal ban, terpal genteng, retribusi keamanan, dll.'],
        ];

        $expenseCategories = [];
        foreach ($expCats as $ec) {
            $expenseCategories[$ec['nama_kategori']] = ExpenseCategory::firstOrCreate(['nama_kategori' => $ec['nama_kategori']], $ec);
        }

        // 7. Seed sample sales transactions (Pemasukan)
        // Transaction 1: Lunas Cash - Bpk Hendra
        $t1 = Transaction::firstOrCreate(
            ['no_faktur' => 'INV-20260920-001'],
            [
                'tanggal' => '2026-09-20',
                'customer_id' => $customers['PLG-001']->id,
                'total_barang' => 13500000, // 3000 pcs Morando Glazur @ 4500
                'ongkos_kirim' => 450000,
                'diskon' => 0,
                'grand_total' => 13950000,
                'bayar' => 13950000,
                'sisa_piutang' => 0,
                'status_pembayaran' => 'lunas',
                'metode_pembayaran' => 'transfer',
                'status_pengiriman' => 'terkirim',
                'alamat_pengiriman' => 'Jl. Merdeka No. 45, Proyek Klaster Harmoni',
                'supir_armada' => 'Truk Colt Diesel B 9421 CD - Pak Joko',
                'catatan' => 'Lunas via transfer BCA. Kirim pagi pukul 08.00 WIB.',
                'user_id' => $admin->id
            ]
        );

        TransactionDetail::firstOrCreate(
            ['transaction_id' => $t1->id, 'product_id' => $products['GNT-MRN-01']->id],
            [
                'nama_produk_snapshot' => $products['GNT-MRN-01']->nama_produk,
                'satuan_snapshot' => 'pcs',
                'harga_satuan' => 4500,
                'harga_beli_snapshot' => 3200,
                'jumlah' => 3000,
                'subtotal' => 13500000
            ]
        );

        Cashflow::firstOrCreate(
            ['referensi_tabel' => 'transactions', 'referensi_id' => $t1->id],
            [
                'tanggal' => '2026-09-20',
                'jenis' => 'masuk',
                'kategori' => 'Penjualan Genteng',
                'nominal' => 13950000,
                'keterangan' => 'Penjualan Faktur INV-20260920-001 - CV Bangun Jaya Mandiri (3000 pcs Morando Glazur + Ongkir)',
                'user_id' => $admin->id
            ]
        );

        // Transaction 2: Piutang / Tempo - Bpk Joko Priyono (DP 5 Juta, Sisa 2.75 Juta)
        $t2 = Transaction::firstOrCreate(
            ['no_faktur' => 'INV-20260925-002'],
            [
                'tanggal' => '2026-09-25',
                'customer_id' => $customers['PLG-003']->id,
                'total_barang' => 7500000, // 3000 pcs Mantili @ 2500
                'ongkos_kirim' => 250000,
                'diskon' => 0,
                'grand_total' => 7750000,
                'bayar' => 5000000,
                'sisa_piutang' => 2750000,
                'status_pembayaran' => 'tempo',
                'metode_pembayaran' => 'tunai',
                'status_pengiriman' => 'terkirim',
                'alamat_pengiriman' => 'Desa Sukamaju RT 03/RW 02, Proyek Masjid',
                'supir_armada' => 'Pick-up L300 G 8234 AB - Pak slamet',
                'catatan' => 'Uang muka DP Rp 5.000.000. Pelunasan saat atap selesai terpasang.',
                'user_id' => $kasir->id
            ]
        );

        TransactionDetail::firstOrCreate(
            ['transaction_id' => $t2->id, 'product_id' => $products['GNT-MNT-01']->id],
            [
                'nama_produk_snapshot' => $products['GNT-MNT-01']->nama_produk,
                'satuan_snapshot' => 'pcs',
                'harga_satuan' => 2500,
                'harga_beli_snapshot' => 1800,
                'jumlah' => 3000,
                'subtotal' => 7500000
            ]
        );

        Cashflow::firstOrCreate(
            ['referensi_tabel' => 'transactions', 'referensi_id' => $t2->id],
            [
                'tanggal' => '2026-09-25',
                'jenis' => 'masuk',
                'kategori' => 'Penjualan Genteng (DP)',
                'nominal' => 5000000,
                'keterangan' => 'Pembayaran Uang Muka INV-20260925-002 - Bpk. Joko Priyono',
                'user_id' => $kasir->id
            ]
        );

        // 8. Seed sample expenses (Pengeluaran)
        $exp1 = Expense::firstOrCreate(
            ['no_pengeluaran' => 'EXP-20260921-001'],
            [
                'tanggal' => '2026-09-21',
                'expense_category_id' => $expenseCategories['Upah Kuli Bongkar Muat']->id,
                'nama_keperluan' => 'Upah Bongkar Muat Genteng Morando 3.000 pcs ke Klaster Harmoni',
                'nominal' => 250000,
                'metode_pembayaran' => 'tunai',
                'catatan' => '2 orang kuli angkut (@ Rp 125.000)',
                'user_id' => $admin->id
            ]
        );

        Cashflow::firstOrCreate(
            ['referensi_tabel' => 'expenses', 'referensi_id' => $exp1->id],
            [
                'tanggal' => '2026-09-21',
                'jenis' => 'keluar',
                'kategori' => 'Upah Kuli Bongkar Muat',
                'nominal' => 250000,
                'keterangan' => 'EXP-20260921-001: Upah Bongkar Muat Genteng Morando 3.000 pcs',
                'user_id' => $admin->id
            ]
        );

        $exp2 = Expense::firstOrCreate(
            ['no_pengeluaran' => 'EXP-20260922-002'],
            [
                'tanggal' => '2026-09-22',
                'expense_category_id' => $expenseCategories['BBM & Tol Armada Truk']->id,
                'nama_keperluan' => 'Isi Solar Truk Colt Diesel 35 Liter + Tol',
                'nominal' => 315000,
                'metode_pembayaran' => 'tunai',
                'catatan' => 'Pengiriman genteng proyek Klaster Harmoni & Sukamaju',
                'user_id' => $admin->id
            ]
        );

        Cashflow::firstOrCreate(
            ['referensi_tabel' => 'expenses', 'referensi_id' => $exp2->id],
            [
                'tanggal' => '2026-09-22',
                'jenis' => 'keluar',
                'kategori' => 'BBM & Tol Armada Truk',
                'nominal' => 315000,
                'keterangan' => 'EXP-20260922-002: Solar Truk Colt Diesel 35L',
                'user_id' => $admin->id
            ]
        );

        $exp3 = Expense::firstOrCreate(
            ['no_pengeluaran' => 'EXP-20260924-003'],
            [
                'tanggal' => '2026-09-24',
                'expense_category_id' => $expenseCategories['Kulakan Stok Genteng']->id,
                'supplier_id' => $suppliers['SPL-002']->id,
                'nama_keperluan' => 'Kulakan Tambahan Stok Genteng Morando Glazur 2.000 pcs',
                'nominal' => 6400000,
                'metode_pembayaran' => 'transfer',
                'catatan' => 'Transfer pabrik Jatiwangi Majalengka (2.000 pcs x Rp 3.200)',
                'user_id' => $admin->id
            ]
        );

        Cashflow::firstOrCreate(
            ['referensi_tabel' => 'expenses', 'referensi_id' => $exp3->id],
            [
                'tanggal' => '2026-09-24',
                'jenis' => 'keluar',
                'kategori' => 'Kulakan Stok Genteng',
                'nominal' => 6400000,
                'keterangan' => 'EXP-20260924-003: Kulakan Genteng Morando Glazur Pabrik Jatiwangi',
                'user_id' => $admin->id
            ]
        );
    }
}
