<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class PaymentReceipt extends Model
{
    protected $fillable = [
        'transaction_id',
        'no_kwitansi',
        'tanggal',
        'nominal',
        'metode_pembayaran',
        'catatan',
        'user_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'float',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
