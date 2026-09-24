<?php

namespace App\Filament\Resources\Pelanggans\Pages;

use App\Filament\Resources\Pelanggans\PelangganResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePelanggan extends CreateRecord
{
    protected static string $resource = PelangganResource::class;

        protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['kode_member'] = \App\Models\Pelanggan::generateKodeMember();
        $data['user_id']     = \Filament\Facades\Filament::auth()->id();

        return $data;
    }
}
