<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'kode_pelanggan',
        'nama',
        'no_hp',
        'alamat',
        'keterangan',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getSisaPiutangAttribute()
    {
        return $this->transactions()->where('status_pembayaran', '!=', 'lunas')->sum('sisa_piutang');
    }
}
