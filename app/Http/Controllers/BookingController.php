<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Background;
use App\Models\ExtraItem;
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

        // 📌 Validasi dasar
        $rules = [
            'contact_name'     => 'required|string|max:100',
            'whatsapp_number'  => 'required|string|max:20',
            'booking_date'     => 'required|date|after_or_equal:today',
            'booking_time'     => 'required|string',
            'session_name'     => 'required|string|max:100',
            'package_name'     => 'required|string|max:100',
            'total_price'      => 'required|numeric|min:0',
            'notes'            => 'nullable|string',
            'selected_backgrounds' => 'nullable|array',
            'selected_extra_items' => 'nullable|array',
            'baby_name'        => 'nullable|string|max:255',
            'baby_age'         => 'nullable|string|max:50',
        ];

        // 📌 Kalau admin → wajib isi customer_id & payment_method
        if ($isAdmin && !$isCustomer) {
            $rules['customer_id']    = 'required|exists:customers,id';
            $rules['payment_method'] = 'required|in:cash,transfer';
        }

        $data = $request->validate($rules);

        // --- Backgrounds: simpan data lengkap ---
        $backgrounds = [];
        if ($request->filled('selected_backgrounds')) {
            $backgrounds = Background::whereIn('id', (array)$request->selected_backgrounds)
                ->get(['id', 'name', 'image'])
                ->map(fn($bg) => [
                    'id'    => $bg->id,
                    'name'  => $bg->name,
                    'image' => $bg->image,
                ])->values()->toArray();
        }

        // --- Extra items: simpan data lengkap ---
        $extras = [];
        if ($request->filled('selected_extra_items')) {
            $extras = ExtraItem::whereIn('id', (array)$request->selected_extra_items)
                ->get(['id', 'name', 'price'])
                ->map(fn($ex) => [
                    'id'    => $ex->id,
                    'name'  => $ex->name,
                    'price' => (int) $ex->price,
                ])->values()->toArray();
        }

        $data['selected_backgrounds'] = $backgrounds;
        $data['selected_extra_items'] = $extras;

        // --- Mode Customer Online ---
        if ($isCustomer) {
            $data['customer_id']      = Auth::guard('customer')->id();
            $data['payment_method']   = 'transfer';
            $data['status']           = 'waiting_payment';
            $data['payment_deadline'] = now()->addMinutes(10);
        }

        // --- Mode Admin Offline ---
        if ($isAdmin && !$isCustomer) {
            if ($data['payment_method'] === 'cash') {
                $data['status'] = 'booked';
            } else {
                $data['status']           = 'waiting_payment';
                $data['payment_deadline'] = now()->addMinutes(10);
            }
        }

        // --- Cek bentrok slot ---
        $exists = Booking::where('booking_date', $data['booking_date'])
            ->where('booking_time', $data['booking_time'])
            ->whereIn('status', ['waiting_payment', 'pending_verification', 'booked'])
            ->exists();

        if ($exists) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Slot sudah terisi, pilih waktu lain.'
                ], 409);
            }
            return back()->with('errorMessage', 'Slot sudah terisi, pilih waktu lain.');
        }

        try {
            $booking = Booking::create($data);

            // 📌 Customer via AJAX (fetch)
            if ($isCustomer && $request->expectsJson()) {
                return response()->json([
                    'message' => 'Pesanan berhasil dibuat, silakan lakukan pembayaran.',
                    'redirect_url' => route('booking.payment', $booking)
                ], 201);
            }

            // 📌 Customer biasa
            if ($isCustomer) {
                return redirect()
                    ->route('booking.payment', $booking)
                    ->with('successMessage', 'Pesanan berhasil dibuat, silakan lakukan pembayaran.');
            }

            // 📌 Admin offline
            if ($isAdmin && !$isCustomer) {
                return redirect()
                    ->route('bookings.index')
                    ->with('successMessage', 'Booking manual berhasil ditambahkan.');
            }

            abort(403, 'Unauthorized');
        } catch (\Exception $e) {
            Log::error('Booking gagal: ' . $e->getMessage(), ['payload' => $data]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Terjadi kesalahan, coba lagi.',
                    'error'   => $e->getMessage()
                ], 500);
            }

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
                'status'        => 'pending_verification',
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
            'status'         => $booking->status,
            'remaining_time' => $booking->getRemainingPaymentTime(),
            'deadline'       => $booking->payment_deadline?->timestamp,
        ]);
    }
}
