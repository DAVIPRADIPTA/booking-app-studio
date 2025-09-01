@extends('layouts.app')
@section('title', 'Tambah Booking Manual - Admin')

@push('styles')
    <style>
        /* Import Dancing Script Font */
        @import url('https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap');
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
            0% {
                box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.7);
            }
            70% {
                box-shadow: 0 0 0 12px rgba(220, 38, 38, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(220, 38, 38, 0);
            }
        }
        .background-option.pulse {
            animation: backgroundPulse 1.5s;
        }
        /* Form Elements */
        .form-input,
        .form-select {
            width: 100%;
            padding: clamp(0.9rem, 1.8vw, 1.2rem) clamp(1.2rem, 2.5vw, 1.5rem);
            border: 1px solid #d1d5db;
            border-radius: clamp(0.6rem, 1.2vw, 0.9rem);
            background: white;
            font-size: clamp(0.85rem, 1.5vw, 0.95rem);
            color: #1f2937;
            transition: all 0.3s ease;
        }
        .form-input:focus,
        .form-select:focus {
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
        /* Package Notice */
        .package-notice {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0.08) 100%);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #ef4444;
            font-size: 0.9rem;
        }
        .package-notice svg {
            min-width: 1.25rem;
            min-height: 1.25rem;
        }
    </style>
@endpush

@section('content')
    <div class="max-w-6xl mx-auto bg-white shadow-lg rounded-xl p-6">
       <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Tambah Booking Manual</h1>

    <div class="flex items-center gap-2">
        <!-- Link balik ke daftar booking -->
        <a href="{{ route('bookings.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold rounded-lg transition">
            <!-- icon panah -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
        </a>
    </div>
</div>


        <form action="{{ route('bookings.store') }}" method="POST" enctype="multipart/form-data" id="adminBookingForm"
            class="space-y-8">
            @csrf

            <!-- Pilih Customer -->
            <div class="bg-gray-50 p-6 rounded-lg border">
                <label class="block font-semibold text-gray-700 mb-3">Pilih Customer</label>
                <select name="customer_id"
                    class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    required>
                    <option value="">-- Pilih Customer --</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
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
                    <input type="text" name="contact_name" value="{{ old('contact_name') }}"
                        class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        required>
                    @error('contact_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="bg-gray-50 p-6 rounded-lg border">
                    <label class="block font-semibold text-gray-700 mb-2">Nomor WhatsApp</label>
                    <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number') }}"
                        class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="081234567890" required>
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
                    <input type="date" name="booking_date" id="booking_date" min="{{ now()->format('Y-m-d') }}"
                        value="{{ old('booking_date') }}"
                        class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        required>
                    @error('booking_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="bg-gray-50 p-6 rounded-lg border">
                    <label class="block font-semibold text-gray-700 mb-2">Waktu Pemotretan</label>
                    <select id="booking_time" name="booking_time"
                        class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        required @if(empty($availableTimes) && !old('booking_time')) disabled @endif>
                        @if(!empty($availableTimes) && old('booking_date'))
                            <option value="">-- Pilih Waktu --</option>
                            @foreach($availableTimes as $t)
                                <option value="{{ $t }}" {{ old('booking_time') == $t ? 'selected' : '' }}>{{ $t }} WIB</option>
                            @endforeach
                        @else
                            <option value="">{{ old('booking_time') ? old('booking_time') : 'Pilih tanggal dulu...' }}</option>
                        @endif
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
                <select name="payment_method" id="payment_method"
                    class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    required>
                    <option value="cash">Cash</option>
                    <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                </select>

                <!-- Upload Bukti Transfer -->
                <div id="uploadProofSection" class="mt-4 {{ old('payment_method') !== 'transfer' ? 'hidden' : '' }}">
                    <label class="block font-semibold text-gray-700 mb-2">Upload Bukti Transfer</label>
                    <input type="file" name="payment_proof" class="w-full border border-gray-300 rounded-lg p-3"
                        accept="image/*">
                    <p class="text-gray-500 text-sm mt-1">Hanya jika metode pembayaran = Transfer</p>
                </div>
            </div>

            <!-- Pilih Paket -->
            <div class="package-section-container bg-gray-50 p-6 rounded-lg border">
                <h2 class="text-xl font-semibold text-gray-700 mb-6">Pilih Paket</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Baby Smash Cake -->
                    <div class="package-card cursor-pointer border-2 rounded-lg p-4 hover:border-red-500 transition {{ in_array(old('package_name'), ['Baby Smash Cake', 'babysmash']) ? 'border-red-500 bg-red-50' : 'border-gray-200' }}"
                        data-package="Baby Smash Cake" data-price="550000" data-backgrounds="0" data-category="baby-smash">
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
                    <div class="package-card cursor-pointer border-2 rounded-lg p-4 hover:border-red-500 transition {{ old('package_name') == 'Plain' ? 'border-red-500 bg-red-50' : 'border-gray-200' }}"
                        data-package="Plain" data-price="300000" data-backgrounds="1" data-category="plain">
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
                    <div class="package-card cursor-pointer border-2 rounded-lg p-4 hover:border-red-500 transition {{ old('package_name') == 'Grande' ? 'border-red-500 bg-red-50' : 'border-gray-200' }}"
                        data-package="Grande" data-price="500000" data-backgrounds="2" data-category="grande">
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
                    <div class="package-card cursor-pointer border-2 rounded-lg p-4 hover:border-red-500 transition {{ old('package_name') == 'Royal' ? 'border-red-500 bg-red-50' : 'border-gray-200' }}"
                        data-package="Royal" data-price="700000" data-backgrounds="4" data-category="royal">
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
                    <div class="package-card cursor-pointer border-2 rounded-lg p-4 hover:border-red-500 transition {{ in_array(old('package_name'), ['Prewed I', 'prewed1']) ? 'border-red-500 bg-red-50' : 'border-gray-200' }}"
                        data-package="Prewed I" data-price="700000" data-backgrounds="2" data-category="pre-wedding">
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
                    <div class="package-card cursor-pointer border-2 rounded-lg p-4 hover:border-red-500 transition {{ in_array(old('package_name'), ['Prewed II', 'prewed2']) ? 'border-red-500 bg-red-50' : 'border-gray-200' }}"
                        data-package="Prewed II" data-price="1000000" data-backgrounds="3" data-category="pre-wedding">
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

                <input type="hidden" name="package_name" id="package_name" value="{{ old('package_name') }}">
                <input type="hidden" name="session_name" id="session_name" value="Photoshoot Session">
                @error('package_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Pilih Background -->
            <div id="background-section" class="bg-gray-50 p-6 rounded-lg border {{ in_array(old('package_name'), ['Baby Smash Cake', 'babysmash']) || !old('package_name') ? 'hidden' : '' }}">
                <div class="flex justify-between items-center mb-3">
                    <label class="block font-semibold text-gray-700">Pilih Background (Maks: <span id="maxBackgroundsLabel">0</span>)</label>
                    <span class="background-counter" id="backgroundCounter">{{ is_array($selectedBackgrounds ?? null) ? count($selectedBackgrounds) : 0 }}</span>
                </div>
                <div id="background-container" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <!-- Background akan diisi oleh JavaScript -->
                </div>
                <input type="hidden" name="selected_backgrounds" id="selected_backgrounds"
                    value="{{ old('selected_backgrounds') ? json_encode($selectedBackgrounds) : json_encode($selectedBackgrounds ?? []) }}">
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
                                    data-name="{{ $item->name }}" data-price="{{ $item->price }}"
                                    @if(old('selected_extra_items') && in_array($item->id, old('selected_extra_items'))) checked @endif>
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
                                    data-name="{{ $item->name }}" data-price="{{ $item->price }}"
                                    @if(old('selected_extra_items') && in_array($item->id, old('selected_extra_items'))) checked @endif>
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
                                    data-name="{{ $item->name }}" data-price="{{ $item->price }}"
                                    @if(old('selected_extra_items') && in_array($item->id, old('selected_extra_items'))) checked @endif>
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
                    <input type="text" name="baby_name" value="{{ old('baby_name') }}"
                        class="w-full border border-gray-300 rounded-lg p-3">
                </div>

                <div class="bg-gray-50 p-6 rounded-lg border">
                    <label class="block font-semibold text-gray-700 mb-2">Usia Bayi (Opsional)</label>
                    <input type="text" name="baby_age" value="{{ old('baby_age') }}"
                        class="w-full border border-gray-300 rounded-lg p-3">
                </div>
            </div>

            <!-- Catatan Tambahan -->
            <div class="bg-gray-50 p-6 rounded-lg border">
                <label class="block font-semibold text-gray-700 mb-2">Catatan Tambahan</label>
                <textarea name="notes" rows="3"
                    class="w-full border border-gray-300 rounded-lg p-3">{{ old('notes') }}</textarea>
            </div>

            <!-- Total Payment -->
            <div class="bg-gray-50 p-6 rounded-lg border">
                <div class="flex justify-between items-center">
                    <span class="font-semibold text-lg">Total Pembayaran:</span>
                    <span id="totalPriceDisplay" class="text-2xl font-bold text-red-600">IDR {{ number_format($totalPrice ?? 0) }}</span>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-center">
                <button type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white font-bold px-8 py-3 rounded-lg shadow transition text-lg">
                    Simpan Booking
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let selectedPackage = null;
    let selectedBackgrounds = @json($selectedBackgrounds ?? []);
    let selectedExtras = [];
    let basePrice = 0;
    let maxBackgrounds = 0;
    let packageCategory = '';
    let availableTimes = @json($availableTimes ?? []); // from server (controller)

    // Inisialisasi extra items dari old input
    const oldExtraItems = @json(old('selected_extra_items', []));
    document.querySelectorAll('.extra-checkbox').forEach(checkbox => {
        const itemId = checkbox.value;
        if (oldExtraItems.includes(parseInt(itemId))) {
            selectedExtras.push({
                id: itemId,
                name: checkbox.dataset.name,
                price: parseInt(checkbox.dataset.price)
            });
        }
    });

    // Package Selection
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
            document.getElementById('backgroundCounter').textContent = selectedBackgrounds.length;

            const backgroundSection = document.getElementById('background-section');
            if (maxBackgrounds === 0) {
                backgroundSection.classList.add('hidden');
                selectedBackgrounds = [];
                updateHiddenBackgrounds();
            } else {
                backgroundSection.classList.remove('hidden');
                displayBackgroundsByCategory(packageCategory);
            }

            updateTotal();
        });
    });

    // Display Backgrounds by Category
    function displayBackgroundsByCategory(category) {
        const container = document.getElementById('background-container');
        container.innerHTML = '';
        let backgrounds = [];

        switch(category) {
            case 'baby-smash': backgrounds = @json($babySmashBackgrounds); break;
            case 'plain': backgrounds = @json($plainBackgrounds); break;
            case 'grande': backgrounds = @json($grandeBackgrounds); break;
            case 'royal': backgrounds = @json($royalBackgrounds); break;
            case 'pre-wedding': backgrounds = @json($prewedBackgrounds); break;
            case 'family': backgrounds = @json($familyBackgrounds); break;
            case 'graduation': backgrounds = @json($graduationBackgrounds); break;
            default: backgrounds = @json($backgroundItems);
        }

        backgrounds.forEach(bg => {
            const isSelected = selectedBackgrounds.includes(bg.id);
            const div = document.createElement('div');
            div.className = `background-option cursor-pointer border-2 rounded-lg overflow-hidden transition ${isSelected ? 'border-red-500 bg-red-50' : 'border-gray-200'}`;
            div.dataset.id = bg.id;
            div.innerHTML = `
                <img src="{{ asset('storage') }}/${bg.image}" alt="${bg.name}" class="w-full h-32 object-cover">
                <div class="p-2 bg-gray-50"><p class="text-sm font-medium text-gray-700">${bg.name}</p></div>
            `;
            div.addEventListener('click', function() {
                const id = parseInt(this.dataset.id);
                const index = selectedBackgrounds.indexOf(id);
                if (index > -1) {
                    selectedBackgrounds.splice(index, 1);
                    this.classList.remove('border-red-500', 'bg-red-50');
                } else if (selectedBackgrounds.length < maxBackgrounds) {
                    selectedBackgrounds.push(id);
                    this.classList.add('border-red-500', 'bg-red-50');
                    this.classList.add('pulse');
                    setTimeout(() => this.classList.remove('pulse'), 1500);
                } else {
                    alert(`Paket ${selectedPackage.name} hanya boleh ${maxBackgrounds} background.`);
                    return;
                }
                document.getElementById('backgroundCounter').textContent = selectedBackgrounds.length;
                updateHiddenBackgrounds();
                updateTotal();
            });
            container.appendChild(div);
        });

        // Highlight yang sudah dipilih
        selectedBackgrounds.forEach(id => {
            const el = container.querySelector(`[data-id="${id}"]`);
            if (el) el.classList.add('border-red-500', 'bg-red-50');
        });
    }

    function updateHiddenBackgrounds() {
        document.getElementById('selected_backgrounds').value = JSON.stringify(selectedBackgrounds);
    }

    // Extra Items change
    document.querySelectorAll('.extra-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const id = this.value;
            const name = this.dataset.name;
            const price = parseInt(this.dataset.price);
            if (this.checked) {
                if (!selectedExtras.some(item => item.id === id)) {
                    selectedExtras.push({ id, name, price });
                }
            } else {
                selectedExtras = selectedExtras.filter(item => item.id !== id);
            }
            updateTotal();
        });
    });

    // Payment Method
    const paymentMethod = document.getElementById('payment_method');
    const proofSection = document.getElementById('uploadProofSection');
    paymentMethod.addEventListener('change', () => {
        proofSection.classList.toggle('hidden', paymentMethod.value !== 'transfer');
    });

    // Fetch available times from API (and set availableTimes var)
    window.fetchAvailableTimes = async function() {
        const date = document.getElementById('booking_date').value;
        if (!date) return;

        const select = document.getElementById('booking_time');
        const info = document.getElementById('time-availability-info');
        const msg = document.getElementById('availability-message');

        select.disabled = true;
        select.innerHTML = '<option>Memuat...</option>';
        info.classList.add('hidden');

        try {
            const res = await fetch(`/api/available-times?booking_date=${encodeURIComponent(date)}`);
            if (!res.ok) throw new Error('Fetch error');
            const data = await res.json();

            availableTimes = data.available_times || [];

            if (data.status === 'full') {
                select.innerHTML = '<option value="" disabled>Hari ini full booked</option>';
                select.disabled = true;
                msg.textContent = 'Tidak ada slot tersedia.';
                info.className = 'availability-info full';
            } else {
                select.innerHTML = '<option value="">-- Pilih Waktu --</option>';
                data.available_times.forEach(t => {
                    const opt = document.createElement('option');
                    opt.value = t;
                    opt.textContent = t + ' WIB';
                    select.appendChild(opt);
                });
                select.disabled = false;
                msg.textContent = `Ada ${data.available_times.length} slot tersedia.`;
                info.className = data.status === 'limited' ? 'availability-info limited' : 'availability-info';
            }
            info.classList.remove('hidden');
        } catch (e) {
            console.error('Error fetching available times:', e);
            select.innerHTML = '<option value="" disabled>Gagal muat data</option>';
            select.disabled = true;
            msg.textContent = 'Terjadi kesalahan saat memuat data. Coba lagi nanti.';
            info.className = 'availability-info';
            info.classList.remove('hidden');
        }
    };

    // event listener tanggal
    document.getElementById('booking_date').addEventListener('change', fetchAvailableTimes);

    // Validasi form sebelum submit
    document.getElementById('adminBookingForm').addEventListener('submit', function(e) {
        if (!selectedPackage) {
            e.preventDefault();
            alert('Silakan pilih paket.');
            return;
        }

        if (maxBackgrounds > 0 && selectedBackgrounds.length === 0) {
            e.preventDefault();
            alert('Pilih minimal 1 background.');
            return;
        }

        const bookingTime = document.getElementById('booking_time').value;
        const bookingDate = document.getElementById('booking_date').value;

        if (bookingDate && bookingTime) {
            const isAvailable = availableTimes.includes(bookingTime);
            if (!isAvailable) {
                e.preventDefault();
                alert('Waktu yang dipilih tidak tersedia. Silakan pilih dari daftar waktu yang tersedia.');
                return;
            }
        }

        const method = document.getElementById('payment_method').value;
        if (method === 'transfer') {
            const fileInput = document.querySelector('input[name="payment_proof"]');
            if (!fileInput || !fileInput.files.length) {
                e.preventDefault();
                alert('Upload bukti transfer.');
                return;
            }
        }

        // set hidden backgrounds before submit
        updateHiddenBackgrounds();
    });

    // Update Total
    function updateTotal() {
        const extrasTotal = selectedExtras.reduce((sum, item) => sum + item.price, 0);
        const total = basePrice + extrasTotal;
        document.getElementById('totalPriceDisplay').textContent = `IDR ${new Intl.NumberFormat('id-ID').format(total)}`;
    }

    // Initialize: apply old package if any
    const oldPackage = "{{ old('package_name') }}";
    if (oldPackage) {
        const card = document.querySelector(`.package-card[data-package="${oldPackage}"]`);
        if (card) card.click();
    }

    // If booking_date already filled (reload), fetch initial times
    if (document.getElementById('booking_date').value) {
        // if server already provided availableTimes we still run fetch to ensure freshness
        fetchAvailableTimes();
    }

    updateTotal();
});
</script>
@endpush
