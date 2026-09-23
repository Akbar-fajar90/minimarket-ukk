<?php

namespace App\Filament\Resources\Produks\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Str;

class ProdukForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->required()
                    ->maxLength(255)
                    ->minLength(3)
                    ->unique(ignoreRecord: true)
                    ->rule('regex:/^[a-zA-Z0-9\s\-_\.]+$/')
                    ->validationMessages([
                        'required' => 'Nama produk wajib diisi.',
                        'max_length' => 'Nama produk maksimal 255 karakter.',
                        'min_length' => 'Nama produk minimal 3 karakter.',
                        'unique' => 'Nama produk sudah digunakan.',
                        'regex' => 'Nama produk hanya boleh berisi huruf, angka, spasi, dan simbol -_.',
                    ])
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),

                TextInput::make('harga')
                    ->numeric()
                    ->minValue(0)
                    ->prefix('Rp')
                    ->helperText('Masukkan tanpa titik. Contoh: 3000')
                    ->dehydrateStateUsing(function ($state) {
                        if ($state === null) return null;
                        return (float) str_replace(['.', ','], '', (string) $state);
                    })
                    ->rule(function () {
                        return function ($attribute, $value, $fail) {
                            if (is_string($value) && preg_match('/\.\d{3}/', $value)) {
                                $fail('Jangan gunakan titik untuk pemisah ribuan. Contoh: 3000');
                            }
                        };
                    }),

                FileUpload::make('gambar')
                    ->image()
                    ->imageEditor()
                    ->directory('produk')
                    ->maxSize(2048)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/jpg'])
                    ->required()
                    ->validationMessages([
                        'required' => 'Gambar produk wajib diunggah.',
                        'max' => 'Ukuran gambar maksimal 2MB.',
                        'mimes' => 'Format gambar harus JPG, JPEG, PNG, atau WEBP.',
                    ]),

                TextInput::make('stok')
                    ->required()
                    ->numeric()
                    ->integer()
                    ->minValue(0)
                    ->maxValue(999999)
                    ->default(0)
                    ->validationMessages([
                        'required' => 'Stok wajib diisi.',
                        'numeric' => 'Stok harus berupa angka.',
                        'integer' => 'Stok harus berupa bilangan bulat.',
                        'min' => 'Stok tidak boleh negatif.',
                        'max' => 'Stok terlalu besar.',
                    ]),

                Select::make('kategori_id')
                    ->label('Kategori')
                    ->relationship('kategori', 'nama')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('nama')
                            ->required()
                            ->maxLength(255)
                            ->minLength(3)
                            ->unique(table: 'kategoris', column: 'nama')
                            ->validationMessages([
                                'required' => 'Nama kategori wajib diisi.',
                                'max_length' => 'Nama kategori maksimal 255 karakter.',
                                'min_length' => 'Nama kategori minimal 3 karakter.',
                                'unique' => 'Nama kategori sudah digunakan.',
                            ]),
                    ])
                    ->required()
                    ->validationMessages([
                        'required' => 'Kategori wajib dipilih.',
                    ]),
            ]);
    }
}