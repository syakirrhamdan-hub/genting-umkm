<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Cashflow extends Model
{
    protected $fillable = [
        'tanggal',
        'jenis',
        'kategori',
        'nominal',
        'referensi_tabel',
        'referensi_id',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
