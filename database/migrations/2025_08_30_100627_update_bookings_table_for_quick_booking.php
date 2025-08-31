<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update semua status 'pending' ke 'waiting_payment' terlebih dahulu
        DB::statement("UPDATE bookings SET status = 'waiting_payment' WHERE status = 'pending'");
        
        Schema::table('bookings', function (Blueprint $table) {
            // 2. Update status enum
            $table->enum('status', [
                'waiting_payment', 
                'pending_verification', 
                'booked', 
                'completed', 
                'cancelled'
            ])->default('waiting_payment')->change();
            
            // 3. Hapus default value, biarkan null
            $table->timestamp('payment_deadline')->nullable()->change();
            
            // 4. Tambah kolom untuk bukti pembayaran
            $table->string('payment_proof')->nullable()->after('payment_url');
            
            // 5. Tambah kolom untuk mencatat waktu cancel otomatis
            $table->timestamp('auto_cancelled_at')->nullable()->after('refund_proof');
            
            // 6. Hapus kolom Xendit yang tidak diperlukan
            $table->dropColumn('payment_method');
            $table->dropColumn('payment_reference');
            $table->dropColumn('payment_url');
        });
        
        // 7. Set payment_deadline untuk data lama
        DB::statement("UPDATE bookings 
                      SET payment_deadline = NOW() + INTERVAL 10 MINUTE 
                      WHERE status = 'waiting_payment' AND payment_deadline IS NULL");
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // 1. Tambahkan kembali kolom Xendit
            $table->string('payment_method')->nullable()->after('status');
            $table->string('payment_reference')->nullable()->after('payment_method');
            $table->string('payment_url')->nullable()->after('payment_reference');
            
            // 2. Hapus kolom tambahan
            $table->dropColumn('payment_proof');
            $table->dropColumn('auto_cancelled_at');
            
            // 3. Kembalikan ke status sebelumnya
            $table->enum('status', ['pending', 'booked', 'completed', 'cancelled'])
                  ->default('pending')
                  ->change();
        });
        
        // 4. Kembalikan status 'waiting_payment' ke 'pending'
        DB::statement("UPDATE bookings SET status = 'pending' WHERE status = 'waiting_payment'");
    }
};