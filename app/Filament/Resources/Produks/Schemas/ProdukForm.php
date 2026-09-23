<?php

namespace App\Filament\Resources\Produks\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;

class ProdukForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->required(),
                TextInput::make('harga')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                FileUpload::make('gambar')
                    ->image()
                    ->imageEditor()
                    ->directory('produk') 
                    ->maxSize(2048) 
                    ->required(),
                TextInput::make('stok')
                    ->required()
                    ->numeric()
                    ->default(0),
                Select::make('kategori_id')
                    ->label('Kategori')
                    ->relationship('kategori', 'nama') 
                    ->searchable()
                    ->preload() 
                    ->createOptionForm([ 
                        TextInput::make('nama')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->required(),
            ]);
    }
}
