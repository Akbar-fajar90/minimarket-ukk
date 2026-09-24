<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Voucher extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'kode', 'nama', 'tipe', 'nilai', 'minimal_belanja',
        'poin_dibutuhkan', 'kuota', 'terpakai',
        'tanggal_mulai', 'tanggal_berakhir', 'aktif',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_berakhir' => 'date',
        'aktif'           => 'boolean',
    ];

       public static function generateKodeVoucher(): string
    {
        $prefix = 'VCR-';

        $last = static::withTrashed()
            ->where('kode', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('kode');

        $lastNumber = $last ? (int) substr($last, -4) : 0;

        return $prefix . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }


    public function penjualans()
    {
        return $this->hasMany(Penjualan::class);
    }

    public function penukaranPoins()
    {
        return $this->hasMany(PenukaranPoin::class);
    }

    public function scopeAktif(Builder $query)
    {
        return $query->where('aktif', true)
            ->where(fn ($q) => $q->whereNull('tanggal_mulai')->orWhere('tanggal_mulai', '<=', now()))
            ->where(fn ($q) => $q->whereNull('tanggal_berakhir')->orWhere('tanggal_berakhir', '>=', now()))
            ->whereColumn('terpakai', '<', 'kuota');
    }

    public function getSisaKuotaAttribute(): int
    {
        return max(0, $this->kuota - $this->terpakai);
    }

    public function hitungDiskon(float $total): float
    {
        if ($total < $this->minimal_belanja) {
            return 0;
        }

        return $this->tipe === 'persen'
            ? $total * ($this->nilai / 100)
            : $this->nilai;
    }
}