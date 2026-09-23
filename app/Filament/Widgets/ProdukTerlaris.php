<?php

namespace App\Filament\Widgets;

use App\Models\Produk;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class ProdukTerlaris extends TableWidget
{
    protected static ?string $heading = 'Produk Terlaris';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn (): Builder => Produk::query()
                    ->select('produks.*')
                    ->selectSub(
                        function ($query) {
                            $query->from('detail_penjualans')
                                ->selectRaw('COALESCE(SUM(jumlah), 0)')
                                ->whereColumn(
                                    'detail_penjualans.produk_id',
                                    'produks.id'
                                );
                        },
                        'total_terjual'
                    )
                    ->orderByDesc('total_terjual')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('nama')
                    ->label('Produk')
                    ->searchable(),

                TextColumn::make('total_terjual')
                    ->label('Terjual')
                    ->suffix(' pcs')
                    ->sortable(),
            ]);
    }
}