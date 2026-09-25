<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $fillable = [
    'user_id', 'pelanggan_id', 'voucher_id',
    'nama_pelanggan', 'no_telepon', 'alamat',
    'tanggal_penjualan',
    'total_harga', 'diskon', 'total_bayar',
];

    public function details()
    {
        return $this->hasMany(DetailPenjualan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

        public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    public function penukaranPoins()
    {
        return $this->hasMany(PenukaranPoin::class);
    }

    // Hitung total_harga setiap disimpan dan menambahkan nama customer walk-in ke Database
    protected static function booted()
    {
        static::created(function (Penjualan $penjualan) {
            if (empty($penjualan->pelanggan_id) && empty($penjualan->nama_pelanggan)) {
            $penjualan->updateQuietly([
            'nama_pelanggan' => 'cust-' . str_pad($penjualan->id, 4, '0', STR_PAD_LEFT),
            ]);
    }
        });
        static::saved(function (Penjualan $penjualan) {
            $total = $penjualan->details()->sum('subtotal');
            $diskon = $penjualan->voucher
                ? $penjualan->voucher->hitungDiskon($total)
                : 0;

            $penjualan->updateQuietly([
                'total_harga' => $total,
                'diskon'      => $diskon,
                'total_bayar' => max(0, $total - $diskon),
            ]);
        });
    }
}