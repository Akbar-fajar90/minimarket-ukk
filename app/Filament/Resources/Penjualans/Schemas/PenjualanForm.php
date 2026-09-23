<?php

namespace App\Filament\Resources\Penjualans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Schema;
use Filament\Facades\Filament;
use App\Models\Penjualan;
use App\Models\Produk;

class PenjualanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->label('Kasir')
                    ->default(Filament::auth()->id())
                    ->disabled()
                    ->dehydrated()
                    ->required(),

                TextInput::make('nama_pelanggan')
                    ->label('Kode Pelanggan')
                    ->default(function () {
                        $last = Penjualan::latest('id')->first();
                        $lastNumber = $last ? (int) substr($last->nama_pelanggan, -4) : 0;
                        return 'cust-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
                    })
                    ->readOnly()
                    ->required()
                    ->maxLength(50)
                    ->regex('/^cust-\d{4}$/')
                    ->validationMessages([
                        'required' => 'Kode pelanggan wajib diisi.',
                        'max_length' => 'Kode pelanggan maksimal 50 karakter.',
                        'regex' => 'Format kode pelanggan harus cust-XXXX.',
                    ]),

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
                            ->rules([
                                'required',
                                'exists:produks,id',
                            ])
                            ->validationMessages([
                                'required' => 'Produk wajib dipilih.',
                                'exists' => 'Produk yang dipilih tidak valid.',
                            ])
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $produk = Produk::find($state);
                                $harga  = $produk?->harga ?? 0;
                                $jumlah = (float) ($get('jumlah') ?? 1);
                                $set('harga', $harga);
                                $set('subtotal', $harga * $jumlah);
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
                            ->rule(function (callable $get) {
                                return function ($attribute, $value, $fail) use ($get) {
                                    if (!is_numeric($value) || (int) $value != $value) {
                                        $fail('Jumlah harus berupa bilangan bulat.');
                                        return;
                                    }
                                    $produk = Produk::find($get('produk_id'));
                                    if (!$produk) {
                                        $fail('Pilih produk terlebih dahulu.');
                                        return;
                                    }
                                    if ($value < 1) {
                                        $fail('Jumlah minimal 1.');
                                        return;
                                    }
                                    if ($value > $produk->stok) {
                                        $fail("Stok tidak cukup. Tersedia: {$produk->stok}");
                                    }
                                };
                            })
                            ->validationMessages([
                                'required' => 'Jumlah wajib diisi.',
                                'numeric' => 'Jumlah harus berupa angka.',
                                'integer' => 'Jumlah harus bilangan bulat.',
                                'min' => 'Jumlah minimal 1.',
                                'max' => 'Jumlah melebihi stok tersedia.',
                            ])
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $set('subtotal', (float) $state * (float) $get('harga'));
                            }),

                        TextInput::make('harga')
                            ->numeric()
                            ->readOnly()
                            ->live()
                            ->required()
                            ->minValue(0)
                            ->validationMessages([
                                'required' => 'Harga wajib diisi.',
                                'numeric' => 'Harga harus berupa angka.',
                                'min' => 'Harga tidak boleh negatif.',
                            ])
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $set('subtotal', (float) $state * (float) ($get('jumlah') ?? 1));
                            }),

                        Hidden::make('subtotal')
                            ->dehydrated()
                            ->default(0),
                    ])
                    ->columns(4)
                    ->defaultItems(1)
                    ->minItems(1)
                    ->addActionLabel('Tambah Produk Lain')
                    ->validationMessages([
                        'min_items' => 'Minimal harus ada 1 produk dalam transaksi.',
                    ]),
            ]);
    }
}