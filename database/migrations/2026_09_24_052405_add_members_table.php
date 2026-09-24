<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelanggans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_member', 30)->unique();
            $table->string('nama', 100);
            $table->string('no_telepon', 20)->nullable()->unique();
            $table->string('email', 100)->nullable()->unique();
            $table->text('alamat')->nullable();
            $table->integer('poin')->default(0);
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif');
            $table->date('tanggal_bergabung')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelanggans');
    }
};