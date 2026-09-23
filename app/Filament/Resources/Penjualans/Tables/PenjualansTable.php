<?php

namespace App\Filament\Resources\Penjualans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PenjualansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('nama_pelanggan')
                    ->searchable(),
                TextColumn::make('no_telepon')
                    ->searchable(),
                TextColumn::make('tanggal_penjualan')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('total_harga')
                    ->label('Total Bayar')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('user.name')->label('Kasir'),
                TextColumn::make('details_sum_subtotal')
                    ->label('Cek Subtotal')
                    ->money('IDR', locale: 'id')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
