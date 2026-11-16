<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Pendataan Toko Genting
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Dashboard
Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

// Master Produk Genteng & Stok
Route::post('/products/{product}/adjust-stock', 'ProductController@adjustStock')->name('products.adjust_stock');
Route::resource('products', 'ProductController');

// Kategori Genteng
Route::resource('categories', 'CategoryController')->except(['create', 'show', 'edit']);

// Pelanggan & Supplier
Route::resource('customers', 'CustomerController');
Route::resource('suppliers', 'SupplierController');

// Transaksi Penjualan (Pemasukan) & Piutang
Route::get('/transactions/{transaction}/invoice', 'TransactionController@printInvoice')->name('transactions.invoice');
Route::get('/transactions/{transaction}/delivery-order', 'TransactionController@printDeliveryOrder')->name('transactions.delivery_order');
Route::post('/transactions/{transaction}/payment', 'TransactionController@addPayment')->name('transactions.payment');
Route::resource('transactions', 'TransactionController');

// Pengeluaran (Kas Keluar)
Route::resource('expenses', 'ExpenseController');
Route::resource('expense-categories', 'ExpenseCategoryController')->except(['create', 'show', 'edit']);

// Buku Kas & Laporan Keuangan
Route::get('/cashflows', 'CashflowController@index')->name('cashflows.index');
Route::get('/reports/profit-loss', 'CashflowController@profitAndLoss')->name('reports.profit_loss');
