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
     * Tampilkan daftar semua pesanan.
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
     * Konfirmasi DP: simpan bukti, ubah status ke 'booked', lalu arahkan ke WhatsApp.
     */
    public function confirmDp(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        // Validasi input
        $request->validate([
            'dp_amount' => 'required|numeric|min:0',
            'dp_proof' => 'required|image|max:2048',
        ]);

        // Simpan bukti DP
        $path = $request->file('dp_proof')->store('bukti_dp', 'public');

        // Update data booking
        $booking->update([
            'dp_amount' => $request->dp_amount,
            'dp_proof' => $path,
            'status' => 'booked',
        ]);

        // Set pesan sukses
        session()->flash('success', 'DP berhasil dikonfirmasi. Silakan kirim pesan ke pelanggan.');

        // Buat pesan WhatsApp
        $message = "Halo kak {$booking->contact_name},\n\n"
            . "Terima kasih, DP kamu untuk sesi {$booking->session_name} pada tanggal "
            . Carbon::parse($booking->booking_date)->translatedFormat('d F Y') . " pukul {$booking->booking_time} telah kami terima.\n"
            . "Pesananmu sudah dikonfirmasi ✅ Sampai jumpa di hari H!";

        // Format nomor WhatsApp (08xx → 628xx)
        $whatsappNumber = preg_replace('/[^0-9]/', '', $booking->whatsapp_number);
        if (str_starts_with($whatsappNumber, '0')) {
            $whatsappNumber = '62' . substr($whatsappNumber, 1);
        }

        // ✅ URL BENAR: TANPA SPASI EKSTRA
        $url = 'https://wa.me/' . $whatsappNumber . '?text=' . urlencode($message);

        return redirect()->away($url);
    }

    /**
     * Selesaikan pesanan: simpan bukti pelunasan, ubah status ke 'completed', lalu arahkan ke WhatsApp.
     */
    public function completeBooking(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        // Validasi input
        $request->validate([
            'final_payment_amount' => 'required|numeric|min:0',
            'final_payment_proof' => 'required|image|max:2048',
        ]);

        // Simpan bukti pelunasan
        $path = $request->file('final_payment_proof')->store('bukti_pelunasan', 'public');

        // Update data booking
        $booking->update([
            'final_payment_amount' => $request->final_payment_amount,
            'final_payment_proof' => $path,
            'status' => 'completed',
        ]);

        // Set pesan sukses
        session()->flash('success', 'Pelunasan berhasil dikonfirmasi.');

        // Buat pesan WhatsApp
        $message = "Halo kak {$booking->contact_name},\n\n"
            . "Kami telah menerima pelunasan untuk sesi {$booking->session_name} pada tanggal "
            . Carbon::parse($booking->booking_date)->translatedFormat('d F Y') . ".\n"
            . "Terima kasih telah mempercayakan layanan kami. See you next time! ❤️";

        // Format nomor WhatsApp
        $whatsappNumber = preg_replace('/[^0-9]/', '', $booking->whatsapp_number);
        if (str_starts_with($whatsappNumber, '0')) {
            $whatsappNumber = '62' . substr($whatsappNumber, 1);
        }

        // ✅ URL BENAR: TANPA SPASI EKSTRA
        $url = 'https://wa.me/' . $whatsappNumber . '?text=' . urlencode($message);

        return redirect()->away($url);
    }

    /**
     * Hapus pesanan dan file bukti pembayaran.
     */
    public function destroy(Booking $booking)
    {
        // Hapus file bukti jika ada
        if ($booking->dp_proof && Storage::disk('public')->exists($booking->dp_proof)) {
            Storage::disk('public')->delete($booking->dp_proof);
        }
        if ($booking->final_payment_proof && Storage::disk('public')->exists($booking->final_payment_proof)) {
            Storage::disk('public')->delete($booking->final_payment_proof);
        }

        $booking->delete();

        return redirect()->route('bookings.index')->with('success', 'Pesanan berhasil dihapus.');
    }
}