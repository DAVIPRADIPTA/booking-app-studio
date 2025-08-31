<x-layouts.app :title="'Detail Pesanan #' . $booking->id">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <h1 class="text-2xl font-bold text-gray-800">Detail Pesanan #{{ $booking->id }}</h1>
            <a href="{{ route('bookings.index') }}"
               class="inline-flex items-center gap-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kembali ke Daftar Pesanan</span>
            </a>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- LEFT COLUMN -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Customer Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Informasi Pelanggan</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-500 text-sm">Nama Kontak</p>
                            <p class="font-medium">{{ $booking->contact_name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Nomor WhatsApp</p>
                            <p class="font-medium">{{ $booking->whatsapp_number }}</p>
                        </div>
                        @if($booking->baby_name)
                        <div>
                            <p class="text-gray-500 text-sm">Nama Bayi</p>
                            <p class="font-medium">{{ $booking->baby_name }}</p>
                        </div>
                        @endif
                        @if($booking->baby_age)
                        <div>
                            <p class="text-gray-500 text-sm">Usia Bayi</p>
                            <p class="font-medium">{{ $booking->baby_age }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Booking Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Detail Booking</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-500 text-sm">Sesi</p>
                            <p class="font-medium">{{ $booking->formatted_session_name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Tanggal</p>
                            <p class="font-medium">{{ $booking->booking_date->translatedFormat('d F Y') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Jam</p>
                            <p class="font-medium">{{ $booking->booking_time }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Paket</p>
                            <p class="font-medium">{{ $booking->package_name }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-gray-500 text-sm">Catatan</p>
                            <p class="font-medium">{{ $booking->notes ?: '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Selected Items -->
                @if(!empty($booking->selected_backgrounds) || !empty($booking->selected_extra_items))
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Item yang Dipilih</h2>
                    
                    @if(!empty($booking->selected_backgrounds))
                    <div class="mb-6">
                        <h3 class="font-semibold text-gray-700 mb-2">Background</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach($booking->selected_backgrounds as $bg)
                            <div class="flex items-center p-2 bg-gray-50 rounded-lg">
                                <span>{{ $bg['name'] }}</span>
                                <span class="ml-auto text-gray-500">Rp{{ number_format($bg['price'], 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if(!empty($booking->selected_extra_items))
                    <div>
                        <h3 class="font-semibold text-gray-700 mb-2">Item Tambahan</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach($booking->selected_extra_items as $item)
                            <div class="flex items-center p-2 bg-gray-50 rounded-lg">
                                <span>{{ $item['name'] }}</span>
                                <span class="ml-auto text-gray-500">Rp{{ number_format($item['price'], 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                @endif
            </div>

            <!-- RIGHT COLUMN -->
            <div class="space-y-6">
                <!-- Status Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Status Pesanan</h2>
                    <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold border 
                        {{ $booking->status_color }}">
                        {{ $booking->status_label }}
                    </span>

                    @if($booking->status === 'waiting_payment')
                        <div class="mt-4 p-3 bg-amber-50 rounded-lg border border-amber-100 text-sm text-amber-700">
                            Customer memiliki <strong>{{ $booking->getRemainingTimeFormatted() }}</strong> 
                            untuk menyelesaikan pembayaran.
                        </div>
                    @endif

                    @if($booking->status === 'cancelled' && $booking->cancellation_reason)
                        <div class="mt-4 p-3 bg-red-50 rounded-lg border border-red-100 text-sm text-red-700">
                            <strong>Dibatalkan:</strong> {{ $booking->cancellation_reason }}
                        </div>
                    @endif
                </div>

                <!-- Payment Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Detail Pembayaran</h2>
                    <div class="space-y-3">
                        <div>
                            <p class="text-gray-500 text-sm">Metode Pembayaran</p>
                            <p class="font-medium capitalize">{{ $booking->payment_method }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Total Harga</p>
                            <p class="font-bold text-xl">Rp{{ number_format($booking->total_price, 0, ',', '.') }}</p>
                        </div>

                        @if($booking->payment_proof)
                        <div>
                            <p class="text-gray-500 text-sm mb-2">Bukti Pembayaran</p>
                            <img src="{{ Storage::url($booking->payment_proof) }}" 
                                 class="w-full rounded-lg border" alt="Bukti Pembayaran">
                            <a href="{{ Storage::url($booking->payment_proof) }}" target="_blank"
                               class="mt-2 inline-block text-sm bg-gray-100 px-3 py-1 rounded hover:bg-gray-200">
                                Lihat / Download
                            </a>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Aksi Admin</h2>
                    <div class="space-y-3">
                        @if($booking->status === 'waiting_payment')
                            <form method="POST" action="{{ route('bookings.forceCancel', $booking->id) }}">
                                @csrf
                                <button type="submit"
                                        class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded-lg">
                                    Batalkan Sekarang
                                </button>
                            </form>
                        @elseif($booking->status === 'pending_verification')
                            <form method="POST" action="{{ route('bookings.verifyPayment', $booking->id) }}">
                                @csrf
                                <button type="submit"
                                        class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 rounded-lg">
                                    Verifikasi & Konfirmasi
                                </button>
                            </form>
                        @elseif($booking->status === 'booked')
                            <form method="POST" action="{{ route('bookings.completeBooking', $booking->id) }}">
                                @csrf
                                <button type="submit"
                                        class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 rounded-lg">
                                    Tandai Selesai
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
