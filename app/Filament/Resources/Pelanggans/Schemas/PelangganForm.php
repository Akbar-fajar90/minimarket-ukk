<?php

namespace App\Filament\Resources\Pelanggans\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use App\Models\User;
use Filament\Facades\Filament;



class PelangganForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kode_member')
                    ->label('kode Member')
                    ->unique(ignoreRecord: true)
                    ->default(fn () => \App\Models\Pelanggan::generateKodeMember())
                    ->readOnly()
                    ->maxLength(30)
                    ->required(),
                TextInput::make('nama')
                    ->maxLength(100)
                    ->required(),
                TextInput::make('no_telepon')
                    ->label('No. Telp')
                    ->minLength(8)
                    ->maxLength(20)
                    ->unique(ignoreRecord:true)
                    ->tel()
                    ->default(null),
                TextInput::make('email')
                    ->label('Email address')
                    ->unique(ignoreRecord:true)
                    ->email()
                    ->default(null),
                Textarea::make('alamat')
                    ->columnSpanFull(),
                TextInput::make('poin')
                    ->required()
                    ->numeric()
                    ->disabled()
                    ->dehydrated()
                    ->default(0),
                Select::make('status')
                    ->options(['Aktif' => 'Aktif', 'Nonaktif' => 'Nonaktif'])
                    ->default('Aktif')
                    ->required(),
                DatePicker::make('tanggal_bergabung'),
                Select::make('user_id')
                    ->label('Didaftarkan Oleh')
                    ->options(User::pluck('name', 'id'))
                    ->default(Filament::auth()->id())
                    ->disabled()
                    ->dehydrated()
                    ->required(),
            ]);
    }
}
