<?php

namespace App\Filament\Widgets;

use App\Models\Penjualan;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalPendapatanOverview extends BaseWidget
{
    public ?string $dateFilter = 'all_time';
    public ?string $monthFilter = null;
    public ?string $yearFilter = null;
    public ?string $customDateFrom = null;
    public ?string $customDateTo = null;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('dateFilter')
                    ->label('Filter')
                    ->options([
                        'today' => 'Hari Ini',
                        'this_month' => 'Bulan Ini',
                        'this_year' => 'Tahun Ini',
                        'custom' => 'Custom Range',
                        'all_time' => 'Semua Waktu',
                    ])
                    ->default('all_time')
                    ->live()
                    ->afterStateUpdated(function (Set $set) {
                        $set('monthFilter', null);
                        $set('yearFilter', null);
                        $set('customDateFrom', null);
                        $set('customDateTo', null);
                    }),

                Select::make('monthFilter')
                    ->label('Bulan')
                    ->options(function () {
                        $months = [];
                        for ($i = 1; $i <= 12; $i++) {
                            $months[$i] = Carbon::create(null, $i, 1)->monthName;
                        }
                        return $months;
                    })
                    ->visible(fn (Get $get): bool => $get('dateFilter') === 'custom')
                    ->live()
                    ->afterStateUpdated(function (Set $set) {
                        $set('customDateFrom', null);
                        $set('customDateTo', null);
                    }),

                Select::make('yearFilter')
                    ->label('Tahun')
                    ->options(function () {
                        $years = [];
                        $currentYear = Carbon::now()->year;
                        for ($i = $currentYear - 5; $i <= $currentYear + 5; $i++) {
                            $years[$i] = $i;
                        }
                        return $years;
                    })
                    ->visible(fn (Get $get): bool => $get('dateFilter') === 'custom')
                    ->live()
                    ->afterStateUpdated(function (Set $set) {
                        $set('customDateFrom', null);
                        $set('customDateTo', null);
                    }),

                DatePicker::make('customDateFrom')
                    ->label('Dari Tanggal')
                    ->visible(fn (Get $get): bool => $get('dateFilter') === 'custom'
                        && ! $get('monthFilter')
                        && ! $get('yearFilter'))
                    ->live(),

                DatePicker::make('customDateTo')
                    ->label('Sampai Tanggal')
                    ->visible(fn (Get $get): bool => $get('dateFilter') === 'custom'
                        && ! $get('monthFilter')
                        && ! $get('yearFilter'))
                    ->live(),
            ]);
    }

    protected function getStats(): array
    {
        $query = Penjualan::query()->where('status', '!=', 'cancelled');

        if ($this->dateFilter === 'today') {
            $query->whereDate('tanggal_penjualan', Carbon::today());
        } elseif ($this->dateFilter === 'this_month') {
            $query->whereMonth('tanggal_penjualan', Carbon::now()->month)
                  ->whereYear('tanggal_penjualan', Carbon::now()->year);
        } elseif ($this->dateFilter === 'this_year') {
            $query->whereYear('tanggal_penjualan', Carbon::now()->year);
        } elseif ($this->dateFilter === 'custom') {
            if ($this->monthFilter) {
                $query->whereMonth('tanggal_penjualan', $this->monthFilter);
            }
            if ($this->yearFilter) {
                $query->whereYear('tanggal_penjualan', $this->yearFilter);
            }
            if ($this->customDateFrom) {
                $query->whereDate('tanggal_penjualan', '>=', $this->customDateFrom);
            }
            if ($this->customDateTo) {
                $query->whereDate('tanggal_penjualan', '<=', $this->customDateTo);
            }
        }

        $totalPendapatan = $query->sum('total_bayar');

        return [
            Stat::make('Total Pendapatan', 'Rp ' . number_format((float) $totalPendapatan, 0, ',', '.'))
                ->description('Total pendapatan dari penjualan')
                ->color('success'),
        ];
    }
}