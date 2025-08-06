<x-layouts.app :title="'Detail Pesanan #' . $booking->id">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Detail Pesanan #{{ $booking->id }}</h1>
            <a href="{{ route('bookings.index') }}"
               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">Kembali</a>
        </div>

        <!-- Alert Success -->
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- Grid Layout -->
        <div class="grid md:grid-cols-3 gap-6">
            <!-- Kolom Informasi -->
            <div class="md:col-span-2 space-y-6">
                <!-- Informasi Booking -->
                <div class="bg-white rounded shadow p-6">
                    <h2 class="text-lg font-bold mb-4">Informasi Pesanan</h2>
                    <div class="space-y-2 text-sm">
                        <p><strong>Nama:</strong> {{ $booking->contact_name }}</p>
                        <p><strong>WhatsApp:</strong> {{ $booking->whatsapp_number }}</p>
                        <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($booking->booking_date)->translatedFormat('d F Y') }}</p>
                        <p><strong>Jam:</strong> {{ $booking->booking_time }}</p>
                        <p><strong>Sesi:</strong> {{ $booking->session_name }}</p>
                        <p><strong>Paket:</strong> {{ $booking->package_name }}</p>
                        <p><strong>Total:</strong> Rp{{ number_format($booking->total_price, 0, ',', '.') }}</p>
                        <p><strong>Catatan:</strong> {{ $booking->notes ?? '-' }}</p>
                    </div>
                </div>
                <!-- Background & Tambahan -->
                <div class="bg-white rounded shadow p-6">
                    <h2 class="text-lg font-bold mb-4">Tambahan & Background</h2>
                    @if ($booking->selected_backgrounds)
                        <p class="font-semibold">Background:</p>
                        <ul class="list-disc ml-5 mb-2">
                            @foreach ($booking->selected_backgrounds as $bg)
                                <li>{{ $bg['name'] }}</li>
                            @endforeach
                        </ul>
                    @endif
                    @if ($booking->selected_extra_items)
                        <p class="font-semibold">Extra Item:</p>
                        <ul class="list-disc ml-5">
                            @foreach ($booking->selected_extra_items as $extra)
                                <li>{{ $extra['name'] }} (Rp{{ number_format($extra['price'], 0, ',', '.') }})</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500">Tidak ada extra item.</p>
                    @endif
                </div>
            </div>

            <!-- Kolom Admin -->
            <div class="space-y-6">
                <!-- Tombol Refresh Manual -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                    <h2 class="text-lg font-bold text-gray-800 mb-3">Tindakan Cepat</h2>
                    <button 
                        onclick="window.location.reload()" 
                        class="inline-flex items-center gap-2 bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg text-sm transition w-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2A8.001 8.001 0 0020.418 15" />
                        </svg>
                        Refresh Halaman
                    </button>
                    <p class="text-gray-500 text-xs mt-2">Klik untuk memperbarui status terbaru</p>
                </div>

                <!-- STATUS -->
                <div class="bg-white rounded shadow p-6">
                    <h2 class="text-lg font-bold mb-4">Status Pesanan</h2>
                    @php
                        $badge = match ($booking->status) {
                            'waiting' => 'bg-yellow-200 text-yellow-800',
                            'booked' => 'bg-green-200 text-green-800',
                            'completed' => 'bg-blue-200 text-blue-800',
                            default => 'bg-gray-200 text-gray-800'
                        };
                    @endphp
                    <span class="px-3 py-1 rounded text-sm font-semibold {{ $badge }}">
                        {{ ucfirst($booking->status === 'waiting' ? 'Menunggu DP' : ($booking->status === 'booked' ? 'Sudah Dibooking' : 'Selesai')) }}
                    </span>
                </div>

                <!-- Kirim Konfirmasi WA -->
                @if ($booking->status === 'waiting')
                    @php
                        $dpAmount = number_format($booking->total_price / 2, 0, ',', '.');
                        $message = "Halo kak {$booking->contact_name}, pesananmu untuk tanggal {$booking->booking_date->format('d M Y')} jam {$booking->booking_time} telah kami terima. Mohon transfer DP Rp{$dpAmount} untuk mengamankan jadwal. Balas dengan bukti transfer ya. Terima kasih.";
                        $whatsappLink = "https://wa.me/" . preg_replace('/[^0-9]/', '', $booking->whatsapp_number) . "?text=" . urlencode($message);
                    @endphp
                    <div>
                        <a href="{{ $whatsappLink }}" target="_blank"
                            class="block text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded mb-4">
                            Kirim Konfirmasi Pesanan via WhatsApp
                        </a>
                    </div>
                @endif

                <!-- FORM KONFIRMASI DP -->
                @if ($booking->status === 'waiting')
                    <div class="bg-white rounded shadow p-6">
                        <h2 class="text-lg font-bold mb-4">Konfirmasi DP</h2>
                        <form action="{{ route('bookings.confirmDp', $booking->id) }}" method="POST" enctype="multipart/form-data" onsubmit="setTimeout(() => window.location.reload(), 3000)">
                            @csrf
                            <div class="mb-4">
                                <label class="block font-medium mb-1">Nominal DP</label>
                                <input type="number" name="dp_amount" required class="w-full border rounded px-3 py-2">
                            </div>
                            <div class="mb-4">
                                <label class="block font-medium mb-1">Bukti Transfer DP</label>
                                <input type="file" name="dp_proof" accept="image/*" required class="w-full border rounded px-3 py-2">
                            </div>
                            <button class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Konfirmasi DP & Kirim WA
                            </button>
                        </form>
                    </div>
                @endif

                <!-- TAMPILKAN DP (SAAT BOOKED) -->
                @if ($booking->status === 'booked')
                    <div class="bg-white rounded shadow p-6">
                        <h2 class="text-lg font-bold mb-4">DP Telah Diterima</h2>
                        <p><strong>Nominal:</strong> Rp{{ number_format($booking->dp_amount, 0, ',', '.') }}</p>
                        <p class="mt-2 font-semibold">Bukti Transfer:</p>
                        <img src="{{ asset('storage/' . $booking->dp_proof) }}" class="rounded w-full max-w-sm">
                    </div>
                @endif

                <!-- FORM PELUNASAN -->
                @if ($booking->status === 'booked')
                    <div class="bg-white rounded shadow p-6">
                        <h2 class="text-lg font-bold mb-4">Pelunasan</h2>
                        <form action="{{ route('bookings.completeBooking', $booking->id) }}" method="POST" enctype="multipart/form-data" onsubmit="setTimeout(() => window.location.reload(), 3000)">
                            @csrf
                            <div class="mb-4">
                                <label class="block font-medium mb-1">Nominal Pelunasan</label>
                                <input type="number" name="final_payment_amount" required class="w-full border rounded px-3 py-2">
                            </div>
                            <div class="mb-4">
                                <label class="block font-medium mb-1">Bukti Pelunasan</label>
                                <input type="file" name="final_payment_proof" accept="image/*" required class="w-full border rounded px-3 py-2">
                            </div>
                            <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Selesaikan Pesanan & Kirim WA
                            </button>
                        </form>
                    </div>
                @endif

                <!-- LAPORAN FINAL -->
                @if ($booking->status === 'completed')
                    <div class="bg-white rounded shadow p-6">
                        <h2 class="text-lg font-bold mb-4">Laporan Pembayaran</h2>
                        <p><strong>Nominal DP:</strong> Rp{{ number_format($booking->dp_amount, 0, ',', '.') }}</p>
                        <p><strong>Nominal Pelunasan:</strong> Rp{{ number_format($booking->final_payment_amount, 0, ',', '.') }}</p>
                        <div class="mt-4">
                            <p class="font-semibold">Bukti DP:</p>
                            <img src="{{ asset('storage/' . $booking->dp_proof) }}" class="rounded w-full max-w-sm mb-3">
                            <p class="font-semibold">Bukti Pelunasan:</p>
                            <img src="{{ asset('storage/' . $booking->final_payment_proof) }}" class="rounded w-full max-w-sm">
                        </div>
                    </div>
                @endif

                <!-- DELETE -->
                <div class="bg-white rounded shadow p-6">
                    <form method="POST" action="{{ route('bookings.destroy', $booking->id) }}" onsubmit="return confirm('Yakin ingin menghapus pesanan ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">Hapus Pesanan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>