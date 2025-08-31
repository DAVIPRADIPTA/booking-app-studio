<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Booking;

class CustomerController extends Controller
{
    /**
     * Menampilkan riwayat booking customer.
     */
    public function bookings()
    {
        $bookings = Booking::where('customer_id', Auth::guard('customer')->id())
                           ->orderByDesc('created_at')
                           ->paginate(10); // ✅ gunakan paginate agar links() bisa dipakai

        return view('customer.bookings.index', compact('bookings'));
    }
}
