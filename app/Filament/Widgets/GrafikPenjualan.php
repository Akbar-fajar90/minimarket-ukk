<?php

namespace App\Filament\Widgets;

use App\Models\Penjualan;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class GrafikPenjualan extends ChartWidget
{
    protected ?string $heading = 'Grafik Penjualan';

    protected function getData(): array
    {
        $labels = [];
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $tanggal = Carbon::today()->subDays($i);

            $labels[] = $tanggal->translatedFormat('D');

            $data[] = Penjualan::whereDate(
                'tanggal_penjualan',
                $tanggal
            )->sum('total_harga');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan',
                    'data' => $data,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}