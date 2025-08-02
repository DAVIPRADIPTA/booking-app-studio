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
        // 1. Validasi data yang masuk
        $validatedData = $request->validate([
            'contact_name' => 'required|string|max:255',
            'whatsapp_number' => 'required|string|max:20',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required|string',
            'session_name' => 'required|string|max:255',
            'package_name' => 'required|string|max:255',
            'selected_backgrounds' => 'nullable|array',
            'selected_extra_items' => 'nullable|array',
            'total_price' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        // 2. Buat pesanan baru di database
        // Data yang disimpan adalah yang sudah divalidasi
        try {
            $booking = Booking::create($validatedData);

            // 3. Kirim notifikasi (opsional, bisa ke WhatsApp, email, dll)
            // Di sini kita bisa menambahkan logika untuk notifikasi
            // misalnya mengirim notifikasi ke admin

            // 4. Berikan respon sukses ke frontend
            return response()->json([
                'message' => 'Pesanan Anda berhasil disimpan!',
                'booking_id' => $booking->id
            ], 201);

        } catch (\Exception $e) {
            // Tangani error jika terjadi
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan pesanan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
