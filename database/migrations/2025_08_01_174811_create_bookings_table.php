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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('contact_name');
            $table->string('whatsapp_number');
            $table->date('booking_date');
            $table->string('booking_time'); // Atau enum jika ingin lebih spesifik
            $table->string('session_name');
            $table->string('package_name');
            $table->json('selected_backgrounds'); // Simpan sebagai JSON
            $table->json('selected_extra_items'); // Simpan sebagai JSON
            $table->integer('total_price');
            $table->text('notes')->nullable();
            $table->enum('status', ['waiting', 'booked', 'completed', 'cancelled'])->default('waiting');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
