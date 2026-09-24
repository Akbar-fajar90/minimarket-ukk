<?php

namespace App\Filament\Resources\Penjualans\Pages;

use App\Filament\Resources\Penjualans\PenjualanResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Produk;
use App\Models\Voucher;

class CreatePenjualan extends CreateRecord
{
    protected static string $resource = PenjualanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        foreach ($data['details'] ?? [] as $i => $d) {
            $produk = Produk::find($d['produk_id']);
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

        $diskon = 0;
        if ($this->record->voucher_id) {
            $voucher = Voucher::find($this->record->voucher_id);
            
            if ($voucher) {
                $diskon = $voucher->hitungDiskon($total);
                
                $voucher->increment('terpakai');
            }
        }

        $totalBayar = max(0, $total - $diskon);

        $this->record->update([
            'total_harga' => $total,
            'diskon'      => $diskon,
            'total_bayar' => $totalBayar,
        ]);

        $this->record->refresh();
        $penjualan = $this->record;

        if ($penjualan->pelanggan_id && $penjualan->total_bayar > 0) {
            $poin = (int) floor($penjualan->total_bayar / 10000);

            if ($poin > 0) {
                $penjualan->pelanggan->tambahPoin(
                    jumlah: $poin,
                    penjualan: $penjualan,
                    keterangan: "Poin dari transaksi #{$penjualan->id}"
                );
            }
        }
    }
}