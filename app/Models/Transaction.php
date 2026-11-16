<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Transaction extends Model
{
    protected $fillable = [
        'no_faktur',
        'tanggal',
        'customer_id',
        'nama_pelanggan_manual',
        'no_hp_pelanggan',
        'total_barang',
        'ongkos_kirim',
        'diskon',
        'grand_total',
        'bayar',
        'sisa_piutang',
        'status_pembayaran',
        'metode_pembayaran',
        'status_pengiriman',
        'alamat_pengiriman',
        'supir_armada',
        'catatan',
        'user_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total_barang' => 'float',
        'ongkos_kirim' => 'float',
        'diskon' => 'float',
        'grand_total' => 'float',
        'bayar' => 'float',
        'sisa_piutang' => 'float',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function paymentReceipts()
    {
        return $this->hasMany(PaymentReceipt::class);
    }

    public function getNamaPelangganAttribute()
    {
        if ($this->customer) {
            return $this->customer->nama;
        }
        return $this->nama_pelanggan_manual ?: 'Pelanggan Umum';
    }

    public function getLabaKotorAttribute()
    {
        $totalHpp = $this->details->sum(function($item) {
            return $item->harga_beli_snapshot * $item->jumlah;
        });
        return ($this->total_barang - $this->diskon) - $totalHpp;
    }
}
