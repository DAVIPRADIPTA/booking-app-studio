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
        Schema::table('bookings', function (Blueprint $table) {
            // Memastikan kolom JSON bisa NULL dan memiliki default NULL
            $table->json('selected_backgrounds')->nullable()->default(null)->change();
            $table->json('selected_extra_items')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Mengembalikan ke kondisi semula (menghapus default null)
            $table->json('selected_backgrounds')->nullable()->change();
            $table->json('selected_extra_items')->nullable()->change();
        });
    }
};