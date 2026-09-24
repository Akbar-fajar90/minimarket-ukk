<?php

namespace App\Filament\Resources\Vouchers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;

class VoucherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kode')
                    ->label('Kode Voucher')
                    ->unique(ignoreRecord: true)
                    ->default(fn () => \App\Models\Voucher::generateKodeVoucher())
                    ->readOnly()
                    ->maxLength(30)
                    ->required()
                    ->helperText('Kode dibuat otomatis oleh sistem'),
                TextInput::make('nama')
                    ->required()
                    ->maxLength(100),
                Select::make('tipe')
                    ->options(['nominal' => 'Nominal (Rp.)', 'persen' => 'Persen (%)'])
                    ->default('nominal')
                    ->required()
                    ->live(),
                TextInput::make('nilai')
                    ->required()
                    ->numeric()
                    ->prefix(fn (Get $get) => $get('tipe') === 'persen' ? null : 'Rp')
                    ->suffix(fn (Get $get) => $get('tipe') === 'persen' ? '%' : null),
                TextInput::make('minimal_belanja')
                    ->required()
                    ->label('Minimum Belanja')
                    ->numeric()
                    ->default(0)
                    ->prefix('Rp'),
                TextInput::make('poin_dibutuhkan')
                    ->required()
                    ->label('Poin Dibutuhkan')
                    ->numeric()
                    ->default(0)
                    ->helperText('0 = Tidak perlu poin *(voucher umum)'),
                TextInput::make('kuota')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('terpakai')
                    ->numeric()
                    ->default(0)
                    ->disabled()
                    ->dehydrated(),
                DatePicker::make('tanggal_mulai')
                    ->label('Berlaku Dari'),
                DatePicker::make('tanggal_berakhir')
                ->label('Berlaku Sampai'),
                Toggle::make('aktif')
                    ->required(),
            ]);
    }
}
