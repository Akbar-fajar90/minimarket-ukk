<?php

namespace App\Filament\Resources\Penjualans\Tables;

use Filament\Actions\Action;
use App\Models\Penjualan;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;
use App\Models\Produk;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'warning',
                    })
                    ->default('completed')
                    ->sortable(),

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
                Filter::make('tanggal_penjualan')
                    ->form([
                        DatePicker::make('date_from')
                            ->label('Dari Tanggal')
                            ->placeholder(function () {
                                $min = Penjualan::min('tanggal_penjualan');
                                return $min ? Carbon::parse($min)->format('d M Y') : 'Tidak ada data';
                            }),
                        DatePicker::make('date_until')
                            ->label('Sampai Tanggal')
                            ->placeholder(function () {
                                $max = Penjualan::max('tanggal_penjualan');
                                return $max ? Carbon::parse($max)->format('d M Y') : 'Tidak ada data';
                            }),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_penjualan', '>=', $date),
                            )
                            ->when(
                                $data['date_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_penjualan', '<=', $date),
                            );
                    }),
                SelectFilter::make('bulan_penjualan')
                    ->options(function () {
                        $months = [];
                        for ($i = 1; $i <= 12; $i++) {
                            $months[$i] = Carbon::create(null, $i, 1)->monthName;
                        }
                        return $months;
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        if (isset($data['value']) && $data['value'] !== null) {
                            return $query->whereMonth('tanggal_penjualan', $data['value']);
                        }
                        return $query;
                    })
                    ->label('Bulan'),
                SelectFilter::make('tahun_penjualan')
                    ->options(fn () => array_combine(range(Carbon::now()->year - 5, Carbon::now()->year + 5), range(Carbon::now()->year - 5, Carbon::now()->year + 5)))
                    ->query(fn (Builder $query, array $data): Builder => $query->when($data['value'] ?? null, fn (Builder $query, $value) => $query->whereYear('tanggal_penjualan', $value)))
                    ->label('Tahun'),
                SelectFilter::make('status')
                    ->options([
                        'all' => 'Semua',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (isset($data['value']) && $data['value'] !== null && $data['value'] !== 'all') {
                            return $query->where('status', $data['value']);
                        }
                        return $query;
                    })
                    ->label('Status Transaksi'),
            ])
            ->recordActions([
                ViewAction::make(),

                Action::make('cancel')
                    ->label('Batalkan')
                    ->icon('heroicon-o-x-circle')
                    ->color('warning')
                    ->visible(fn (Penjualan $record) => $record->status !== 'cancelled')
                    ->requiresConfirmation()
                    ->modalHeading('Batalkan Penjualan?')
                    ->modalDescription('Status akan diubah menjadi "cancelled" dan stok produk akan dikembalikan.')
                    ->modalSubmitActionLabel('Ya, Batalkan')
                    ->action(function (Penjualan $record) {
                        DB::transaction(function () use ($record) {
                            foreach ($record->details as $detail) {
                                self::restoreStockForDetail($detail, $record->id);
                            }

                            $record->update(['status' => 'cancelled']);
                            $record->details()->update(['status' => 'cancelled']);

                            self::logPenjualanActivity(
                                $record->id,
                                'Penjualan Dibatalkan',
                                'dibatalkan dan stok dikembalikan.'
                            );
                        });

                        \Filament\Notifications\Notification::make()
                            ->title('Penjualan #' . $record->id . ' berhasil dibatalkan')
                            ->success()
                            ->send();
                    }),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('bulk_delete')
                        ->label('Delete')
                        ->color('danger')
                        ->icon('heroicon-o-trash')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            DB::transaction(function () use ($records) {
                                foreach ($records as $penjualan) {
                                    if ($penjualan->status !== 'cancelled') {
                                        foreach ($penjualan->details as $detail) {
                                            self::restoreStockForDetail($detail, $penjualan->id);
                                        }
                                    }
                                    self::logPenjualanActivity($penjualan->id, 'Penjualan Dihapus (Bulk)', 'dihapus dan stok dikembalikan.');
                                    $penjualan->details()->delete();
                                    $penjualan->delete();
                                }
                            });
                        }),

                    BulkAction::make('bulk_cancelled')
                        ->label('Cancelled')
                        ->color('warning')
                        ->icon('heroicon-o-x-circle')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            DB::transaction(function () use ($records) {
                                foreach ($records as $penjualan) {
                                    if ($penjualan->status !== 'cancelled') {
                                        foreach ($penjualan->details as $detail) {
                                            self::restoreStockForDetail($detail, $penjualan->id);
                                        }
                                        $penjualan->update(['status' => 'cancelled']);
                                        $penjualan->details()->update(['status' => 'cancelled']);
                                        self::logPenjualanActivity($penjualan->id, 'Penjualan Dibatalkan (Bulk)', 'dibatalkan dan stok dikembalikan.');
                                    }
                                }
                            });
                        }),

                    ExportBulkAction::make()
                        ->exports([
                            ExcelExport::make()
                                ->fromTable()
                                ->withFilename('laporan-penjualan-' . now()->format('Y-m-d')),
                        ]),
                ]),
            ]);
    }

    private static function restoreStockForDetail(\App\Models\DetailPenjualan $detail, int $penjualanId): void
    {
        $produk = Produk::find($detail->produk_id);
        if ($produk) {
            $produk->increment('stok', $detail->jumlah);
        } else {
            self::logPenjualanActivity(
                $penjualanId,
                'Warning',
                'Produk dengan ID ' . $detail->produk_id . ' tidak ditemukan saat mencoba mengembalikan stok.'
            );
        }
    }

    private static function logPenjualanActivity(int $penjualanId, string $activityType, string $description): void
    {
        ActivityLog::create([
            'user_id' => Auth::id() ?: 0,
            'activity_type' => $activityType,
            'description' => 'Penjualan #' . $penjualanId . ' ' . $description,
        ]);
    }
}