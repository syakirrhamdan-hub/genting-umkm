<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpensesTable extends Migration
{
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('no_pengeluaran')->unique();
            $table->date('tanggal');
            $table->unsignedBigInteger('expense_category_id')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->string('nama_keperluan');
            $table->decimal('nominal', 14, 2);
            $table->string('metode_pembayaran')->default('tunai');
            $table->string('bukti_transaksi')->nullable();
            $table->text('catatan')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->foreign('expense_category_id')->references('id')->on('expense_categories')->onDelete('set null');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('expenses');
    }
}
