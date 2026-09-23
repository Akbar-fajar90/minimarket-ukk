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
        \App\Models\Kategori::insert([
        ['nama' => 'Makanan', 'created_at' => now(), 'updated_at' => now()],
        ['nama' => 'Minuman', 'created_at' => now(), 'updated_at' => now()],
        ['nama' => 'Snack',   'created_at' => now(), 'updated_at' => now()],
    ]);
    }
}
