<x-layouts.app :title="'Detail Pesanan #' . $booking->id . ' - Peace Picture Studio'">
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            <!-- Back Button -->
            <div class="mb-8">
                <a href="{{ route('customer.bookings.index') }}" 
                   class="inline-flex items-center gap-2 text-slate-300 hover:text-white transition-all duration-200 group">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Riwayat Pemesanan</span>
                </a>
            </div>

            <!-- Page Header -->
            <div class="text-center mb-10">
                <h1 class="text-3xl sm:text-4xl font-bold text-white mb-3 tracking-tight">
                    Detail Pesanan #{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}
                </h1>
                <p class="text-slate-400 text-lg max-w-2xl mx-auto">
                    Informasi lengkap tentang pesanan Anda
                </p>
            </div>

            <!-- Success Message -->
            @if(session('successMessage'))
            <div class="mb-6 bg-green-900/50 border border-green-500 rounded-xl p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3 flex-1 md:flex md:justify-between">
                        <p class="text-sm text-green-200">
                            {{ session('successMessage') }}
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Error Message -->
            @if(session('errorMessage'))
            <div class="mb-6 bg-red-900/50 border border-red-500 rounded-xl p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3 flex-1 md:flex md:justify-between">
                        <p class="text-sm text-red-200">
                            {{ session('errorMessage') }}
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Booking Status -->
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl overflow-hidden mb-8">
                <div class="p-6 sm:p-8">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                        <div>
                            <h2 class="text-xl font-semibold text-white mb-2">Status Pesanan</h2>
                            <div class="inline-flex px-3 py-1 rounded-full text-sm font-semibold border {{ $booking->statusBadgeColor }}">
                                {{ $booking->statusLabel }}
                            </div>
                            
                            @if($booking->status === 'waiting_payment')
                                <div class="mt-3 p-3 bg-amber-500/20 border border-amber-500/30 rounded-xl">
                                    <p class="text-sm text-amber-200">
                                        <strong>Peringatan:</strong> Anda memiliki waktu <strong>{{ $booking->getRemainingPaymentTime() }} detik</strong> untuk menyelesaikan pembayaran.
                                    </p>
                                </div>
                            @elseif($booking->status === 'cancelled' && $booking->auto_cancelled_at)
                                <div class="mt-3 p-3 bg-red-500/20 border border-red-500/30 rounded-xl">
                                    <p class="text-sm text-red-200">
                                        <strong>Dibatalkan Otomatis:</strong> {{ $booking->auto_cancelled_at->format('d M Y H:i') }}
                                    </p>
                                    <p class="text-sm text-red-200 mt-1">
                                        {{ $booking->cancellation_reason }}
                                    </p>
                                </div>
                            @endif
                        </div>
                        
                        <div class="mt-4 sm:mt-0">
                            @if($booking->status === 'waiting_payment')
                                <a href="{{ route('booking.payment', $booking) }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                    Lanjutkan Pembayaran
                                </a>
                            @elseif($booking->status === 'pending_verification' && $booking->payment_proof)
                                <div class="text-sm text-slate-300">
                                    Menunggu verifikasi pembayaran
                                </div>
                            @elseif($booking->status === 'booked')
                                <div class="text-sm text-slate-300">
                                    Pesanan telah dikonfirmasi
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Details -->
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl overflow-hidden mb-8">
                <div class="p-6 sm:p-8">
                    <h2 class="text-xl font-semibold text-white mb-6">Detail Pesanan</h2>
                    
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-slate-400 text-sm">Sesi</p>
                                <p class="font-medium text-white">{{ $booking->formatted_session_name }}</p>
                            </div>
                            <div>
                                <p class="text-slate-400 text-sm">Paket</p>
                                <p class="font-medium text-white">{{ $booking->package_name }}</p>
                            </div>
                            <div>
                                <p class="text-slate-400 text-sm">Tanggal</p>
                                <p class="font-medium text-white">{{ $booking->booking_date->translatedFormat('d F Y') }}</p>
                            </div>
                            <div>
                                <p class="text-slate-400 text-sm">Jam</p>
                                <p class="font-medium text-white">{{ $booking->booking_time }} WIB</p>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-slate-400 text-sm">Catatan</p>
                                <p class="font-medium text-white">{{ $booking->notes ?: '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Selected Items -->
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl overflow-hidden mb-8">
                <div class="p-6 sm:p-8">
                    <h2 class="text-xl font-semibold text-white mb-6">Item yang Dipilih</h2>
                    
                    <!-- Backgrounds -->
                    @if(!empty($booking->selected_backgrounds))
                    <div class="mb-6">
                        <h3 class="font-semibold text-slate-300 mb-2">Background</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach($booking->selected_backgrounds as $background)
                            <div class="flex items-center p-2 bg-slate-800/50 rounded-lg">
                                <span class="text-slate-200">{{ $background['name'] }}</span>
                                <span class="ml-auto text-slate-400">Rp{{ number_format($background['price'], 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    <!-- Extra Items -->
                    @if(!empty($booking->selected_extra_items))
                    <div>
                        <h3 class="font-semibold text-slate-300 mb-2">Item Tambahan</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach($booking->selected_extra_items as $item)
                            <div class="flex items-center p-2 bg-slate-800/50 rounded-lg">
                                <span class="text-slate-200">{{ $item['name'] }}</span>
                                <span class="ml-auto text-slate-400">Rp{{ number_format($item['price'], 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Customer Info -->
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl overflow-hidden mb-8">
                <div class="p-6 sm:p-8">
                    <h2 class="text-xl font-semibold text-white mb-6">Informasi Pelanggan</h2>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-slate-400 text-sm">Nama</p>
                                <p class="font-medium text-white">{{ $booking->contact_name }}</p>
                            </div>
                            <div>
                                <p class="text-slate-400 text-sm">Nomor WhatsApp</p>
                                <p class="font-medium text-white">{{ $booking->whatsapp_number }}</p>
                            </div>
                            
                            @if($booking->baby_name)
                            <div>
                                <p class="text-slate-400 text-sm">Nama Bayi</p>
                                <p class="font-medium text-white">{{ $booking->baby_name }}</p>
                            </div>
                            @endif
                            
                            @if($booking->baby_age)
                            <div>
                                <p class="text-slate-400 text-sm">Usia Bayi</p>
                                <p class="font-medium text-white">{{ $booking->baby_age }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl overflow-hidden">
                <div class="p-6 sm:p-8">
                    <h2 class="text-xl font-semibold text-white mb-6">Detail Pembayaran</h2>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between py-2 border-b border-slate-700/50">
                            <span class="text-slate-400">Total Harga</span>
                            <span class="font-bold text-lg text-white">Rp{{ number_format($booking->total_price, 0, ',', '.') }}</span>
                        </div>
                        
                        @if($booking->status === 'pending_verification' && $booking->payment_proof)
                        <div>
                            <p class="text-slate-400 text-sm mb-2">Bukti Pembayaran</p>
                            <div class="border border-slate-600 rounded-lg overflow-hidden">
                                <img src="{{ Storage::url($booking->payment_proof) }}" alt="Bukti Pembayaran" class="w-full h-auto">
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>