<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Penjualan;
use Illuminate\Support\Facades\Auth;

class PenjualanObserver
{
    public function created(Penjualan $penjualan): void
    {
        if (Auth::check()) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity_type' => 'Penjualan Dibuat',
                'description' => 'Penjualan ID #' . $penjualan->id . ' dibuat oleh ' . Auth::user()->name . ' dengan total Rp ' . number_format($penjualan->total_bayar, 0, ',', '.'),
            ]);
        }
    }

    public function updated(Penjualan $penjualan): void
    {
        if (Auth::check()) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity_type' => 'Penjualan Diperbarui',
                'description' => 'Penjualan ID #' . $penjualan->id . ' diperbarui oleh ' . Auth::user()->name,
            ]);
        }
    }

    public function deleted(Penjualan $penjualan): void
    {
        if (Auth::check()) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity_type' => 'Penjualan Dihapus',
                'description' => 'Penjualan ID #' . $penjualan->id . ' dihapus oleh ' . Auth::user()->name,
            ]);
        }
    }
}