<x-layouts.app :title="'Detail Pesanan #' . $booking->id">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <h1 class="text-2xl font-bold text-gray-800">Detail Pesanan #{{ $booking->id }}</h1>
            <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                <a href="{{ route('bookings.index') }}"
                    class="inline-flex items-center justify-center gap-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg transition w-full sm:w-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Daftar Pesanan</span>
                </a>

                @if($booking->status === 'booked')
                    <a href="{{ route('bookings.edit', $booking->id) }}"
                        class="inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg transition w-full sm:w-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>Edit Pesanan</span>
                    </a>
                @endif
            </div>
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
                            <p class="font-medium">{{ $booking->formatted_session_name ?? $booking->session_name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Tanggal</p>
                            <p class="font-medium">
                                {{ \Carbon\Carbon::parse($booking->booking_date)->translatedFormat('d F Y') }}
                            </p>
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

                        <!-- Backgrounds -->
                        @if(!empty($booking->selected_backgrounds))
                            <div class="mb-6">
                                <h3 class="font-semibold text-gray-700 mb-3">Background yang Dipilih</h3>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                    @foreach($booking->selected_backgrounds as $bg)
                                        <div class="bg-gray-50 rounded-lg border overflow-hidden">
                                            <div class="w-full aspect-square">
                                                @if(!empty($bg['image']))
                                                    <img src="{{ asset('storage/' . $bg['image']) }}"
                                                        alt="{{ $bg['name'] ?? 'Background' }}"
                                                        class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center bg-gray-200 text-gray-500">
                                                        Tidak ada gambar
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="p-2 text-center">
                                                <span class="text-sm font-medium text-gray-700">
                                                    {{ $bg['name'] ?? 'Background' }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Extras -->
                        @if(!empty($booking->selected_extra_items))
                            <div>
                                <h3 class="font-semibold text-gray-700 mb-3">Item Tambahan</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach($booking->selected_extra_items as $item)
                                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg border">
                                            <span class="font-medium">{{ $item['name'] ?? 'Item' }}</span>
                                            <span class="text-gray-600">
                                                Rp{{ number_format($item['price'] ?? 0, 0, ',', '.') }}
                                            </span>
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

                    @php
                        // Use array mapping for PHP 7.4+ compatibility (avoids `match` requirement)
                        $badgeMap = [
                            'waiting_payment' => ['color' => 'bg-yellow-100 text-yellow-800 border-yellow-200', 'label' => 'Menunggu Pembayaran'],
                            'pending_verification' => ['color' => 'bg-orange-100 text-orange-800 border-orange-200', 'label' => 'Menunggu Verifikasi'],
                            'booked' => ['color' => 'bg-green-100 text-green-800 border-green-200', 'label' => 'Sudah Dibooking'],
                            'completed' => ['color' => 'bg-blue-100 text-blue-800 border-blue-200', 'label' => 'Selesai'],
                            'cancelled' => ['color' => 'bg-red-100 text-red-800 border-red-200', 'label' => 'Dibatalkan'],
                        ];

                        $defaultBadge = ['color' => 'bg-gray-100 text-gray-800 border-gray-200', 'label' => ucfirst($booking->status)];

                        $badge = $badgeMap[$booking->status] ?? $defaultBadge;
                        $badgeColor = $badge['color'];
                        $label = $badge['label'];
                    @endphp

                    <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold border {{ $badgeColor }}">
                        {{ $label }}
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

                        {{-- Hanya tampilkan area bukti ketika metode = transfer --}}
                        @if($booking->payment_method === 'transfer')
                            @if($booking->payment_proof)
                                <div>
                                    <p class="text-gray-500 text-sm mb-2">Bukti Pembayaran</p>
                                    <img src="{{ asset('storage/' . $booking->payment_proof) }}" class="w-full rounded-lg border"
                                        alt="Bukti Pembayaran">
                                    <a href="{{ asset('storage/' . $booking->payment_proof) }}" target="_blank"
                                        class="mt-2 inline-block text-sm bg-gray-100 px-3 py-1 rounded hover:bg-gray-200">
                                        Lihat / Download
                                    </a>
                                </div>
                            @else
                                <div class="p-3 bg-yellow-50 rounded-lg border border-yellow-100 text-sm text-yellow-700">
                                    Metode pembayaran: <strong>Transfer</strong>. Namun belum ada bukti transfer yang diunggah.
                                </div>
                            @endif
                        @endif

                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Aksi Admin</h2>
                    <div class="space-y-3">
                        @if($booking->status === 'waiting_payment')
                            <form method="POST" action="{{ route('bookings.forceCancel', $booking->id) }}" class="js-prompt-reason">
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

                            <form method="POST" action="{{ route('bookings.cancelBooking', $booking->id) }}" class="js-prompt-reason">
                                @csrf
                                <button type="submit"
                                    class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded-lg">
                                    Batalkan Pesanan
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

                            <form method="POST" action="{{ route('bookings.cancelBooking', $booking->id) }}" class="js-prompt-reason">
                                @csrf
                                <button type="submit"
                                    class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded-lg">
                                    Batalkan Pesanan
                                </button>
                            </form>
                        @endif

                        {{-- Tombol Hapus hanya muncul bila status completed atau cancelled --}}
                        @if(in_array($booking->status, ['completed', 'cancelled']))
                            <form method="POST" action="{{ route('bookings.destroy', $booking->id) }}" class="js-confirm-delete">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-full bg-red-700 hover:bg-red-800 text-white font-semibold py-2 rounded-lg">
                                    Hapus Pesanan
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- JS: prompt alasan pembatalan (opsional) + confirm delete --}}
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Prompt alasan pembatalan (opsional). Jika user Cancel => batalkan submit.
            document.querySelectorAll('form.js-prompt-reason').forEach(function (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    var message = 'Masukkan alasan pembatalan (opsional). Tekan Cancel untuk membatalkan aksi.';
                    var reason = prompt(message);

                    // Jika user menekan Cancel pada prompt -> batalkan submit
                    if (reason === null) {
                        return;
                    }

                    // Jika diisi non-empty -> tambahkan hidden input
                    if (reason.trim() !== '') {
                        var input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'cancellation_reason';
                        input.value = reason.trim();
                        form.appendChild(input);
                    }

                    // submit form setelah prompt
                    form.submit();
                });
            });

            // Konfirmasi sebelum hapus
            document.querySelectorAll('form.js-confirm-delete').forEach(function (form) {
                form.addEventListener('submit', function (e) {
                    var ok = confirm('Yakin ingin menghapus pesanan ini? Tindakan ini tidak bisa dibatalkan.');
                    if (!ok) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
    @endpush
</x-layouts.app>
