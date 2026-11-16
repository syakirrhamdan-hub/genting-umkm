<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    protected $fillable = [
        'transaction_id',
        'product_id',
        'nama_produk_snapshot',
        'satuan_snapshot',
        'harga_satuan',
        'harga_beli_snapshot',
        'jumlah',
        'subtotal',
    ];

    protected $casts = [
        'harga_satuan' => 'float',
        'harga_beli_snapshot' => 'float',
        'jumlah' => 'integer',
        'subtotal' => 'float',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
