@extends('layouts.app')

@section('title', 'Edit Booking - Admin')

@push('styles')
<style>
/* Import Dancing Script Font */
@import url('https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap');

.font-dancing {
    font-family: 'Dancing Script', cursive;
}

/* Main Container */
.main-container {
    min-height: 100vh;
    position: relative;
    padding-bottom: 5rem;
    overflow: visible;
}

/* Clean Professional Background */
.cinematic-bg {
    background-image: url('{{ asset("images/prewed/3.jpg") }}');
    background-position: center center;
    background-size: cover;
    background-repeat: no-repeat;
    position: absolute;
    inset: 0;
    z-index: 0;
    filter: brightness(0.5) contrast(1.1);
}

/* Clean Dark Overlay */
.cinematic-overlay {
    background: linear-gradient(135deg, rgba(0, 0, 0, 0.8) 0%, rgba(0, 0, 0, 0.6) 50%, rgba(0, 0, 0, 0.8) 100%);
    position: absolute;
    inset: 0;
    z-index: 1;
}

/* Content Wrapper - Clean */
.content-wrapper {
    position: relative;
    z-index: 20;
    padding: clamp(2rem, 4vw, 3rem) clamp(1rem, 2vw, 1.5rem) clamp(5rem, 6vw, 6rem);
    min-height: 100vh;
    overflow: visible;
    display: flex;
    flex-direction: column;
    align-items: center;
    max-width: 100vw;
}

/* Hero Section - Elegant */
.hero-section {
    text-align: center;
    margin-bottom: clamp(3rem, 5vw, 4rem);
    position: relative;
    z-index: 10;
    width: 100%;
    max-width: 900px;
    padding: clamp(2rem, 4vw, 3rem) clamp(1.5rem, 3vw, 2rem);
    animation: fadeInUp 0.8s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.hero-section h1 {
    font-size: clamp(1.8rem, 3.5vw, 2.3rem);
    font-weight: 600;
    color: white;
    text-align: center;
    margin-bottom: clamp(2.5rem, 4vw, 3rem);
    position: relative;
    z-index: 10;
    text-shadow: 0 2px 12px rgba(0, 0, 0, 0.6);
}

/* Package Notice */
.package-notice {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.18) 0%, rgba(59, 130, 246, 0.10) 100%);
    border: 1px solid rgba(59, 130, 246, 0.4);
    border-radius: clamp(0.6rem, 1.2vw, 0.9rem);
    padding: clamp(1rem, 2vw, 1.3rem);
    margin-bottom: clamp(1.5rem, 3vw, 2rem);
    color: #93c5fd;
    font-size: clamp(0.8rem, 1.3vw, 0.9rem);
    text-align: center;
    backdrop-filter: blur(25px);
}

/* Package Selection */
.package-card {
    position: relative;
    border: 2px solid #e5e7eb;
    border-radius: 1rem;
    padding: 1.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #f9fafb;
    text-align: center;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.package-card:hover {
    border-color: #dc2626;
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.package-card.selected {
    border-color: #dc2626;
    background: #f0f9ff;
    box-shadow: 0 10px 25px rgba(220, 38, 38, 0.2);
}

.package-card.selected::after {
    content: '✓';
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    width: 2rem;
    height: 2rem;
    background: #dc2626;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

.package-card .package-price {
    color: #dc2626;
}

.package-title {
    font-family: 'Dancing Script', cursive;
    font-size: clamp(1.4rem, 2.5vw, 1.7rem);
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.6rem;
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.package-price {
    font-size: clamp(1.2rem, 2vw, 1.4rem);
    font-weight: 700;
    color: #1f2937;
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.package-features {
    list-style: none;
    padding: 0;
    margin: 0;
    position: relative;
    z-index: 10;
}

.package-features li {
    font-size: clamp(0.8rem, 1.5vw, 0.95rem);
    color: #4b5563;
    margin-bottom: 0.9rem;
    position: relative;
    padding-left: 1.2rem;
}

.package-features li::before {
    content: "•";
    color: #dc2626;
    font-weight: bold;
    position: absolute;
    left: 0;
    top: 0;
}

.package-note {
    margin-top: 0.5rem;
    font-size: 0.8rem;
    color: #ef4444;
}

/* Background Selection */
.background-option {
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.background-option::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.6s ease;
    z-index: 1;
}

.background-option:hover::before {
    left: 100%;
}

.background-option.selected::after {
    content: '✓';
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    width: 1.8rem;
    height: 1.8rem;
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 0.9rem;
    opacity: 0;
    transform: scale(0) rotate(-180deg);
    transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    box-shadow: 0 4px 15px rgba(220, 38, 38, 0.4);
    z-index: 2;
}

.background-option.selected::after {
    opacity: 1;
    transform: scale(1) rotate(0deg);
}

.background-option.selecting {
    transform: scale(0.95);
    transition: transform 0.2s ease;
}

/* Pulse animation for newly selected backgrounds - Deep Red */
@keyframes backgroundPulse {
    0% { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.7); }
    70% { box-shadow: 0 0 0 12px rgba(220, 38, 38, 0); }
    100% { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0); }
}

.background-option.pulse {
    animation: backgroundPulse 1.5s;
}

/* Form Elements */
.form-input, .form-select {
    width: 100%;
    padding: clamp(0.9rem, 1.8vw, 1.2rem) clamp(1.2rem, 2.5vw, 1.5rem);
    border: 1px solid #d1d5db;
    border-radius: clamp(0.6rem, 1.2vw, 0.9rem);
    background: white;
    font-size: clamp(0.85rem, 1.5vw, 0.95rem);
    color: #1f2937;
    transition: all 0.3s ease;
}

.form-input:focus, .form-select:focus {
    outline: none;
    border-color: #dc2626;
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
}

/* Availability Info */
.availability-info {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(59, 130, 246, 0.05) 100%);
    border: 1px solid #93c5fd;
    border-radius: clamp(0.6rem, 1.2vw, 0.9rem);
    padding: clamp(1rem, 2vw, 1.3rem);
    margin-top: 0.5rem;
    color: #1e40af;
    font-size: clamp(0.8rem, 1.3vw, 0.9rem);
    text-align: center;
}

.availability-info.limited {
    background: linear-gradient(135deg, rgba(234, 179, 8, 0.1) 0%, rgba(234, 179, 8, 0.05) 100%);
    border: 1px solid #fde047;
    color: #854d0e;
}

.availability-info.full {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(239, 68, 68, 0.05) 100%);
    border: 1px solid #fca5a5;
    color: #b91c1c;
}

/* Background Counter */
.background-counter {
    position: absolute;
    top: -2.5rem;
    right: 0.5rem;
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    color: white;
    border-radius: 50%;
    width: 2rem;
    height: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.9rem;
    box-shadow: 0 4px 15px rgba(220, 38, 38, 0.4);
}

/* Total Payment Card */
.total-payment-card {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0.08) 100%);
    border: 2px solid rgba(255, 255, 255, 0.25);
    border-radius: clamp(1.2rem, 2.5vw, 1.8rem);
    padding: clamp(1.5rem, 3vw, 2rem);
    backdrop-filter: blur(35px);
    box-shadow: 0 30px 80px rgba(0, 0, 0, 0.5);
    position: relative;
    overflow: hidden;
    text-align: center;
}

.total-amount {
    font-size: clamp(1.8rem, 4vw, 2.5rem);
    font-weight: 800;
    color: rgba(220, 38, 38, 0.95);
    text-shadow: 0 2px 12px rgba(220, 38, 38, 0.4);
    transition: all 0.4s ease;
}

.total-amount.updating {
    transform: scale(1.05);
    color: rgba(239, 68, 68, 0.95);
    text-shadow: 0 2px 12px rgba(239, 68, 68, 0.4);
}

/* Submit Button */
.submit-section {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: clamp(1rem, 2vw, 1.5rem);
    position: relative;
    z-index: 10;
}

.submit-btn-premium {
    width: 100%;
    max-width: min(420px, calc(100vw - 2rem));
    background: linear-gradient(135deg, rgba(220, 38, 38, 0.9) 0%, rgba(185, 28, 28, 0.9) 50%, rgba(153, 27, 27, 0.9) 100%);
    border: 2px solid rgba(220, 38, 38, 0.6);
    border-radius: clamp(1rem, 2vw, 1.5rem);
    padding: 0;
    color: white;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    backdrop-filter: blur(30px);
    position: relative;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(220, 38, 38, 0.4);
    font-family: inherit;
    display: flex;
    align-items: center;
    justify-content: center;
    height: clamp(3.2rem, 6vw, 4rem);
    font-size: clamp(1rem, 2vw, 1.2rem);
    font-weight: 600;
}

.submit-btn-premium:hover {
    transform: translateY(-3px);
    box-shadow: 0 25px 70px rgba(220, 38, 38, 0.5);
    background: linear-gradient(135deg, rgba(185, 28, 28, 0.95) 0%, rgba(153, 27, 27, 0.95) 50%, rgba(127, 23, 23, 0.95) 100%);
}

.submit-btn-premium:active {
    transform: translateY(0);
}

.submit-btn-premium.loading {
    background: linear-gradient(135deg, rgba(100, 100, 100, 0.9) 0%, rgba(80, 80, 80, 0.9) 50%, rgba(60, 60, 60, 0.9) 100%);
    cursor: not-allowed;
}

.submit-note {
    display: flex;
    align-items: flex-start;
    justify-content: center;
    gap: 0.6rem;
    font-size: clamp(0.7rem, 1.2vw, 0.8rem);
    color: rgba(255, 255, 255, 0.6);
    text-align: center;
    max-width: 400px;
    margin: 0 auto;
    line-height: 1.4;
    text-shadow: 0 1px 3px rgba(0, 0, 0, 0.6);
}

.submit-note svg {
    flex-shrink: 0;
    opacity: 0.7;
    margin-top: 0.1rem;
}

/* Success Message - Deep Red Theme */
.success-message {
    background: linear-gradient(135deg, rgba(220, 38, 38, 0.30), rgba(220, 38, 38, 0.18));
    border: 1px solid rgba(220, 38, 38, 0.6);
    border-radius: clamp(0.7rem, 1.4vw, 1rem);
    padding: clamp(1.2rem, 2.5vw, 1.8rem);
    margin-bottom: 1.8rem;
    color: #fca5a5;
    font-size: clamp(0.85rem, 1.5vw, 0.95rem);
    text-align: center;
    display: none;
    backdrop-filter: blur(30px);
    box-shadow: 0 20px 50px rgba(220, 38, 38, 0.3);
    position: relative;
    z-index: 10;
}

.success-message.show {
    display: block;
}

/* Error Messages */
.error-message {
    display: block;
    min-height: 1.2rem;
    color: #ef4444;
    font-size: 0.8rem;
    margin-top: 0.4rem;
    text-shadow: 0 1px 3px rgba(0, 0, 0, 0.6);
    font-weight: 500;
}

/* Loading Animation */
.loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top-color: white;
    animation: spin 1s linear infinite;
    margin-right: 8px;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Booking History */
.booking-history {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.12), rgba(255, 255, 255, 0.08));
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: clamp(0.8rem, 1.6vw, 1.2rem);
    padding: clamp(1.5rem, 3vw, 2rem);
    margin-top: clamp(1.5rem, 3vw, 2rem);
    backdrop-filter: blur(20px);
}

.booking-history h3 {
    font-size: clamp(1.2rem, 2.2vw, 1.5rem);
    color: #f0f9ff;
    margin-bottom: clamp(1rem, 2vw, 1.3rem);
    display: flex;
    align-items: center;
    gap: 0.6rem;
}

.booking-history-item {
    background: rgba(255, 255, 255, 0.08);
    border-radius: clamp(0.6rem, 1.2vw, 0.9rem);
    padding: clamp(1rem, 2vw, 1.3rem);
    margin-bottom: clamp(0.8rem, 1.6vw, 1rem);
    transition: all 0.2s ease;
}

.booking-history-item:hover {
    background: rgba(255, 255, 255, 0.15);
    transform: translateX(4px);
}

.booking-history-item p {
    margin: 0.3rem 0;
    color: #e0f2fe;
    font-size: clamp(0.8rem, 1.5vw, 0.9rem);
}

.booking-history-item strong {
    color: #f8fafc;
}

/* Admin Notes Section */
.admin-notes {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.12), rgba(255, 255, 255, 0.08));
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: clamp(0.8rem, 1.6vw, 1.2rem);
    padding: clamp(1.5rem, 3vw, 2rem);
    margin-top: clamp(1.5rem, 3vw, 2rem);
    backdrop-filter: blur(20px);
}

.admin-notes h3 {
    font-size: clamp(1.2rem, 2.2vw, 1.5rem);
    color: #f0f9ff;
    margin-bottom: clamp(1rem, 2vw, 1.3rem);
    display: flex;
    align-items: center;
    gap: 0.6rem;
}

.admin-notes textarea {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    resize: vertical;
    min-height: 100px;
}

.admin-notes textarea:focus {
    border-color: #dc2626;
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
}

/* Status Badge */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.4rem 0.8rem;
    border-radius: 9999px;
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.status-waiting_payment {
    background: rgba(234, 179, 8, 0.2);
    border: 1px solid rgba(234, 179, 8, 0.4);
    color: #ca8a04;
}

.status-pending_verification {
    background: rgba(251, 146, 60, 0.2);
    border: 1px solid rgba(251, 146, 60, 0.4);
    color: #ea580c;
}

.status-booked {
    background: rgba(34, 197, 94, 0.2);
    border: 1px solid rgba(34, 197, 94, 0.4);
    color: #166534;
}

.status-completed {
    background: rgba(59, 130, 246, 0.2);
    border: 1px solid rgba(59, 130, 246, 0.4);
    color: #1e40af;
}

.status-cancelled {
    background: rgba(239, 68, 68, 0.2);
    border: 1px solid rgba(239, 68, 68, 0.4);
    color: #b91c1c;
}

/* Disabled State */
.disabled-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.05);
    z-index: 10;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border-radius: 0.5rem;
}

.disabled-overlay-content {
    background: rgba(255, 255, 255, 0.9);
    padding: 1.5rem;
    border-radius: 0.5rem;
    text-align: center;
    max-width: 80%;
}
</style>
@endpush

@section('content')
<div class="main-container">
    <div class="cinematic-bg"></div>
    <div class="cinematic-overlay"></div>
    
    <div class="content-wrapper">
        <div class="hero-section">
            <h1>Edit Booking #{{ $booking->id }}</h1>
            
            <div class="package-notice">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <strong>Penting:</strong> Hanya booking dengan status "Sudah Dibooking" yang bisa di-edit.
                </div>
            </div>
        </div>

        <div class="max-w-6xl mx-auto bg-white shadow-lg rounded-xl p-6">
            <form action="{{ route('bookings.update', $booking->id) }}" method="POST" enctype="multipart/form-data" id="adminBookingForm" class="space-y-8">
                @csrf
                @method('PUT')

                <!-- Hanya tampilkan form jika statusnya booked -->
                @if($booking->status === 'booked')
                    <!-- Pilih Customer -->
                    <div class="bg-gray-50 p-6 rounded-lg border">
                        <label class="block font-semibold text-gray-700 mb-3">Pilih Customer</label>
                        <select name="customer_id" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                            <option value="">-- Pilih Customer --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ $booking->customer_id == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }} ({{ $customer->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nama Kontak & WhatsApp -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 p-6 rounded-lg border">
                            <label class="block font-semibold text-gray-700 mb-2">Nama Kontak</label>
                            <input type="text" name="contact_name" value="{{ old('contact_name', $booking->contact_name) }}" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                            @error('contact_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="bg-gray-50 p-6 rounded-lg border">
                            <label class="block font-semibold text-gray-700 mb-2">Nomor WhatsApp</label>
                            <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $booking->whatsapp_number) }}" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="081234567890" required>
                            <p class="text-gray-500 text-sm mt-1">Contoh: 081234567890 atau +6281234567890</p>
                            @error('whatsapp_number')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Tanggal & Waktu -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 p-6 rounded-lg border">
                            <label class="block font-semibold text-gray-700 mb-2">Tanggal Pemotretan</label>
                            <input type="date" name="booking_date" id="booking_date" min="{{ now()->format('Y-m-d') }}" value="{{ old('booking_date', $booking->booking_date) }}" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                            @error('booking_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="bg-gray-50 p-6 rounded-lg border">
                            <label class="block font-semibold text-gray-700 mb-2">Waktu Pemotretan</label>
                            <select id="booking_time" name="booking_time" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                                <option value="">Pilih tanggal dulu...</option>
                                @foreach([
                                    '09:00', '09:30', '10:00', '10:30', '11:00', '11:30', 
                                    '13:00', '13:30', '14:00', '14:30', '15:00', '15:30',
                                    '16:00', '16:30', '17:00', '17:30'
                                ] as $time)
                                    <option value="{{ $time }}" {{ old('booking_time', $booking->booking_time) == $time ? 'selected' : '' }}>
                                        {{ $time }} WIB
                                    </option>
                                @endforeach
                            </select>
                            <div id="time-availability-info" class="hidden mt-2 p-3 rounded-lg border">
                                <span id="availability-message"></span>
                            </div>
                            @error('booking_time')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Metode Pembayaran -->
                    <div class="bg-gray-50 p-6 rounded-lg border">
                        <label class="block font-semibold text-gray-700 mb-2">Metode Pembayaran</label>
                        <select name="payment_method" id="payment_method" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                            <option value="cash" {{ old('payment_method', $booking->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="transfer" {{ old('payment_method', $booking->payment_method) == 'transfer' ? 'selected' : '' }}>Transfer</option>
                        </select>
                        
                        <!-- Upload Bukti Transfer -->
                        <div id="uploadProofSection" class="mt-4 {{ old('payment_method', $booking->payment_method) !== 'transfer' ? 'hidden' : '' }}">
                            <label class="block font-semibold text-gray-700 mb-2">Upload Bukti Transfer</label>
                            @if($booking->payment_proof)
                                <div class="mb-3">
                                    <p class="text-gray-700 mb-2">Bukti Pembayaran Saat Ini:</p>
                                    <img src="{{ asset('storage/' . $booking->payment_proof) }}" class="w-full max-h-64 object-contain rounded-lg border mb-2">
                                    <a href="{{ asset('storage/' . $booking->payment_proof) }}" target="_blank" class="text-blue-600 hover:underline">Lihat Bukti Pembayaran</a>
                                </div>
                            @endif
                            <input type="file" name="payment_proof" class="w-full border border-gray-300 rounded-lg p-3" accept="image/*">
                            <p class="text-gray-500 text-sm mt-1">Hanya jika metode pembayaran = Transfer</p>
                        </div>
                    </div>

                    <!-- Pilih Paket -->
                    <div class="package-section-container bg-gray-50 p-6 rounded-lg border">
                        <h2 class="text-xl font-semibold text-gray-700 mb-6">Pilih Paket</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- Baby Smash Cake -->
                            <div class="package-card cursor-pointer border-2 rounded-lg p-4 hover:border-red-500 transition {{ old('package_name', $booking->package_name) == 'Baby Smash Cake' ? 'border-red-500 bg-red-50' : 'border-gray-200' }}"
                                 data-package="Baby Smash Cake" 
                                 data-price="550000" 
                                 data-backgrounds="0"
                                 data-category="baby-smash">
                                <div class="package-header">
                                    <h3 class="package-title font-dancing text-2xl">Baby Smash Cake</h3>
                                    <div class="package-price text-lg font-bold">IDR 550k</div>
                                </div>
                                <ul class="package-features text-sm mt-3 space-y-1">
                                    <li>30 Minutes Photoshoot</li>
                                    <li>1 Concept Background</li>
                                    <li>10 Edited Photos</li>
                                    <li>1 Printed + Frame 12Rs</li>
                                    <li>Max 2 Wardrobes</li>
                                    <li>Google Drive Access (1 Month)</li>
                                </ul>
                                <div class="package-note text-xs text-red-500 mt-2">
                                    <strong>Note:</strong> Cake Not From Studio
                                </div>
                            </div>

                            <!-- Plain -->
                            <div class="package-card cursor-pointer border-2 rounded-lg p-4 hover:border-red-500 transition {{ old('package_name', $booking->package_name) == 'Plain' ? 'border-red-500 bg-red-50' : 'border-gray-200' }}"
                                 data-package="Plain" 
                                 data-price="300000" 
                                 data-backgrounds="1"
                                 data-category="plain">
                                <div class="package-header">
                                    <h3 class="package-title font-dancing text-2xl">Plain</h3>
                                    <div class="package-price text-lg font-bold">IDR 300k</div>
                                </div>
                                <ul class="package-features text-sm mt-3 space-y-1">
                                    <li>20 Menit Photoshoot</li>
                                    <li>Max 4 Persons</li>
                                    <li>1 Background</li>
                                    <li>10 Edited Photos</li>
                                    <li>1 Photo Printed 12RS</li>
                                    <li>Max 1 Wardrobe</li>
                                    <li>Google Drive Access (1 Month)</li>
                                </ul>
                            </div>

                            <!-- Grande -->
                            <div class="package-card cursor-pointer border-2 rounded-lg p-4 hover:border-red-500 transition {{ old('package_name', $booking->package_name) == 'Grande' ? 'border-red-500 bg-red-50' : 'border-gray-200' }}"
                                 data-package="Grande" 
                                 data-price="500000" 
                                 data-backgrounds="2"
                                 data-category="grande">
                                <div class="package-header">
                                    <h3 class="package-title font-dancing text-2xl">Grande</h3>
                                    <div class="package-price text-lg font-bold">IDR 500k</div>
                                </div>
                                <ul class="package-features text-sm mt-3 space-y-1">
                                    <li>35 Menit Photoshoot</li>
                                    <li>Max 4 Persons</li>
                                    <li>2 Background</li>
                                    <li>20 Edited Photos</li>
                                    <li>1 Photo Printed 16RS</li>
                                    <li>Max 2 Wardrobes</li>
                                    <li>Google Drive Access (1 Month)</li>
                                </ul>
                            </div>

                            <!-- Royal -->
                            <div class="package-card cursor-pointer border-2 rounded-lg p-4 hover:border-red-500 transition {{ old('package_name', $booking->package_name) == 'Royal' ? 'border-red-500 bg-red-50' : 'border-gray-200' }}"
                                 data-package="Royal" 
                                 data-price="700000" 
                                 data-backgrounds="4"
                                 data-category="royal">
                                <div class="package-header">
                                    <h3 class="package-title font-dancing text-2xl">Royal</h3>
                                    <div class="package-price text-lg font-bold">IDR 700k</div>
                                </div>
                                <ul class="package-features text-sm mt-3 space-y-1">
                                    <li>50 Menit Photoshoot</li>
                                    <li>Max 4 Persons</li>
                                    <li>4 Background</li>
                                    <li>30 Edited Photos</li>
                                    <li>1 Photo Printed 16RS</li>
                                    <li>Max 2 Wardrobes</li>
                                    <li>Google Drive Access (1 Month)</li>
                                </ul>
                            </div>

                            <!-- Prewed I -->
                            <div class="package-card cursor-pointer border-2 rounded-lg p-4 hover:border-red-500 transition {{ old('package_name', $booking->package_name) == 'Prewed I' ? 'border-red-500 bg-red-50' : 'border-gray-200' }}"
                                 data-package="Prewed I" 
                                 data-price="700000" 
                                 data-backgrounds="2"
                                 data-category="pre-wedding">
                                <div class="package-header">
                                    <h3 class="package-title font-dancing text-2xl">Prewed I</h3>
                                    <div class="package-price text-lg font-bold">IDR 700k</div>
                                </div>
                                <ul class="package-features text-sm mt-3 space-y-1">
                                    <li>50 Minutes Photosession</li>
                                    <li>2 Background</li>
                                    <li>20 Edited Photo</li>
                                    <li>Max 1 Wardrobe</li>
                                    <li>1 Photo Printed 12rs + Frame</li>
                                    <li>Google Drive Expired 1 Month</li>
                                </ul>
                            </div>

                            <!-- Prewed II -->
                            <div class="package-card cursor-pointer border-2 rounded-lg p-4 hover:border-red-500 transition {{ old('package_name', $booking->package_name) == 'Prewed II' ? 'border-red-500 bg-red-50' : 'border-gray-200' }}"
                                 data-package="Prewed II" 
                                 data-price="1000000" 
                                 data-backgrounds="3"
                                 data-category="pre-wedding">
                                <div class="package-header">
                                    <h3 class="package-title font-dancing text-2xl">Prewed II</h3>
                                    <div class="package-price text-lg font-bold">IDR 1000k</div>
                                </div>
                                <ul class="package-features text-sm mt-3 space-y-1">
                                    <li>70 Minutes Photosession</li>
                                    <li>3 Background</li>
                                    <li>40 Edited Photo</li>
                                    <li>Max 2 Wardrobe</li>
                                    <li>2 Photo Printed 16rs + Frame</li>
                                    <li>Google Drive Expired 1 Month</li>
                                </ul>
                            </div>
                        </div>
                        <input type="hidden" name="package_name" id="package_name" value="{{ old('package_name', $booking->package_name) }}">
                        <input type="hidden" name="session_name" id="session_name" value="{{ old('session_name', $booking->session_name) }}">
                        @error('package_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Pilih Background -->
                    <div id="background-section" class="bg-gray-50 p-6 rounded-lg border {{ strtolower(old('package_name', $booking->package_name)) === 'baby smash cake' || strtolower(old('package_name', $booking->package_name)) === 'babysmash' ? 'hidden' : '' }}">
                        <div class="flex justify-between items-center mb-3">
                            <label class="block font-semibold text-gray-700">Pilih Background (Maks: <span id="maxBackgroundsLabel">0</span>)</label>
                            <span class="background-counter" id="backgroundCounter">0</span>
                        </div>
                        <div id="background-container" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            <!-- Background akan diisi oleh JavaScript berdasarkan paket yang dipilih -->
                        </div>
                        <input type="hidden" name="selected_backgrounds" id="selected_backgrounds" value="{{ old('selected_backgrounds', json_encode($selectedBackgrounds)) }}">
                        <p class="text-gray-500 text-sm mt-4">* Silakan pilih minimal 1 background</p>
                        @error('selected_backgrounds')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Extra Items -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Cetak Foto -->
                        <div class="bg-gray-50 p-6 rounded-lg border">
                            <h3 class="font-semibold text-gray-700 mb-3">Cetak Foto</h3>
                            <div class="space-y-3">
                                @foreach($printItems as $item)
                                <label class="flex items-center p-2 border rounded hover:bg-gray-50">
                                    <input type="checkbox" name="selected_extra_items[]" value="{{ $item->id }}" 
                                           class="h-4 w-4 text-red-600 rounded focus:ring-red-500 extra-checkbox"
                                           data-name="{{ $item->name }}"
                                           data-price="{{ $item->price }}"
                                           @if(isset($selectedExtraItems) && in_array($item->id, $selectedExtraItems)) checked @endif>
                                    <span class="ml-2 text-gray-700">{{ $item->name }} (IDR {{ number_format($item->price) }})</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Frame Foto -->
                        <div class="bg-gray-50 p-6 rounded-lg border">
                            <h3 class="font-semibold text-gray-700 mb-3">Frame Foto</h3>
                            <div class="space-y-3">
                                @foreach($frameItems as $item)
                                <label class="flex items-center p-2 border rounded hover:bg-gray-50">
                                    <input type="checkbox" name="selected_extra_items[]" value="{{ $item->id }}" 
                                           class="h-4 w-4 text-red-600 rounded focus:ring-red-500 extra-checkbox"
                                           data-name="{{ $item->name }}"
                                           data-price="{{ $item->price }}"
                                           @if(isset($selectedExtraItems) && in_array($item->id, $selectedExtraItems)) checked @endif>
                                    <span class="ml-2 text-gray-700">{{ $item->name }} (IDR {{ number_format($item->price) }})</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Tambahan & Layanan -->
                        <div class="bg-gray-50 p-6 rounded-lg border">
                            <h3 class="font-semibold text-gray-700 mb-3">Tambahan & Layanan</h3>
                            <div class="space-y-3">
                                @foreach($serviceItems as $item)
                                <label class="flex items-center p-2 border rounded hover:bg-gray-50">
                                    <input type="checkbox" name="selected_extra_items[]" value="{{ $item->id }}" 
                                           class="h-4 w-4 text-red-600 rounded focus:ring-red-500 extra-checkbox"
                                           data-name="{{ $item->name }}"
                                           data-price="{{ $item->price }}"
                                           @if(isset($selectedExtraItems) && in_array($item->id, $selectedExtraItems)) checked @endif>
                                    <span class="ml-2 text-gray-700">{{ $item->name }} (IDR {{ number_format($item->price) }})</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <!-- Informasi Bayi (Opsional) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 p-6 rounded-lg border">
                            <label class="block font-semibold text-gray-700 mb-2">Nama Bayi (Opsional)</label>
                            <input type="text" name="baby_name" value="{{ old('baby_name', $booking->baby_name) }}" class="w-full border border-gray-300 rounded-lg p-3">
                        </div>
                        
                        <div class="bg-gray-50 p-6 rounded-lg border">
                            <label class="block font-semibold text-gray-700 mb-2">Usia Bayi (Opsional)</label>
                            <input type="text" name="baby_age" value="{{ old('baby_age', $booking->baby_age) }}" class="w-full border border-gray-300 rounded-lg p-3">
                        </div>
                    </div>
                    
                    <!-- Catatan Tambahan -->
                    <div class="bg-gray-50 p-6 rounded-lg border">
                        <label class="block font-semibold text-gray-700 mb-2">Catatan Tambahan</label>
                        <textarea name="notes" rows="3" class="w-full border border-gray-300 rounded-lg p-3">{{ old('notes', $booking->notes) }}</textarea>
                    </div>
                    
                    <!-- Total Payment -->
                    <div class="bg-gray-50 p-6 rounded-lg border">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-lg">Total Pembayaran:</span>
                            <span id="totalPriceDisplay" class="text-2xl font-bold text-red-600">IDR {{ number_format($booking->total_price) }}</span>
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="submit-section">
                        <button type="submit" class="submit-btn-premium">
                            <span id="btnContent">Perbarui Booking</span>
                            <span id="btnLoading" class="loading-spinner" style="display: none;"></span>
                        </button>
                        <div class="submit-note">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Semua perubahan akan disimpan dan berlaku instan</span>
                        </div>
                    </div>
                @else
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-yellow-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <h2 class="text-xl font-bold text-gray-800 mb-2">Status Booking Tidak Bisa Diedit</h2>
                        <p class="text-gray-600 mb-4">Hanya booking dengan status "Sudah Dibooking" yang bisa di-edit.</p>
                        <p class="text-gray-500 mb-6">Status saat ini: 
                            <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold border {{ $booking->status === 'booked' ? 'bg-green-100 text-green-800 border-green-200' : ($booking->status === 'completed' ? 'bg-blue-100 text-blue-800 border-blue-200' : 'bg-red-100 text-red-800 border-red-200') }}">
                                {{ $booking->status === 'booked' ? 'Sudah Dibooking' : ($booking->status === 'completed' ? 'Selesai' : 'Dibatalkan') }}
                            </span>
                        </p>
                        <a href="{{ route('bookings.show', $booking->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            Kembali ke Detail Booking
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi variabel global
    let selectedPackage = null;
    let selectedBackgrounds = []; // array of background IDs
    let selectedExtras = []; // array of objects {id, name, price}
    let basePrice = 0;
    let maxBackgrounds = 0;
    let packageCategory = '';
    
    // Ambil data dari old input jika ada
    const oldSelectedBackgrounds = @json($selectedBackgrounds ?? []);
    const oldSelectedExtraItems = @json($selectedExtraItems ?? []);
    const oldPackage = @json(old('package_name', $booking->package_name) ?? null);
    
    // Inisialisasi data lama
    if (oldPackage) {
        const packageCard = document.querySelector(`.package-card[data-package="${oldPackage}"]`);
        if (packageCard) {
            packageCard.click();
        }
    }
    
    // Package selection
    const packageCards = document.querySelectorAll('.package-card');
    packageCards.forEach(card => {
        card.addEventListener('click', function() {
            packageCards.forEach(c => c.classList.remove('selected', 'border-red-500', 'bg-red-50'));
            this.classList.add('selected', 'border-red-500', 'bg-red-50');
            
            selectedPackage = {
                name: this.dataset.package,
                price: parseInt(this.dataset.price),
                maxBackgrounds: parseInt(this.dataset.backgrounds),
                category: this.dataset.category
            };
            
            document.getElementById('package_name').value = selectedPackage.name;
            basePrice = selectedPackage.price;
            maxBackgrounds = selectedPackage.maxBackgrounds;
            packageCategory = selectedPackage.category;
            
            document.getElementById('maxBackgroundsLabel').textContent = maxBackgrounds;
            
            // Tampilkan atau sembunyikan background section
            const backgroundSection = document.getElementById('background-section');
            if (maxBackgrounds === 0) {
                backgroundSection.classList.add('hidden');
            } else {
                backgroundSection.classList.remove('hidden');
                // Tampilkan background sesuai kategori
                displayBackgroundsByCategory(packageCategory);
            }
            
            updateTotal();
        });
    });
    
    // Fungsi untuk menampilkan background berdasarkan kategori
    function displayBackgroundsByCategory(category) {
        const backgroundContainer = document.getElementById('background-container');
        backgroundContainer.innerHTML = '';
        
        // Ambil background berdasarkan kategori
        let backgrounds = [];
        switch(category) {
            case 'baby-smash':
                backgrounds = @json($babySmashBackgrounds);
                break;
            case 'plain':
                backgrounds = @json($plainBackgrounds);
                break;
            case 'grande':
                backgrounds = @json($grandeBackgrounds);
                break;
            case 'royal':
                backgrounds = @json($royalBackgrounds);
                break;
            case 'pre-wedding':
                backgrounds = @json($prewedBackgrounds);
                break;
            case 'family':
                backgrounds = @json($familyBackgrounds);
                break;
            case 'graduation':
                backgrounds = @json($graduationBackgrounds);
                break;
            default:
                backgrounds = @json($backgroundItems);
        }
        
        // Tampilkan background
        backgrounds.forEach(bg => {
            const isSelected = selectedBackgrounds.includes(bg.id);
            const bgOption = document.createElement('div');
            bgOption.className = `background-option cursor-pointer border-2 rounded-lg overflow-hidden transition hover:border-red-500 ${isSelected ? 'border-red-500 bg-red-50' : 'border-gray-200'}`;
            bgOption.dataset.id = bg.id;
            bgOption.dataset.name = bg.name;
            
            bgOption.innerHTML = `
                <img src="{{ asset('storage/') }}/${bg.image}" alt="${bg.name}" class="w-full h-32 object-cover">
                <div class="p-2 bg-gray-50">
                    <p class="text-sm font-medium text-gray-700">${bg.name}</p>
                </div>
            `;
            
            bgOption.addEventListener('click', function() {
                const bgId = this.dataset.id;
                const bgIndex = selectedBackgrounds.indexOf(bgId);
                
                if (bgIndex > -1) {
                    // Remove if already selected
                    selectedBackgrounds.splice(bgIndex, 1);
                    this.classList.remove('border-red-500', 'bg-red-50', 'pulse');
                } else if (selectedBackgrounds.length < maxBackgrounds) {
                    // Add if within limit
                    selectedBackgrounds.push(bgId);
                    this.classList.add('border-red-500', 'bg-red-50');
                    this.classList.add('pulse');
                    
                    // Hapus efek pulse setelah 1.5 detik
                    setTimeout(() => {
                        this.classList.remove('pulse');
                    }, 1500);
                } else {
                    alert(`Paket ${selectedPackage.name} hanya membolehkan maksimal ${maxBackgrounds} background.`);
                    return;
                }
                
                document.getElementById('backgroundCounter').textContent = selectedBackgrounds.length;
                updateTotal();
            });
            
            backgroundContainer.appendChild(bgOption);
        });
    }
    
    // Extra items selection
    const extraItemsCheckboxes = document.querySelectorAll('.extra-checkbox');
    extraItemsCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const itemId = this.value;
            const itemName = this.dataset.name;
            const itemPrice = parseInt(this.dataset.price);
            
            if (this.checked) {
                // Tambahkan ke selectedExtras jika belum ada
                if (!selectedExtras.some(item => item.id === itemId)) {
                    selectedExtras.push({
                        id: itemId,
                        name: itemName,
                        price: itemPrice
                    });
                }
            } else {
                // Hapus dari selectedExtras
                selectedExtras = selectedExtras.filter(item => item.id !== itemId);
            }
            
            updateTotal();
        });
    });
    
    // Payment method change
    const paymentMethodSelect = document.getElementById('payment_method');
    const uploadProofSection = document.getElementById('uploadProofSection');
    
    // Initial check for payment method
    if (paymentMethodSelect.value === 'transfer') {
        uploadProofSection.classList.remove('hidden');
    } else {
        uploadProofSection.classList.add('hidden');
    }
    
    paymentMethodSelect.addEventListener('change', function() {
        if (this.value === 'transfer') {
            uploadProofSection.classList.remove('hidden');
        } else {
            uploadProofSection.classList.add('hidden');
        }
    });
    
    // Fetch available times function
    window.fetchAvailableTimes = async function() {
        const dateInput = document.getElementById('booking_date');
        const timeSelect = document.getElementById('booking_time');
        const selectedDate = dateInput.value;
        
        if (!selectedDate) return;
        
        timeSelect.disabled = true;
        timeSelect.innerHTML = '<option value="">Memuat...</option>';
        const timeAvailabilityInfo = document.getElementById('time-availability-info');
        const availabilityMessage = document.getElementById('availability-message');
        timeAvailabilityInfo.classList.add('hidden');
        
        try {
            const response = await fetch(`/api/available-times?booking_date=${encodeURIComponent(selectedDate)}`);
            if (!response.ok) throw new Error('Gagal memuat data waktu');
            
            const data = await response.json();
            timeSelect.innerHTML = '';
            
            if (data.status === 'full') {
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'Hari ini full booked';
                option.disabled = true;
                timeSelect.appendChild(option);
                timeSelect.disabled = true;
                
                availabilityMessage.textContent = 'Maaf, tidak ada slot tersedia di tanggal ini. Silakan pilih tanggal lain.';
                timeAvailabilityInfo.className = 'availability-info full';
                timeAvailabilityInfo.classList.remove('hidden');
            } else {
                data.available_times.forEach(time => {
                    const option = document.createElement('option');
                    option.value = time;
                    option.textContent = `${time} WIB`;
                    timeSelect.appendChild(option);
                });
                
                timeSelect.disabled = false;
                
                if (data.status === 'limited') {
                    availabilityMessage.textContent = `Hanya tersisa ${data.available_times.length} slot. Segera booking!`;
                    timeAvailabilityInfo.className = 'availability-info limited';
                } else {
                    availabilityMessage.textContent = `Ada ${data.available_times.length} slot yang tersedia.`;
                    timeAvailabilityInfo.className = 'availability-info';
                }
                timeAvailabilityInfo.classList.remove('hidden');
            }
        } catch (err) {
            console.error(err);
            timeSelect.innerHTML = '<option value="">Gagal muat</option>';
            timeSelect.disabled = true;
        }
    };
    
    // Update total payment
    function updateTotal() {
        let extrasTotal = 0;
        
        // Hitung total harga extra items
        selectedExtras.forEach(item => {
            extrasTotal += item.price;
        });
        
        const totalPrice = basePrice + extrasTotal;
        const totalPriceDisplay = document.getElementById('totalPriceDisplay');
        totalPriceDisplay.classList.add('updating');
        
        setTimeout(() => {
            totalPriceDisplay.textContent = `IDR ${new Intl.NumberFormat('id-ID').format(totalPrice)}`;
            totalPriceDisplay.classList.remove('updating');
        }, 300);
    }
    
    // Date change - fetch available times
    document.getElementById('booking_date').addEventListener('change', fetchAvailableTimes);
    
    // Run initial fetch if date is already filled
    if (document.getElementById('booking_date').value) {
        fetchAvailableTimes();
    }
    
    // Initialize background selection if already selected
    if (oldSelectedBackgrounds.length > 0) {
        selectedBackgrounds = oldSelectedBackgrounds;
        document.getElementById('backgroundCounter').textContent = selectedBackgrounds.length;
        
        // Jika ada background yang dipilih, pastikan background section ditampilkan
        if (selectedPackage && selectedPackage.maxBackgrounds > 0) {
            document.getElementById('background-section').classList.remove('hidden');
            displayBackgroundsByCategory(packageCategory);
        }
        
        // Update tampilan background
        setTimeout(() => {
            selectedBackgrounds.forEach(id => {
                const bgOption = document.querySelector(`.background-option[data-id="${id}"]`);
                if (bgOption) {
                    bgOption.classList.add('border-red-500', 'bg-red-50');
                }
            });
        }, 100);
    }
    
    // Initialize extra items selection if already selected
    if (oldSelectedExtraItems.length > 0) {
        extraItemsCheckboxes.forEach(checkbox => {
            const itemId = checkbox.value;
            if (oldSelectedExtraItems.includes(parseInt(itemId))) {
                checkbox.checked = true;
                
                // Tambahkan ke selectedExtras
                const itemName = checkbox.dataset.name;
                const itemPrice = parseInt(checkbox.dataset.price);
                selectedExtras.push({
                    id: itemId,
                    name: itemName,
                    price: itemPrice
                });
            }
        });
        updateTotal();
    }
    
    // Initialize total price
    updateTotal();
    
    // Initial check for Baby Smash Cake
    if (oldPackage && (oldPackage === 'Baby Smash Cake' || oldPackage === 'babysmash')) {
        document.getElementById('background-section').classList.add('hidden');
    }
    
    // Form submission handling
    const adminBookingForm = document.getElementById('adminBookingForm');
    const submitBtn = document.querySelector('.submit-btn-premium');
    const btnContent = document.getElementById('btnContent');
    const btnLoading = document.getElementById('btnLoading');
    
    if (adminBookingForm) {
        adminBookingForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validasi paket
            if (!selectedPackage) {
                alert('Silakan pilih paket terlebih dahulu.');
                return;
            }
            
            // Validasi background untuk paket yang membutuhkan
            if (selectedPackage.maxBackgrounds > 0 && selectedBackgrounds.length === 0) {
                alert('Silakan pilih minimal 1 background.');
                return;
            }
            
            // Validasi bukti pembayaran untuk transfer
            const paymentMethod = document.getElementById('payment_method').value;
            if (paymentMethod === 'transfer') {
                const paymentProof = document.querySelector('input[name="payment_proof"]');
                if (!paymentProof.files || paymentProof.files.length === 0) {
                    // Jika sudah ada bukti lama, tidak perlu upload ulang
                    if (!@json($booking->payment_proof)) {
                        alert('Silakan upload bukti transfer untuk metode pembayaran transfer.');
                        return;
                    }
                }
            }
            
            // Set loading state
            submitBtn.classList.add('loading');
            btnContent.style.display = 'none';
            btnLoading.style.display = 'inline-block';
            
            // Submit form
            this.submit();
        });
    }
});
</script>
@endpush