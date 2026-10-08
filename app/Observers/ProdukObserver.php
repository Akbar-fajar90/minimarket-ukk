<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Produk;
use Illuminate\Support\Facades\Auth;

class ProdukObserver
{
    public function created(Produk $produk): void
    {
        if (Auth::check()) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity_type' => 'Produk Dibuat',
                'description' => 'Produk ' . $produk->nama_produk . ' (ID #' . $produk->id . ') dibuat oleh ' . Auth::user()->name,
            ]);
        }
    }

    public function updated(Produk $produk): void
    {
        if (Auth::check()) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity_type' => 'Produk Diperbarui',
                'description' => 'Produk ' . $produk->nama_produk . ' (ID #' . $produk->id . ') diperbarui oleh ' . Auth::user()->name,
            ]);
        }
    }

    public function deleted(Produk $produk): void
    {
        if (Auth::check()) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity_type' => 'Produk Dihapus',
                'description' => 'Produk ' . $produk->nama_produk . ' (ID #' . $produk->id . ') dihapus oleh ' . Auth::user()->name,
            ]);
        }
    }
}