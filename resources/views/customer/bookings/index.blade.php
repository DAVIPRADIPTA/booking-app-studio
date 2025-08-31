@extends('layouts.app')

@section('title', 'Riwayat Pemesanan')

@section('content')
<div class="max-w-5xl mx-auto bg-white shadow-lg rounded-lg p-6 mt-8">
    <h2 class="text-2xl font-bold mb-4">Riwayat Pemesanan</h2>

    @if($bookings->isEmpty())
        <p class="text-gray-600">Belum ada riwayat pemesanan.</p>
    @else
        <table class="min-w-full border-collapse border border-gray-300">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border p-2">Tanggal</th>
                    <th class="border p-2">Waktu</th>
                    <th class="border p-2">Paket</th>
                    <th class="border p-2">Total</th>
                    <th class="border p-2">Status</th>
                    <th class="border p-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $booking)
                    <tr>
                        <td class="border p-2">{{ $booking->booking_date }}</td>
                        <td class="border p-2">{{ $booking->booking_time }}</td>
                        <td class="border p-2">{{ $booking->package_name }}</td>
                        <td class="border p-2">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                        <td class="border p-2">
                            @if($booking->status === 'waiting_payment')
                                <span class="text-yellow-600 font-semibold">Menunggu Pembayaran</span>
                            @elseif($booking->status === 'pending_verification')
                                <span class="text-blue-600 font-semibold">Menunggu Verifikasi</span>
                            @elseif($booking->status === 'booked')
                                <span class="text-green-600 font-semibold">Dikonfirmasi</span>
                            @elseif($booking->status === 'completed')
                                <span class="text-gray-600 font-semibold">Selesai</span>
                            @else
                                <span class="text-red-600 font-semibold">Dibatalkan</span>
                            @endif
                        </td>
                        <td class="border p-2 text-center">
                            @if($booking->status === 'waiting_payment')
                                <a href="{{ route('booking.payment', $booking->id) }}" 
                                   class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
                                    Bayar Sekarang
                                </a>
                            @elseif($booking->status === 'pending_verification')
                                <span class="text-sm text-gray-500">Menunggu verifikasi admin</span>
                            @elseif($booking->status === 'booked')
                                <span class="text-sm text-green-600">Sudah terkonfirmasi</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
