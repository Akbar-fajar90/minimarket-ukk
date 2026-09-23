<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPenjualan extends Model
{
    protected $fillable = ['penjualan_id', 'produk_id', 'jumlah', 'harga', 'subtotal'];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }

    protected static function booted()
    {
        // Saat checkout (detail dibuat) → kurangi stok
        static::created(function (DetailPenjualan $detail) {
            $detail->produk?->decrement('stok', $detail->jumlah);
        });

        // Saat detail diubah → kembalikan stok lama, kurangi stok baru
        static::updated(function (DetailPenjualan $detail) {
            $oldProdukId = $detail->getOriginal('produk_id');
            $oldJumlah   = $detail->getOriginal('jumlah');

            if ($oldProdukId) {
                Produk::find($oldProdukId)?->increment('stok', $oldJumlah);
            }
            if ($detail->produk_id) {
                Produk::find($detail->produk_id)?->decrement('stok', $detail->jumlah);
            }
        });

        // Saat detail dihapus → kembalikan stok
        static::deleted(function (DetailPenjualan $detail) {
            $detail->produk?->increment('stok', $detail->jumlah);
        });
    }
}