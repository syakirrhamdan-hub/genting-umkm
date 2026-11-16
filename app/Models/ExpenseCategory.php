<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    protected $fillable = [
        'nama_kategori',
        'deskripsi',
    ];

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
