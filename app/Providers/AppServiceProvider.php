<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentTimezone;
use App\Models\Penjualan;
use App\Models\Produk;

use App\Observers\PenjualanObserver;
use App\Observers\ProdukObserver;
use App\Observers\StatusObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }


    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FilamentTimezone::set('Asia/Jakarta');
        Penjualan::observe(PenjualanObserver::class);
        Produk::observe(ProdukObserver::class);
        Penjualan::observe(StatusObserver::class);
    }
}
