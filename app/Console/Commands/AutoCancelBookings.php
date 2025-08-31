<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Carbon\Carbon;

class AutoCancelBookings extends Command
{
    /**
     * Nama dan signature command
     */
    protected $signature = 'bookings:auto-cancel';

    /**
     * Deskripsi command
     */
    protected $description = 'Auto cancel bookings if payment deadline exceeded';

    /**
     * Jalankan command
     */
    public function handle()
    {
        $count = Booking::where('status', 'waiting_payment')
            ->where('payment_deadline', '<', Carbon::now())
            ->update([
                'status' => 'cancelled',
                'auto_cancelled_at' => Carbon::now(),
                'cancellation_reason' => 'Otomatis dibatalkan karena melewati batas waktu pembayaran',
            ]);

        $this->info("Auto-cancel selesai. {$count} booking dibatalkan.");
    }
}
