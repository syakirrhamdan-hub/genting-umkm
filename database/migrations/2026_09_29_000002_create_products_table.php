<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('kode_produk')->unique();
            $table->string('nama_produk');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('satuan')->default('pcs'); // pcs, m2, box, kodi
            $table->decimal('harga_beli', 12, 2)->default(0); // HPP
            $table->decimal('harga_jual', 12, 2)->default(0);
            $table->integer('stok')->default(0);
            $table->integer('stok_minimum')->default(500); // genting per biji ratusan/ribuan
            $table->string('gambar')->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}
