<?php

namespace App\Filament\Resources\Penjualans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
// use App\Filament\Exports\PenjualanExporter;
// use Filament\Actions\ExportAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class PenjualansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Kasir')
                    ->sortable(),

                TextColumn::make('pelanggan.nama')
                    ->label('Pelanggan')
                    ->default(fn ($record) => $record->nama_pelanggan ?? 'Walk-in')
                    ->searchable()
                    ->description(fn ($record) => $record->no_telepon ?: null),

                TextColumn::make('voucher.nama')
                    ->label('Voucher')
                    ->default('-')
                    ->badge()
                    ->color('success'),

                TextColumn::make('total_harga')
                    ->label('Total Harga')
                    ->money('IDR', locale: 'id')
                    ->sortable(),

                TextColumn::make('diskon')
                    ->label('Diskon')
                    ->money('IDR', locale: 'id')
                    ->color('danger')
                    ->default(0),

                TextColumn::make('total_bayar')
                    ->label('Total Bayar')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),

                TextColumn::make('tanggal_penjualan')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

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
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                    ExportBulkAction::make()
                        ->exports([
                            ExcelExport::make()
                                ->fromTable()
                                ->withFilename('laporan-penjualan-' . now()->format('Y-m-d')),
                        ]),
                ]),
            ]);
    }
}