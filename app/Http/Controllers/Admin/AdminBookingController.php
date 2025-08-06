<?php

namespace App\Http\Controllers\Admin;

use App\Models\Booking;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AdminBookingController extends Controller
{
    /**
     * Tampilkan daftar pesanan.
     */
    public function index()
    {
        $bookings = Booking::latest()->paginate(15);
        return view('admin.bookings.index', compact('bookings'));
    }

    /**
     * Tampilkan detail pesanan.
     */
    public function show(string $id)
    {
        $booking = Booking::findOrFail($id);
        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Konfirmasi pembayaran DP dan arahkan ke WhatsApp.
     */
    public function confirmDp(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $request->validate([
            'dp_amount' => 'required|numeric|min:0',
            'dp_proof' => 'required|image|max:2048',
        ]);

        $path = $request->file('dp_proof')->store('bukti_dp', 'public');

        $booking->update([
            'dp_amount' => $request->dp_amount,
            'dp_proof' => $path,
            'status' => 'booked',
        ]);

        // Pesan sukses
        session()->flash('success', 'DP berhasil dikonfirmasi. Silakan kirim pesan ke pelanggan.');

        // Pesan WhatsApp
        $message = "Halo kak {$booking->contact_name},\n\n"
            . "Terima kasih, DP kamu untuk sesi {$booking->session_name} pada tanggal "
            . Carbon::parse($booking->booking_date)->format('d F Y') . " pukul {$booking->booking_time} telah kami terima.\n"
            . "Pesananmu sudah dikonfirmasi. Sampai jumpa di hari H!";

        $whatsappNumber = preg_replace('/[^0-9]/', '', $booking->whatsapp_number);
        $url = 'https://wa.me/' . $whatsappNumber . '?text=' . urlencode($message);

        return redirect()->away($url);
    }

    /**
     * Selesaikan pesanan (pelunasan) dan arahkan ke WhatsApp.
     */
    public function completeBooking(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $request->validate([
            'final_payment_amount' => 'required|numeric|min:0',
            'final_payment_proof' => 'required|image|max:2048',
        ]);

        $path = $request->file('final_payment_proof')->store('bukti_pelunasan', 'public');

        $booking->update([
            'final_payment_amount' => $request->final_payment_amount,
            'final_payment_proof' => $path,
            'status' => 'completed',
        ]);

        // Pesan sukses
        session()->flash('success', 'Pelunasan berhasil dikonfirmasi. Silakan kirim konfirmasi ke pelanggan.');

        // Pesan WhatsApp
        $message = "Halo kak {$booking->contact_name},\n\n"
            . "Kami telah menerima pelunasan untuk sesi {$booking->session_name} pada tanggal "
            . Carbon::parse($booking->booking_date)->format('d F Y') . ".\n"
            . "Terima kasih telah mempercayakan layanan kami. See you next time!";

        $whatsappNumber = preg_replace('/[^0-9]/', '', $booking->whatsapp_number);
        $url = 'https://wa.me/' . $whatsappNumber . '?text=' . urlencode($message);

        return redirect()->away($url);
    }

    /**
     * Hapus pesanan dan hapus file bukti pembayaran.
     */
    public function destroy(Booking $booking)
    {
        // Hapus file bukti jika ada
        if ($booking->dp_proof) {
            Storage::disk('public')->delete($booking->dp_proof);
        }
        if ($booking->final_payment_proof) {
            Storage::disk('public')->delete($booking->final_payment_proof);
        }

        $booking->delete();

        return redirect()->route('bookings.index')->with('success', 'Pesanan berhasil dihapus.');
    }
}