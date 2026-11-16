<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'kode_supplier',
        'nama_supplier',
        'nama_kontak',
        'no_hp',
        'alamat',
        'keterangan',
    ];

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
