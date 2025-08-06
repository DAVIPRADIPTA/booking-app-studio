<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class BookingAvailabilityController extends Controller
{
    /**
     * Dapatkan waktu yang tersedia berdasarkan tanggal.
     */
    public function getAvailableTimes(Request $request)
    {
        $request->validate([
            'booking_date' => 'required|date',
        ]);

        $date = $request->booking_date;

        // Daftar waktu yang tersedia (bisa Anda sesuaikan)
        $allTimes = [
            '10:00', '11:00', '12:00', '13:00', 
            '14:00', '15:00', '16:00'
        ];

        // Ambil semua booking untuk tanggal tersebut
        $bookedTimes = Booking::where('booking_date', $date)
            ->pluck('booking_time')
            ->toArray();

        // Waktu yang tersedia = semua waktu - yang sudah booked
        $availableTimes = array_values(array_diff($allTimes, $bookedTimes));

        // Tentukan status hari: tersedia, sebagian, atau full
        $status = 'available';
        if (empty($availableTimes)) {
            $status = 'full';
        } elseif (count($availableTimes) < 3) {
            $status = 'limited';
        }

        return response()->json([
            'available_times' => $availableTimes,
            'booked_times' => $bookedTimes,
            'status' => $status,
            'date' => $date,
        ]);
    }
}