<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Password;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->minLength(3)
                    ->rule('regex:/^[a-zA-Z\s\.\'-]+$/')
                    ->validationMessages([
                        'required' => 'Nama wajib diisi.',
                        'max_length' => 'Nama maksimal 255 karakter.',
                        'min_length' => 'Nama minimal 3 karakter.',
                        'regex' => 'Nama hanya boleh berisi huruf, spasi, titik, apostrof, dan tanda hubung.',
                    ]),

                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->validationMessages([
                        'required' => 'Email wajib diisi.',
                        'email' => 'Format email tidak valid.',
                        'max_length' => 'Email maksimal 255 karakter.',
                        'unique' => 'Email sudah terdaftar.',
                    ]),

                TextInput::make('password')
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create')
                    ->password()
                    ->label('Password')
                    ->minLength(8)
                    ->maxLength(255)
                    ->rule(Password::min(8)->letters()->numbers())
                    ->revealable()
                    ->helperText('Minimal 8 karakter, kombinasi huruf dan angka. Biarkan kosong jika tidak ingin mengubah password.')
                    ->validationMessages([
                        'required' => 'Password wajib diisi.',
                        'min_length' => 'Password minimal 8 karakter.',
                        'max_length' => 'Password maksimal 255 karakter.',
                    ]),

                Select::make('role')
                    ->options(['Admin' => 'Admin', 'Kasir' => 'Kasir'])
                    ->default('Kasir')
                    ->required()
                    ->in(['Admin', 'Kasir'])
                    ->validationMessages([
                        'required' => 'Role wajib dipilih.',
                        'in' => 'Role yang dipilih tidak valid.',
                    ]),
            ]);
    }
}