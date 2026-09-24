<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 30)->unique();
            $table->string('nama', 100);
            $table->enum('tipe', ['nominal', 'persen'])->default('nominal');
            $table->decimal('nilai', 12, 2);
            $table->decimal('minimal_belanja', 12, 2)->default(0);
            $table->integer('poin_dibutuhkan')->default(0);
            $table->integer('kuota')->default(1);
            $table->integer('terpakai')->default(0);
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_berakhir')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};