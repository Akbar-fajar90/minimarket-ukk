<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->foreignId('voucher_id')
                  ->nullable()
                  ->after('pelanggan_id')
                  ->constrained('vouchers')
                  ->nullOnDelete();
            $table->decimal('diskon', 12, 2)->default(0)->after('total_harga');
            $table->decimal('total_bayar', 12, 2)->default(0)->after('diskon');
        });
    }

    public function down(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropForeign(['voucher_id']);
            $table->dropColumn(['voucher_id', 'diskon', 'total_bayar']);
        });
    }
};