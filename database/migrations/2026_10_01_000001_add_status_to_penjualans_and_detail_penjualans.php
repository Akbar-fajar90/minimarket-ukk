<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->string('status')->default('completed')->after('user_id');
        });

        Schema::table('detail_penjualans', function (Blueprint $table) {
            $table->string('status')->default('completed')->after('subtotal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('detail_penjualans', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};