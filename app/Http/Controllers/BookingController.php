<?php

namespace App\Http\Controllers;

use App\Models\Booking;

use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Menyimpan pesanan baru dari form frontend.
     */
    public function store(Request $request)
    {
        $request->validate([
            'contact_name' => 'required|string|max:255',
            'whatsapp_number' => 'required|string|max:20',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required|string|in:10:00,11:00,12:00,13:00,14:00,15:00,16:00',
            'session_name' => 'required|string|max:255',
            'package_name' => 'required|string|max:255',
            'selected_backgrounds' => 'nullable|array',
            'selected_extra_items' => 'nullable|array',
            'total_price' => 'required|integer|min:0',
            'notes' => 'nullable|string',
            'baby_name' => 'nullable|string|max:255',       // tambahkan
            'baby_age' => 'nullable|string|in:1-year,2-years,3-years,4-years,5-years,6-years', // tambahkan
        ]);

        // 🔒 Cek apakah waktu sudah diambil
        $exists = Booking::where('booking_date', $request->booking_date)
            ->where('booking_time', $request->booking_time)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Maaf, waktu yang Anda pilih sudah tidak tersedia. Silakan pilih waktu lain.',
            ], 409); // 409 Conflict
        }

        try {
            $booking = Booking::create($request->all());

            return response()->json([
                'message' => 'Pesanan Anda berhasil disimpan!',
                'booking_id' => $booking->id
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan pesanan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
