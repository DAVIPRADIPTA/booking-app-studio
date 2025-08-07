<?php

namespace App\Http\Controllers\Admin;

use App\Models\Booking;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AdminBookingController extends Controller
{
    const DP_PERCENTAGE = 0.5; // 50% dari total harga

    public function index()
    {
        $bookings = Booking::latest()->paginate(15);
        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(string $id)
    {
        $booking = Booking::findOrFail($id);
        $expectedDpAmount = $booking->total_price * self::DP_PERCENTAGE;

        return view('admin.bookings.show', compact('booking', 'expectedDpAmount'));
    }

    public function confirmDp(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $expectedDpAmount = $booking->total_price * self::DP_PERCENTAGE;

        $request->validate([
            'dp_amount' => 'required|numeric|min:1',
            'dp_proof' => 'required|image|max:2048',
        ], [
            'dp_amount.required' => 'Nominal DP wajib diisi.',
            'dp_amount.numeric' => 'Nominal DP harus berupa angka.',
            'dp_amount.min' => 'Nominal DP harus lebih dari 0.',
            'dp_proof.required' => 'Bukti transfer DP wajib diunggah.',
            'dp_proof.image' => 'File bukti harus berupa gambar.',
            'dp_proof.max' => 'Ukuran file maksimal 2MB.',
        ]);

        if ($request->dp_amount != $expectedDpAmount) {
            return back()->withErrors([
                'dp_amount' => "Nominal DP harus tepat Rp" . number_format($expectedDpAmount, 0, ',', '.') . " (50% dari total harga)."
            ])->withInput();
        }

        $path = $request->file('dp_proof')->store('bukti_dp', 'public');

        $booking->update([
            'dp_amount' => $request->dp_amount,
            'dp_proof' => $path,
            'status' => 'booked',
        ]);

        session()->flash('success', 'DP berhasil dikonfirmasi. Silakan kirim pesan ke pelanggan.');

        $message = "Halo kak {$booking->contact_name},\n\n"
            . "Terima kasih, DP kamu untuk sesi {$booking->session_name} pada tanggal "
            . Carbon::parse($booking->booking_date)->translatedFormat('d F Y') . " pukul {$booking->booking_time} telah kami terima.\n"
            . "Pesananmu sudah dikonfirmasi ✅ Sampai jumpa di hari H!";

        $whatsappNumber = preg_replace('/[^0-9]/', '', $booking->whatsapp_number);
        if (str_starts_with($whatsappNumber, '0')) {
            $whatsappNumber = '62' . substr($whatsappNumber, 1);
        }

        $url = 'https://wa.me/' . $whatsappNumber . '?text=' . urlencode($message);

        return redirect()->away($url);
    }

    public function completeBooking(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $expectedFinalAmount = $booking->total_price - $booking->dp_amount;

        $request->validate([
            'final_payment_amount' => 'required|numeric|min:1',
            'final_payment_proof' => 'required|image|max:2048',
        ], [
            'final_payment_amount.required' => 'Nominal pelunasan wajib diisi.',
            'final_payment_amount.numeric' => 'Nominal pelunasan harus berupa angka.',
            'final_payment_amount.min' => 'Nominal pelunasan harus lebih dari 0.',
            'final_payment_proof.required' => 'Bukti pelunasan wajib diunggah.',
            'final_payment_proof.image' => 'File bukti harus berupa gambar.',
            'final_payment_proof.max' => 'Ukuran file maksimal 2MB.',
        ]);

        if ($request->final_payment_amount != $expectedFinalAmount) {
            return back()->withErrors([
                'final_payment_amount' => "Nominal pelunasan harus tepat Rp" . number_format($expectedFinalAmount, 0, ',', '.') . " (total - DP)."
            ])->withInput();
        }

        $path = $request->file('final_payment_proof')->store('bukti_pelunasan', 'public');

        $booking->update([
            'final_payment_amount' => $request->final_payment_amount,
            'final_payment_proof' => $path,
            'status' => 'completed',
        ]);

        session()->flash('success', 'Pelunasan berhasil dikonfirmasi.');

        $message = "Halo kak {$booking->contact_name},\n\n"
            . "Kami telah menerima pelunasan untuk sesi {$booking->session_name} pada tanggal "
            . Carbon::parse($booking->booking_date)->translatedFormat('d F Y') . ".\n"
            . "Terima kasih telah mempercayakan layanan kami. See you next time! ❤️";

        $whatsappNumber = preg_replace('/[^0-9]/', '', $booking->whatsapp_number);
        if (str_starts_with($whatsappNumber, '0')) {
            $whatsappNumber = '62' . substr($whatsappNumber, 1);
        }

        $url = 'https://wa.me/' . $whatsappNumber . '?text=' . urlencode($message);

        return redirect()->away($url);
    }

    public function destroy(Booking $booking)
    {
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