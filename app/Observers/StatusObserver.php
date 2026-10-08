<?php

namespace App\Observers;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StatusObserver
{
    public function updating(Penjualan $penjualan): void
    {
        if ($penjualan->isDirty('status')
            && $penjualan->getOriginal('status') !== 'cancelled'
            && $penjualan->status === 'cancelled') {

            DB::transaction(function () use ($penjualan) {
                foreach ($penjualan->details as $detail) {
                    $produk = Produk::find($detail->produk_id);
                    $produk?->increment('stok', $detail->jumlah);
                }

                $penjualan->details()->update(['status' => 'cancelled']);

                ActivityLog::create([
                    'user_id' => Auth::id() ?: 0,
                    'activity_type' => 'Penjualan Dibatalkan',
                    'description' => 'Penjualan #' . $penjualan->id . ' dibatalkan dan stok dikembalikan.',
                ]);
            });
        }
    }
}
