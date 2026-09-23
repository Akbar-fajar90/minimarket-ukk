<?php

namespace App\Filament\Resources\Penjualans\Pages;

use App\Filament\Resources\Penjualans\PenjualanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPenjualan extends EditRecord
{
    protected static string $resource = PenjualanResource::class;

        protected function afterSave(): void
    {
        $total = 0;

        foreach ($this->record->details as $detail) {
            $total += (float) $detail->subtotal;
        }

        $this->record->update(['total_harga' => $total]);
    }
    
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
