<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $fillable = [
        'user_id', 'nama_pelanggan', 'no_telepon',
        'alamat', 'tanggal_penjualan', 'total_harga',
    ];

    public function details()
    {
        return $this->hasMany(DetailPenjualan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Hitung total_harga setiap disimpan
    protected static function booted()
    {
        static::saved(function (Penjualan $penjualan) {
            $total = $penjualan->details()->sum('subtotal');
            if ($penjualan->total_harga != $total) {
                $penjualan->updateQuietly(['total_harga' => $total]);
            }
        });
    }
}