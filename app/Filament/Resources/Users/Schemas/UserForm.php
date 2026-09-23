<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('password')
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create')
                    ->password()
                    ->label('Password')
                    ->helperText('Biarkan kosong jika tidak ingin mengubah password.'),
                Select::make('role')
                    ->options(['Admin' => 'Admin', 'Kasir' => 'Kasir'])
                    ->default('Kasir')
                    ->required(),
            ]);
    }
}
