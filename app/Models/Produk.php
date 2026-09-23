<?php

namespace App\Models;
use App\Models\Kategori;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produks';
    protected $primaryKey = 'id';
    protected $fillable = ['nama', 'harga', 'stok', 'kategori', 'gambar'];

    public function detailPenjualans()
    {
        return $this->hasMany(DetailPenjualan::class);
    }
        public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }
}
