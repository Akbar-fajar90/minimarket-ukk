<?php

namespace App\Filament\Resources\Vouchers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use App\Models\Voucher;
use Filament\Tables\Filters\SelectFilter;

class VouchersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('nama')
                    ->searchable(),
                TextColumn::make('tipe')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'persen' ? 'Persen' : 'Nominal'),
                TextColumn::make('nilai')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn (Voucher $record): string =>
                        $record->tipe === 'persen'
                            ? $record->nilai . '%'
                            : 'Rp ' . number_format($record->nilai, 0, ',', '.')
                    ),
                TextColumn::make('minimal_belanja')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('poin_dibutuhkan')
                    ->numeric()
                    ->label('Poin')
                    ->color('warning')
                    ->sortable(),
                TextColumn::make('sisa_kuota')
                    ->label('Sisa Kuota')
                    ->state(fn (Voucher $record): int => $record->sisa_kuota)
                    ->color(fn (Voucher $record): string => $record->sisa_kuota > 0 ? 'success' : 'danger')
                    ->weight('bold'),
                TextColumn::make('terpakai')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tanggal_mulai')
                    ->date()
                    ->sortable(),
                TextColumn::make('tanggal_berakhir')
                    ->label('Berakhir')
                    ->date('d M Y')
                    ->sortable(),
                IconColumn::make('aktif')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('aktif')
                    ->label('Status Aktif')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Nonaktif',
                    ]),

                SelectFilter::make('tipe')
                    ->label('Tipe Voucher')
                    ->options([
                        'persen'  => 'Persen',
                        'nominal' => 'Nominal',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
