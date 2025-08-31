<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->string('contact_name');
            $table->string('whatsapp_number');
            $table->date('booking_date');
            $table->string('booking_time');
            $table->string('session_name');
            $table->string('package_name');
            $table->json('selected_backgrounds');
            $table->json('selected_extra_items');
            $table->integer('total_price');
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'booked', 'completed', 'cancelled'])->default('pending');
            
            // Kolom untuk pembayaran
            $table->timestamp('payment_deadline')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->string('payment_url')->nullable();
            
            // Kolom untuk pembatalan
            $table->boolean('cancellation_requested')->default(false);
            $table->timestamp('cancellation_requested_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->decimal('refund_amount', 15, 2)->nullable();
            $table->string('refund_proof')->nullable();
            
            // Kolom untuk baby smash cake
            $table->string('baby_name')->nullable();
            $table->string('baby_age')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};