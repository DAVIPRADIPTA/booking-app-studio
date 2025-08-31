@extends('layouts.app')

@section('title', 'Pembayaran Manual')

@section('content')
<div class="max-w-2xl mx-auto bg-white shadow-lg rounded-lg p-6 mt-8">
    <h2 class="text-2xl font-bold mb-4 text-center">Instruksi Pembayaran</h2>

    {{-- Info Booking --}}
    <div class="mb-6">
        <p><strong>Nama Kontak:</strong> {{ $booking->contact_name }}</p>
        <p><strong>No. WhatsApp:</strong> {{ $booking->whatsapp_number }}</p>
        <p><strong>Tanggal Booking:</strong> {{ $booking->booking_date }}</p>
        <p><strong>Waktu Booking:</strong> {{ $booking->booking_time }}</p>
        <p><strong>Paket:</strong> {{ $booking->package_name }}</p>
        <p><strong>Total Harga:</strong> Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
    </div>

    {{-- Countdown --}}
    <div class="mb-6 text-center">
        <h3 class="text-lg font-semibold">Batas Waktu Pembayaran</h3>
        <p id="countdown" class="text-2xl font-bold text-red-600"></p>
    </div>

    {{-- Instruksi Transfer --}}
    <div class="mb-6 bg-gray-100 rounded p-4">
        <h3 class="font-semibold mb-2">Silakan transfer ke rekening berikut:</h3>
        <p><strong>BANK BCA</strong></p>
        <p>No. Rekening: <strong>1234567890</strong></p>
        <p>a.n <strong>Peace Picture Studio</strong></p>
        <p class="mt-2 text-sm text-gray-600">Setelah transfer, upload bukti pembayaran sebelum waktu habis.</p>
    </div>

    {{-- Upload Bukti Pembayaran --}}
    <form action="{{ route('booking.uploadProof', $booking->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
        <div>
            <label for="payment_proof" class="block text-sm font-medium text-gray-700">Upload Bukti Pembayaran</label>
            <input type="file" name="payment_proof" id="payment_proof" required
                   class="mt-1 block w-full text-sm text-gray-700 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            @error('payment_proof')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">
            Upload Bukti Pembayaran
        </button>
    </form>
</div>

{{-- Countdown Script --}}
<script>
    const deadline = {{ $booking->payment_deadline->timestamp }} * 1000;

    function updateCountdown() {
        const now = new Date().getTime();
        const distance = deadline - now;

        if (distance <= 0) {
            document.getElementById("countdown").innerHTML = "Waktu pembayaran telah habis!";
            return;
        }

        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        document.getElementById("countdown").innerHTML = minutes + "m " + seconds + "s ";
    }

    setInterval(updateCountdown, 1000);
    updateCountdown();
</script>
@endsection
