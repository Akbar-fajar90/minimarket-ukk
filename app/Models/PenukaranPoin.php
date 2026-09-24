<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenukaranPoin extends Model
{
    protected $table = 'penukaran_poins';

    protected $fillable = [
        'pelanggan_id', 'penjualan_id', 'voucher_id',
        'tipe', 'jumlah', 'keterangan',
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }
}