<?php

namespace App\Filament\Widgets;

use App\Models\Penjualan;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatistikPenjualan extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalTransaksi = Penjualan::where('status', '!=', 'cancelled')->count();

        $pendapatan = Penjualan::where('status', '!=', 'cancelled')->sum('total_harga');

        $transaksiHariIni = Penjualan::where('status', '!=', 'cancelled')->whereDate(
            'tanggal_penjualan',
            today()
        )->count();

        return [
            Stat::make(
                'Total Transaksi',
                number_format($totalTransaksi, 0, ',', '.')
            )
                ->description('Total seluruh transaksi')
                ->icon('heroicon-o-shopping-cart'),

            Stat::make(
                'Pendapatan',
                'Rp ' . number_format($pendapatan, 0, ',', '.')
            )
                ->description('Total pendapatan')
                ->icon('heroicon-o-banknotes'),

            Stat::make(
                'Transaksi Hari Ini',
                number_format($transaksiHariIni, 0, ',', '.')
            )
                ->description('Transaksi pada hari ini')
                ->icon('heroicon-o-calendar-days'),
        ];
    }
}