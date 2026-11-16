<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Expense extends Model
{
    protected $fillable = [
        'no_pengeluaran',
        'tanggal',
        'expense_category_id',
        'supplier_id',
        'nama_keperluan',
        'nominal',
        'metode_pembayaran',
        'bukti_transaksi',
        'catatan',
        'user_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'float',
    ];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
