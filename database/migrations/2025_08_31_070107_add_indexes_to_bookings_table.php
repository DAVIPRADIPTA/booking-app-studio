<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Index untuk mempercepat query cek slot bentrok
            $table->index(['booking_date', 'booking_time'], 'idx_booking_date_time');

            // Index untuk filter status (admin dashboard, auto-cancel)
            $table->index('status', 'idx_status');

            // Index untuk riwayat customer (relasi ke customers)
            $table->index('customer_id', 'idx_customer_id');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('idx_booking_date_time');
            $table->dropIndex('idx_status');
            $table->dropIndex('idx_customer_id');
        });
    }
};
