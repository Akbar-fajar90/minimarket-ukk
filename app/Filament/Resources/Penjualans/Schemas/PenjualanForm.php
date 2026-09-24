<?php

namespace App\Filament\Resources\Penjualans\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Facades\Filament;
use App\Models\Pelanggan;
use App\Models\Produk;
use App\Models\Voucher;
use Livewire\Component;

class PenjualanForm
{
        protected static function hitungUlangTotal(Component $livewire, callable $set): void
    {
        $details = $livewire->data['details'] ?? [];

        $total = collect($details)->sum(function ($item) {
            return (float) ($item['harga'] ?? 0) * (float) ($item['jumlah'] ?? 1);
        });

        $voucherId = $livewire->data['voucher_id'] ?? null;
        $diskon    = 0;

        if ($voucherId) {
            $voucher = Voucher::find($voucherId);
            $diskon  = $voucher?->hitungDiskon($total) ?? 0;
        }

        $set('../../total_harga', $total);
        $set('../../diskon', $diskon);
        $set('../../total_bayar', max(0, $total - $diskon));
    }
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pelanggan')
                    ->schema([
                        TextInput::make('user_id')
                            ->label('Kasir')
                            ->default(Filament::auth()->id())
                            ->disabled()
                            ->dehydrated()
                            ->required(),

                        DateTimePicker::make('tanggal_penjualan')
                            ->label('Tanggal Penjualan')
                            ->default(now())
                            ->seconds(false)        
                            ->required(),

                        Select::make('pelanggan_id')
                            ->label('Pilih Pelanggan')
                            ->relationship('pelanggan', 'nama')
                            ->getOptionLabelFromRecordUsing(
                                fn (Pelanggan $record) =>
                                    "{$record->nama}" . ($record->poin ? " (Poin: {$record->poin})" : '')
                            )
                            ->searchable()
                            ->preload()
                            ->live()
                            ->placeholder('Pelanggan Umum / Walk-in')
                            ->afterStateUpdated(function (?string $state, callable $set, callable $get) {
                                if (! $state) {
                                    $set('nama_pelanggan', null);
                                    $set('no_telepon', null);
                                    $set('alamat', null);
                                    $set('voucher_id', null);
                                    $set('diskon', 0);
                                    $set('total_bayar', $get('total_harga') ?? 0);
                                    return;
                                }

                                $pelanggan = Pelanggan::find($state);

                                $set('nama_pelanggan', $pelanggan?->nama);
                                $set('no_telepon', $pelanggan?->no_telepon);
                                $set('alamat', $pelanggan?->alamat);
                            }),

                        TextInput::make('nama_pelanggan')
                            ->label('Nama Pelanggan')
                            ->maxLength(100)
                            ->helperText('Kosongkan jika pelanggan umum / walk-in'),

                        TextInput::make('no_telepon')
                            ->label('No. Telepon')
                            ->tel()
                            ->maxLength(20),

                        Textarea::make('alamat')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Detail Produk')
                    ->schema([
                        Repeater::make('details')
                    ->relationship()
                    ->schema([
                        Select::make('produk_id')
                            ->label('Pilih Produk')
                            ->relationship('produk', 'nama')
                            ->getOptionLabelFromRecordUsing(
                                fn (Produk $record) =>
                                    "{$record->nama} | Stok: {$record->stok} | Rp " .
                                    number_format($record->harga, 0, ',', '.')
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $get, callable $set, $livewire) {
                                $produk = Produk::find($state);
                                $harga  = $produk?->harga ?? 0;
                                $jumlah = (float) ($get('jumlah') ?? 1);
                                $set('harga', $harga);
                                $set('subtotal', $harga * $jumlah);

                                self::hitungUlangTotal($livewire, $set);
                            }),

                        TextInput::make('jumlah')
                            ->numeric()
                            ->integer()
                            ->default(1)
                            ->required()
                            ->live()
                            ->minValue(1)
                            ->maxValue(function (callable $get) {
                                $produk = Produk::find($get('produk_id'));
                                return $produk?->stok ?? 0;
                            })
                            ->afterStateUpdated(function ($state, callable $get, callable $set, $livewire) {
                                $set('subtotal', (float) $state * (float) $get('harga'));

                                self::hitungUlangTotal($livewire, $set);
                            }),

                        TextInput::make('harga')
                            ->numeric()
                            ->readOnly()
                            ->live()
                            ->required()
                            ->minValue(0),

                        Hidden::make('subtotal')
                            ->dehydrated()
                            ->default(0),
                    ])
                    ->columns(4)
                    ->defaultItems(1)
                    ->minItems(1)
                    ->addActionLabel('Tambah Produk Lain')
                    ->live()
                            ->afterStateUpdated(function ($livewire, $state, callable $get, callable $set) {
                                $details = is_array($state) ? $state : [];

                                $total = collect($details)->sum(function ($item) {
                                    $harga  = (float) ($item['harga'] ?? 0);
                                    $jumlah = (float) ($item['jumlah'] ?? 1);
                                    return $harga * $jumlah;
                                });

                                $set('total_harga', $total);

                                $voucherId = $get('voucher_id');
                                if ($voucherId) {
                                    $voucher = Voucher::find($voucherId);
                                    $diskon  = $voucher?->hitungDiskon($total) ?? 0;
                                    $set('diskon', $diskon);
                                    $set('total_bayar', max(0, $total - $diskon));
                                } else {
                                    $set('diskon', 0);
                                    $set('total_bayar', $total);
                                }
                                self::hitungUlangTotal($livewire, $set);
                            }),
                    ]),

                Section::make('Voucher Diskon')
                    ->schema([
                        Select::make('voucher_id')
                            ->label('Pilih Voucher')
                            ->options(function (callable $get) {
                                $pelangganId = $get('pelanggan_id');

                                if (! $pelangganId) {
                                    return [];
                                }

                                $pelanggan = Pelanggan::find($pelangganId);
                                if (! $pelanggan) {
                                    return [];
                                }

                                return Voucher::aktif()
                                    ->where(function ($q) use ($pelanggan) {
                                        $q->where('poin_dibutuhkan', 0)
                                          ->orWhere('poin_dibutuhkan', '<=', $pelanggan->poin ?? 0);
                                    })
                                    ->pluck('nama', 'id');
                            })
                            ->searchable()
                            ->live()
                            ->disabled(fn (callable $get) => ! $get('pelanggan_id'))
                            ->dehydrated()
                            ->placeholder(fn (callable $get) =>
                                $get('pelanggan_id') ? 'Tanpa Voucher' : 'Hanya untuk member'
                            )
                            ->helperText(fn (callable $get) =>
                                $get('pelanggan_id') ? null : '⚠️ Kupon hanya bisa digunakan oleh pelanggan member.'
                            )
                            ->afterStateUpdated(function (?string $state, callable $set, callable $get) {
                                $totalHarga = (float) ($get('total_harga') ?? 0);

                                if (! $state) {
                                    $set('diskon', 0);
                                    $set('total_bayar', $totalHarga);
                                    return;
                                }

                                $voucher = Voucher::find($state);
                                $diskon  = $voucher?->hitungDiskon($totalHarga) ?? 0;

                                $set('diskon', $diskon);
                                $set('total_bayar', max(0, $totalHarga - $diskon));
                            }),

                        TextInput::make('diskon')
                            ->label('Diskon')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated(),

                        TextInput::make('total_harga')
                            ->label('Total Harga')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated(),

                        TextInput::make('total_bayar')
                            ->label('Total Bayar')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated(),
                    ])->columns(2),
            ]);
    }
}