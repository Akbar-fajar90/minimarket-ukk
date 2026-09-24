<?php

namespace App\Filament\Resources\Penjualans\Pages;

use App\Filament\Resources\Penjualans\PenjualanResource;
use App\Models\Voucher;
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

        $diskon = 0;
        if ($this->record->voucher_id) {
            $voucher = Voucher::find($this->record->voucher_id);
            if ($voucher) {
                $diskon = $voucher->hitungDiskon($total);
            }
        }

        $totalBayar = max(0, $total - $diskon);

        $this->record->update([
            'total_harga' => $total,
            'diskon'      => $diskon,
            'total_bayar' => $totalBayar,
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}