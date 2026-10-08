<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        \App\Models\Kategori::insert([
            ['nama' => 'Makanan',        'created_at' => $now, 'updated_at' => $now],
            ['nama' => 'Minuman',        'created_at' => $now, 'updated_at' => $now],
            ['nama' => 'Snack',          'created_at' => $now, 'updated_at' => $now],
            ['nama' => 'Sembako',        'created_at' => $now, 'updated_at' => $now],
            ['nama' => 'Alat Tulis',     'created_at' => $now, 'updated_at' => $now],
            ['nama' => 'Perlengkapan Mandi', 'created_at' => $now, 'updated_at' => $now],
            ['nama' => 'Obat-obatan',    'created_at' => $now, 'updated_at' => $now],
            ['nama' => 'Elektronik',     'created_at' => $now, 'updated_at' => $now],
            ['nama' => 'Peralatan Rumah Tangga', 'created_at' => $now, 'updated_at' => $now],
            ['nama' => 'Lain-lain',      'created_at' => $now, 'updated_at' => $now],
    ]);
    }
}
