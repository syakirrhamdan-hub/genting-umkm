<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'kode_produk',
        'nama_produk',
        'category_id',
        'satuan',
        'harga_beli',
        'harga_jual',
        'stok',
        'stok_minimum',
        'gambar',
        'deskripsi',
    ];

    protected $casts = [
        'harga_beli' => 'float',
        'harga_jual' => 'float',
        'stok' => 'integer',
        'stok_minimum' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function getMarginAttribute()
    {
        return $this->harga_jual - $this->harga_beli;
    }

    public function getIsLowStockAttribute()
    {
        return $this->stok <= $this->stok_minimum;
    }
}
