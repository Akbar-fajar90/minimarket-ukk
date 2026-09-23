<?php

namespace App\Filament\Resources\Penjualans\Pages;

use App\Filament\Resources\Penjualans\PenjualanResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Produk;
use Filament\Facades\Filament;


class CreatePenjualan extends CreateRecord
{
    protected static string $resource = PenjualanResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        foreach ($data['details'] ?? [] as $i => $d) {
            $produk = \App\Models\Produk::find($d['produk_id']);
            $harga  = (float) ($produk?->harga ?? 0);
            $jumlah = (float) ($d['jumlah'] ?? 1);

            $data['details'][$i]['harga']    = $harga;
            $data['details'][$i]['subtotal'] = $harga * $jumlah;
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $total = 0;

        foreach ($this->record->details as $detail) {
            $produk = Produk::find($detail->produk_id);
            if ($produk) {
                $produk->decrement('stok', $detail->jumlah);
            }

            $total += (float) $detail->subtotal;
        }

        $this->record->update(['total_harga' => $total]);
    }
}
