<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('transaction_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('nama_produk_snapshot');
            $table->string('satuan_snapshot')->default('pcs');
            $table->decimal('harga_satuan', 12, 2)->default(0);
            $table->decimal('harga_beli_snapshot', 12, 2)->default(0);
            $table->integer('jumlah')->default(1);
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->timestamps();

            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transaction_details');
    }
}
