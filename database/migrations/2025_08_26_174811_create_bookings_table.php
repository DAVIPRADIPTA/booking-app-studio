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

            // Relasi ke users (admin yang input / owner booking)
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            // Relasi ke customers (customer yang pesan)
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');

            // Data dasar
            $table->string('contact_name');
            $table->string('whatsapp_number');
            $table->date('booking_date');
            $table->string('booking_time');
            $table->string('session_name');
            $table->string('package_name');
            $table->json('selected_backgrounds')->nullable();
            $table->json('selected_extra_items')->nullable();
            $table->integer('total_price');

            // Payment
            $table->enum('payment_method', ['cash', 'transfer'])->default('transfer');
            $table->text('notes')->nullable();
            $table->enum('status', [
                'waiting_payment',
                'pending_verification',
                'booked',
                'completed',
                'cancelled'
            ])->default('waiting_payment');
            $table->timestamp('payment_deadline')->nullable();
            $table->string('payment_proof')->nullable();

            // Cancellation
            $table->boolean('cancellation_requested')->default(false);
            $table->timestamp('cancellation_requested_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->decimal('refund_amount', 15, 2)->nullable();
            $table->string('refund_proof')->nullable();

            // Tambahan baby smash cake
            $table->string('baby_name')->nullable();
            $table->string('baby_age')->nullable();

            // Auto cancel
            $table->timestamp('auto_cancelled_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
