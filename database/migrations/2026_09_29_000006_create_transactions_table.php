<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('no_faktur')->unique();
            $table->date('tanggal');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('nama_pelanggan_manual')->nullable();
            $table->string('no_hp_pelanggan')->nullable();
            $table->decimal('total_barang', 14, 2)->default(0);
            $table->decimal('ongkos_kirim', 14, 2)->default(0);
            $table->decimal('diskon', 14, 2)->default(0);
            $table->decimal('grand_total', 14, 2)->default(0);
            $table->decimal('bayar', 14, 2)->default(0);
            $table->decimal('sisa_piutang', 14, 2)->default(0);
            $table->string('status_pembayaran')->default('lunas'); // lunas, tempo, belum_lunas
            $table->string('metode_pembayaran')->default('tunai'); // tunai, transfer
            $table->string('status_pengiriman')->default('ambil_sendiri'); // ambil_sendiri, menunggu_kirim, proses_kirim, terkirim
            $table->text('alamat_pengiriman')->nullable();
            $table->string('supir_armada')->nullable();
            $table->text('catatan')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
