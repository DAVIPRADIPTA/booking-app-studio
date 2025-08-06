<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->integer('dp_amount')->nullable()->after('notes');
            $table->string('dp_proof')->nullable()->after('dp_amount');
            $table->integer('final_payment_amount')->nullable()->after('dp_proof');
            $table->string('final_payment_proof')->nullable()->after('final_payment_amount');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'dp_amount',
                'dp_proof',
                'final_payment_amount',
                'final_payment_proof',
            ]);
        });
    }
};
