<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Customer;
use Illuminate\Http\Request;

class AdminBookingController extends Controller
{
    /**
     * List semua booking.
     */
    public function index()
    {
        $bookings = Booking::latest()->get();
        return view('admin.bookings.index', compact('bookings'));
    }

    /**
     * Tampilkan detail booking.
     */
    public function show($id)
    {
        $booking = Booking::findOrFail($id);
        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Form tambah booking manual.
     */
    public function create()
    {
        $customers = Customer::all();
        return view('admin.bookings.create', compact('customers'));
    }

    /**
     * Simpan booking manual dari admin.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id'        => 'required|exists:customers,id',
            'contact_name'       => 'required|string|max:100',
            'whatsapp_number'    => 'required|string|max:20',
            'booking_date'       => 'required|date',
            'booking_time'       => 'required|string|max:20',
            'session_name'       => 'required|string|max:100',
            'package_name'       => 'required|string|max:100',
            'total_price'        => 'required|numeric|min:0',
            'payment_method'     => 'required|in:cash,transfer',
            'status'             => 'required|in:waiting_payment,booked',
            'payment_proof'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // 🔒 Cek bentrok slot agar tidak double booking
        $exists = Booking::where('booking_date', $request->booking_date)
            ->where('booking_time', $request->booking_time)
            ->whereIn('status', ['waiting_payment','pending_verification','booked'])
            ->exists();

        if ($exists) {
            return back()->with('errorMessage', 'Slot sudah terisi, pilih waktu lain.');
        }

        // 🔒 Kalau transfer + langsung booked → wajib upload bukti transfer
        if ($request->payment_method === 'transfer' && $request->status === 'booked' && !$request->hasFile('payment_proof')) {
            return back()->with('errorMessage', 'Harus upload bukti transfer jika status langsung Booked.');
        }

        $paymentProofPath = null;
        if ($request->hasFile('payment_proof')) {
            $paymentProofPath = $request->file('payment_proof')->store('payment_proofs', 'public');
        }

        $booking = Booking::create([
            'customer_id' => $request->customer_id,
            'contact_name' => $request->contact_name,
            'whatsapp_number' => $request->whatsapp_number,
            'booking_date' => $request->booking_date,
            'booking_time' => $request->booking_time,
            'session_name' => $request->session_name,
            'package_name' => $request->package_name,
            'selected_backgrounds' => $request->selected_backgrounds ?? [],
            'selected_extra_items' => $request->selected_extra_items ?? [],
            'total_price' => $request->total_price,
            'status' => $request->status,
            'payment_method' => $request->payment_method,
            'payment_proof' => $paymentProofPath,
            'payment_deadline' => $request->status === 'waiting_payment' ? now()->addMinutes(10) : null,
        ]);

        return redirect()->route('admin.bookings.index')->with('successMessage', 'Booking berhasil ditambahkan.');
    }

    /**
     * Verifikasi pembayaran customer.
     */
    public function verifyPayment($id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->status !== 'pending_verification') {
            return back()->with('errorMessage', 'Pesanan ini tidak menunggu verifikasi.');
        }

        $booking->update(['status' => 'booked']);
        return back()->with('successMessage', 'Pembayaran berhasil diverifikasi.');
    }

    /**
     * Batalkan booking (permintaan customer).
     */
    public function cancelBooking($id)
    {
        $booking = Booking::findOrFail($id);
        if ($booking->status === 'cancelled') {
            return back()->with('errorMessage', 'Pesanan sudah dibatalkan sebelumnya.');
        }

        $booking->update([
            'status' => 'cancelled',
            'auto_cancelled_at' => now(),
            'cancellation_reason' => 'Dibatalkan oleh admin sesuai permintaan customer.',
        ]);

        return back()->with('successMessage', 'Pesanan berhasil dibatalkan.');
    }

    /**
     * Paksa batalkan booking (admin override).
     */
    public function forceCancel($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update([
            'status' => 'cancelled',
            'auto_cancelled_at' => now(),
            'cancellation_reason' => 'Booking dibatalkan oleh admin.',
        ]);

        return back()->with('successMessage', 'Pesanan berhasil dibatalkan paksa.');
    }

    /**
     * Tandai booking selesai.
     */
    public function completeBooking($id)
    {
        $booking = Booking::findOrFail($id);
        if ($booking->status !== 'booked') {
            return back()->with('errorMessage', 'Pesanan belum bisa ditandai selesai.');
        }

        $booking->update(['status' => 'completed']);
        return back()->with('successMessage', 'Pesanan berhasil ditandai selesai.');
    }

    /**
     * Proses pembatalan + refund.
     */
    public function processCancellation(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $request->validate([
            'refund_amount' => 'required|numeric|min:0',
            'refund_proof' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $refundPath = null;
        if ($request->hasFile('refund_proof')) {
            $refundPath = $request->file('refund_proof')->store('refund_proofs', 'public');
        }

        $booking->update([
            'status' => 'cancelled',
            'refund_amount' => $request->refund_amount,
            'refund_proof' => $refundPath,
            'auto_cancelled_at' => now(),
            'cancellation_reason' => 'Dibatalkan dan refund diproses oleh admin.',
        ]);

        return back()->with('successMessage', 'Refund berhasil diproses.');
    }
}
