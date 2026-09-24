<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Pelanggan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'kode_member', 'nama', 'no_telepon', 'email',
        'alamat', 'poin', 'status', 'tanggal_bergabung', 'user_id',
    ];

    public static function generateKodeMember(): string
    {
        $prefix = 'MBR-';

        $last = static::withTrashed()
            ->where('kode_member', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('kode_member');

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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeAktif(Builder $query)
    {
        return $query->where('status', 'Aktif');
    }

    public function tambahPoin(int $jumlah, ?Penjualan $penjualan = null, ?string $keterangan = null): void
    {
        $this->increment('poin', $jumlah);

        $this->penukaranPoins()->create([
            'penjualan_id' => $penjualan?->id,
            'tipe'         => 'masuk',
            'jumlah'       => $jumlah,
            'keterangan'   => $keterangan ?? 'Poin dari transaksi',
        ]);
    }

    public function kurangiPoin(int $jumlah, ?Voucher $voucher = null, ?string $keterangan = null): void
    {
        $this->decrement('poin', $jumlah);

        $this->penukaranPoins()->create([
            'voucher_id' => $voucher?->id,
            'tipe'       => 'keluar',
            'jumlah'     => $jumlah,
            'keterangan' => $keterangan ?? 'Penukaran poin dengan voucher',
        ]);
    }
}