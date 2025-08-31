@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Tambah Booking Manual</h2>

    <form action="{{ route('bookings.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-4">
            <label class="font-semibold">Customer</label>
            <select name="customer_id" class="form-control" required>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->email }})</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="font-semibold">Nama Kontak</label>
            <input type="text" name="contact_name" class="form-control" required>
        </div>

        <div class="mb-4">
            <label class="font-semibold">No. WhatsApp</label>
            <input type="text" name="whatsapp_number" class="form-control" required>
        </div>

        <div class="mb-4">
            <label class="font-semibold">Tanggal Booking</label>
            <input type="date" name="booking_date" class="form-control" required>
        </div>

        <div class="mb-4">
            <label class="font-semibold">Waktu Booking</label>
            <input type="time" name="booking_time" class="form-control" required>
        </div>

        <div class="mb-4">
            <label class="font-semibold">Nama Sesi</label>
            <input type="text" name="session_name" class="form-control" required>
        </div>

        <div class="mb-4">
            <label class="font-semibold">Paket</label>
            <input type="text" name="package_name" class="form-control" required>
        </div>

        <div class="mb-4">
            <label class="font-semibold">Total Harga</label>
            <input type="number" name="total_price" class="form-control" required>
        </div>

        <div class="mb-4">
            <label class="font-semibold">Metode Pembayaran</label>
            <select name="payment_method" class="form-control" required>
                <option value="cash">Cash (langsung lunas)</option>
                <option value="transfer">Transfer (menunggu pembayaran)</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="font-semibold">Bukti Pembayaran (Opsional)</label>
            <input type="file" name="payment_proof" class="form-control">
            <small class="text-gray-500">Upload jika pembayaran transfer sudah dilakukan.</small>
        </div>

        <button type="submit" class="btn btn-primary w-full">Simpan Booking</button>
    </form>
</div>
@endsection
