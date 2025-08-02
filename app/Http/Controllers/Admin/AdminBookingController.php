<?php

namespace App\Http\Controllers\Admin;
use App\Models\Booking;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class AdminBookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         // Ambil semua pesanan dan urutkan dari yang terbaru
        $bookings = Booking::latest()->paginate(15);

        return view('admin.bookings.index', compact('bookings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {   
        $booking = Booking::findOrFail($id); // Cari booking secara manual

         return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $validatedData = $request->validate([
            'status' => ['required', Rule::in(['pending', 'confirmed', 'completed', 'cancelled'])],
        ]);

        $booking->status = $validatedData['status'];
        $booking->save();
        
        // Cek apakah status yang baru adalah 'confirmed'
        if ($booking->status === 'confirmed') {
            // Jika iya, siapkan pesan WhatsApp
            $message = "Halo kak " . $booking->contact_name . ",\n\n"
                     . "Pesanan kamu untuk sesi " . $booking->session_name . " pada tanggal "
                     . Carbon::parse($booking->booking_date)->format('d F Y')
                     . " jam " . $booking->booking_time . " sudah berhasil dikonfirmasi.\n\n"
                     . "Terima kasih sudah memesan di tempat kami!";

            $whatsappUrl = 'https://wa.me/' . $booking->whatsapp_number . '?text=' . urlencode($message);

            // Redirect ke URL WhatsApp
            return redirect()->away($whatsappUrl);
        }

        // Jika status bukan 'confirmed', redirect kembali ke halaman sebelumnya
        return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui.');
    }


    /**
     * Remove the specified resource from storage.
     */
     public function destroy(Booking $booking)
    {
        $booking->delete();

        return redirect()->route('bookings.index')->with('success', 'Pesanan berhasil dihapus.');
    }
}
