<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    /**
     * Store booking (customer online / admin offline).
     */
    public function store(Request $request)
    {
        $isCustomer = Auth::guard('customer')->check();
        $isAdmin    = Auth::guard('web')->check();

        // Base rules
        $rules = [
            'contact_name'     => 'required|string|max:100',
            'whatsapp_number'  => 'required|string|max:20',
            'booking_date'     => 'required|date',
            'booking_time'     => 'required|string',
            'session_name'     => 'required|string',
            'package_name'     => 'required|string',
            'total_price'      => 'required|numeric',
            'notes'            => 'nullable|string',
            'selected_backgrounds' => 'nullable|array',
            'selected_extra_items' => 'nullable|array',
        ];

        // Kalau admin, wajib isi customer_id & payment_method
        if ($isAdmin) {
            $rules['customer_id']    = 'required|exists:customers,id';
            $rules['payment_method'] = 'required|in:cash,transfer';
        }

        $data = $request->validate($rules);

        // 🚀 Customer booking online
        if ($isCustomer) {
            $data['customer_id']      = Auth::guard('customer')->id();
            $data['payment_method']   = 'transfer';
            $data['status']           = 'waiting_payment';
            $data['payment_deadline'] = now()->addMinutes(10);
        }

        // 🚀 Admin booking offline
        if ($isAdmin) {
            if ($data['payment_method'] === 'cash') {
                $data['status'] = 'booked';
            } else {
                $data['status'] = 'waiting_payment';
                $data['payment_deadline'] = now()->addMinutes(10);
            }
        }

        // Cek bentrok slot
        $exists = Booking::where('booking_date', $data['booking_date'])
            ->where('booking_time', $data['booking_time'])
            ->whereIn('status', ['waiting_payment', 'pending_verification', 'booked'])
            ->exists();

        if ($exists) {
            return back()->with('errorMessage', 'Slot sudah terisi, pilih waktu lain.');
        }

        try {
            $booking = Booking::create($data);

            if ($isCustomer) {
                return redirect()
                    ->route('booking.payment', $booking)
                    ->with('successMessage', 'Pesanan berhasil dibuat, silakan lakukan pembayaran.');
            }

            if ($isAdmin) {
                return redirect()
                    ->route('bookings.index')
                    ->with('successMessage', 'Booking manual berhasil ditambahkan.');
            }

            abort(403, 'Unauthorized');
        } catch (\Exception $e) {
            Log::error('Booking gagal: ' . $e->getMessage(), ['payload' => $data]);
            return back()->with('errorMessage', 'Terjadi kesalahan, coba lagi.');
        }
    }


    /**
     * Halaman pembayaran customer.
     */
    public function payment(Booking $booking)
    {
        if ($booking->customer_id !== Auth::guard('customer')->id()) {
            abort(403);
        }

        if ($booking->needsAutoCancellation()) {
            $booking->autoCancel();
        }

        if ($booking->status !== 'waiting_payment') {
            return redirect()->route('customer.bookings')
                ->with('errorMessage', 'Status pesanan tidak valid');
        }

        return view('customer.payments.manual', compact('booking'));
    }

    /**
     * Upload bukti transfer (customer).
     */
    public function uploadProof(Request $request, Booking $booking)
    {
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            $path = $request->file('payment_proof')->store('payment_proofs', 'public');

            $booking->update([
                'payment_proof' => $path,
                'status' => 'pending_verification',
            ]);

            return redirect()->route('customer.bookings')
                ->with('successMessage', 'Bukti pembayaran berhasil diunggah.');
        } catch (\Exception $e) {
            Log::error('Upload bukti gagal: ' . $e->getMessage());
            return back()->with('errorMessage', 'Upload gagal, coba lagi.');
        }
    }

    /**
     * API check status booking (countdown).
     */
    public function checkStatus(Booking $booking)
    {
        if ($booking->needsAutoCancellation()) {
            $booking->autoCancel();
        }

        return response()->json([
            'status' => $booking->status,
            'remaining_time' => $booking->getRemainingPaymentTime(),
            'deadline' => $booking->payment_deadline?->timestamp,
        ]);
    }
}
