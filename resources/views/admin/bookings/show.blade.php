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
                Kembali
            </a>
        </div>

        <!-- Alert: Sukses -->
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Alert: Error Validasi -->
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li><strong>{{ $error }}</strong></li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Kolom Kiri: Informasi Utama -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Informasi Pesanan -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-5 border-b pb-2">Informasi Pesanan</h2>
                    <div class="space-y-3 text-sm text-gray-700">
                        <p><strong>Nama Kontak:</strong> {{ $booking->contact_name }}</p>
                        <p><strong>WhatsApp:</strong> {{ $booking->whatsapp_number }}</p>
                        <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($booking->booking_date)->translatedFormat('d F Y') }}</p>
                        <p><strong>Jam:</strong> {{ $booking->booking_time }}</p>
                        <p><strong>Sesi:</strong>
                            @if($booking->session_name === 'baby-smash-cake')
                                <span class="font-medium text-blue-600">Baby Smash Cake</span>
                            @elseif($booking->session_name === 'pre-wedding')
                                <span class="font-medium text-pink-600">Pre-Wedding</span>
                            @elseif($booking->session_name === 'group-photography')
                                <span class="font-medium text-green-600">Group Photography</span>
                            @else
                                {{ $booking->session_name }}
                            @endif
                        </p>
                        <!-- Baby Smash Cake -->
                        @if($booking->session_name === 'baby-smash-cake')
                            <p><strong>Nama Bayi:</strong> {{ $booking->baby_name ?? '-' }}</p>
                            <p><strong>Usia Bayi:</strong>
                                @php
                                    $ageLabels = [
                                        '1-year' => '1 Tahun',
                                        '2-years' => '2 Tahun',
                                        '3-years' => '3 Tahun',
                                        '4-years' => '4 Tahun',
                                        '5-years' => '5 Tahun',
                                        '6-years' => '6 Tahun',
                                    ];
                                    echo $ageLabels[$booking->baby_age] ?? $booking->baby_age ?? '-';
                                @endphp
                            </p>
                        @endif
                        <!-- Group Photography -->
                        @if($booking->session_name === 'group-photography')
                            <p><strong>Jumlah Orang:</strong> {{ $booking->group_size ?? '-' }}</p>
                        @endif
                        <p><strong>Paket:</strong> {{ $booking->package_name }}</p>
                        <p class="font-semibold text-gray-800"><strong>Total:</strong> Rp{{ number_format($booking->total_price, 0, ',', '.') }}</p>
                        <p><strong>Catatan:</strong> {{ $booking->notes ?? '—' }}</p>
                    </div>
                </div>

                <!-- Background & Extra Items -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-5 border-b pb-2">Tambahan & Background</h2>
                    @if($booking->selected_backgrounds)
                        <p class="font-semibold text-gray-700 mb-2">Background:</p>
                        <ul class="list-disc list-inside mb-4 text-sm text-gray-700">
                            @foreach($booking->selected_backgrounds as $bg)
                                <li>{{ $bg['name'] }}</li>
                            @endforeach
                        </ul>
                    @endif
                    @if($booking->selected_extra_items)
                        <p class="font-semibold text-gray-700 mb-2">Extra Item:</p>
                        <ul class="list-disc list-inside text-sm text-gray-700">
                            @foreach($booking->selected_extra_items as $extra)
                                <li>{{ $extra['name'] }} (Rp{{ number_format($extra['price'], 0, ',', '.') }})</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500 text-sm italic">Tidak ada extra item.</p>
                    @endif
                </div>
            </div>

            <!-- Kolom Kanan: Aksi Admin -->
            <div class="space-y-6">
                <!-- Info DP yang Diharapkan -->
                @if($booking->status === 'waiting')
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <p class="text-xs text-blue-800 font-medium">Nominal DP yang harus dibayar:</p>
                        <p class="text-lg font-bold text-blue-700 mt-1">
                            Rp{{ number_format($expectedDpAmount, 0, ',', '.') }} (50%)
                        </p>
                    </div>
                @endif

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

                <!-- Status -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Status Pesanan</h2>
                    @php
                        $badgeColor = match ($booking->status) {
                            'waiting' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                            'booked' => 'bg-green-100 text-green-800 border-green-200',
                            'completed' => 'bg-blue-100 text-blue-800 border-blue-200',
                            default => 'bg-gray-100 text-gray-800 border-gray-200'
                        };
                    @endphp
                    <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold border {{ $badgeColor }}">
                        {{ ucfirst($booking->status === 'waiting' ? 'Menunggu DP' : ($booking->status === 'booked' ? 'Sudah Dibooking' : 'Selesai')) }}
                    </span>
                </div>

                <!-- Kirim Konfirmasi WhatsApp (Waiting) -->
                @if($booking->status === 'waiting')
                    @php
                        $dpAmount = number_format($expectedDpAmount, 0, ',', '.');
                        $message = "Halo kak {$booking->contact_name},
"
                            . "Terima kasih, pesanan untuk sesi {$booking->session_name} pada tanggal "
                            . \Carbon\Carbon::parse($booking->booking_date)->translatedFormat('d F Y') . " pukul {$booking->booking_time} telah kami terima.
"
                            . "Mohon transfer DP Rp{$dpAmount} untuk mengamankan jadwal.
"
                            . "Balas dengan bukti transfer ya. Terima kasih!";
                        $whatsappNumber = preg_replace('/[^0-9]/', '', $booking->whatsapp_number);
                        $url = 'https://wa.me/' . $whatsappNumber . '?text=' . urlencode($message);
                    @endphp
                    <a href="{{ $url }}" target="_blank"
                       onclick="setRefreshOnWa()"
                       class="block text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-xl transition shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004c-1.03 0-2.01-.199-2.907-.536-1.075-.408-1.995-1.093-2.712-1.979-1.555-1.924-2.291-4.5-1.974-7.052.297-2.398 1.538-4.468 3.43-5.755 1.885-1.28 4.217-1.665 6.396-.987 2.178.678 3.844 2.19 4.76 4.115.92 1.923 1.04 4.11.36 6.146-.68 2.036-2.104 3.719-3.989 4.668-.602.3-1.316.499-2.133.499m7.475-7.45c-.035-1.24-.362-2.416-.955-3.459-.624-1.093-1.489-1.968-2.537-2.569-1.047-.6-2.232-.904-3.434-.876-1.203.028-2.382.385-3.377 1.033-1.002.654-1.786 1.57-2.255 2.649-.47 1.078-.607 2.27-.402 3.436.204 1.167.73 2.245 1.505 3.143.776.898 1.776 1.579 2.907 1.986.66.226 1.35.338 2.044.331.693-.007 1.382-.131 2.03-.371.647-.24 1.23-.59 1.722-1.033.49-.443.879-.969 1.147-1.55.267-.581.407-1.205.41-1.842.003-.637-.13-1.265-.393-1.822"/>
                        </svg>
                        Kirim Konfirmasi via WhatsApp
                    </a>
                @endif

                <!-- Form Konfirmasi DP -->
                @if($booking->status === 'waiting')
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">Konfirmasi DP</h2>
                        <form action="{{ route('bookings.confirmDp', $booking->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nominal DP (Rp)</label>
                                    @error('dp_amount')
                                        <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                                    @enderror
                                    <input type="number" name="dp_amount" required
                                           class="w-full border @error('dp_amount') border-red-500 @else border-gray-300 @enderror rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500"
                                           value="{{ old('dp_amount') }}"
                                           placeholder="Masukkan nominal DP">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Bukti Transfer DP</label>
                                    @error('dp_proof')
                                        <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                                    @enderror
                                    <input type="file" name="dp_proof" accept="image/*" required
                                           class="w-full border @error('dp_proof') border-red-500 @else border-gray-300 @enderror rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                </div>
                                <button type="submit"
                                        class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition shadow-sm">
                                    Konfirmasi DP & Kirim WA
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                <!-- Info DP (Saat Booked) -->
                @if($booking->status === 'booked')
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">DP Diterima</h2>
                        <p class="text-sm text-gray-700"><strong>Nominal:</strong> Rp{{ number_format($booking->dp_amount, 0, ',', '.') }}</p>
                        <p class="mt-3 font-medium text-gray-700">Bukti Transfer:</p>
                        <img src="{{ asset('storage/' . $booking->dp_proof) }}" alt="Bukti DP"
                             class="rounded-lg border w-full mt-2 shadow-sm">
                    </div>
                @endif

                <!-- Form Pelunasan -->
                @if($booking->status === 'booked')
                    @php
                        $remainingAmount = $booking->total_price - $booking->dp_amount;
                    @endphp
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">Pelunasan</h2>
                        <div class="text-xs text-gray-600 mb-3">
                            Sisa tagihan: <strong>Rp{{ number_format($remainingAmount, 0, ',', '.') }}</strong>
                        </div>
                        <form action="{{ route('bookings.completeBooking', $booking->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nominal Pelunasan (Rp)</label>
                                    @error('final_payment_amount')
                                        <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                                    @enderror
                                    <input type="number" name="final_payment_amount" required
                                           class="w-full border @error('final_payment_amount') border-red-500 @else border-gray-300 @enderror rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500"
                                           value="{{ old('final_payment_amount') }}"
                                           placeholder="Masukkan nominal pelunasan">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Bukti Pelunasan</label>
                                    @error('final_payment_proof')
                                        <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                                    @enderror
                                    <input type="file" name="final_payment_proof" accept="image/*" required
                                           class="w-full border @error('final_payment_proof') border-red-500 @else border-gray-300 @enderror rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                </div>
                                <button type="submit"
                                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition shadow-sm">
                                    Selesaikan Pesanan & Kirim WA
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                <!-- Laporan Pembayaran (Completed) -->
                @if($booking->status === 'completed')
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">Laporan Pembayaran</h2>
                        <div class="space-y-3 text-sm text-gray-700">
                            <p><strong>DP:</strong> Rp{{ number_format($booking->dp_amount, 0, ',', '.') }}</p>
                            <p><strong>Pelunasan:</strong> Rp{{ number_format($booking->final_payment_amount, 0, ',', '.') }}</p>
                            <p class="font-semibold text-gray-800 mt-2">
                                Total: Rp{{ number_format($booking->dp_amount + $booking->final_payment_amount, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="mt-4 space-y-4">
                            <div>
                                <p class="font-medium text-gray-700">Bukti DP</p>
                                <img src="{{ asset('storage/' . $booking->dp_proof) }}" alt="Bukti DP"
                                     class="rounded-lg border w-full mt-1 shadow-sm">
                            </div>
                            <div>
                                <p class="font-medium text-gray-700">Bukti Pelunasan</p>
                                <img src="{{ asset('storage/' . $booking->final_payment_proof) }}" alt="Bukti Pelunasan"
                                     class="rounded-lg border w-full mt-1 shadow-sm">
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Hapus Pesanan -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <form method="POST" action="{{ route('bookings.destroy', $booking->id) }}"
                          onsubmit="return confirm('Yakin ingin menghapus pesanan ini? Data tidak dapat dipulihkan.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded-lg transition">
                            Hapus Pesanan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Auto Refresh Setelah Kembali dari WhatsApp -->
    <script>
        function setRefreshOnWa() {
            sessionStorage.setItem('refreshAfterWa', 'true');
        }
        document.addEventListener('visibilitychange', function () {
            if (document.visibilityState === 'visible') {
                const refreshOnReturn = sessionStorage.getItem('refreshAfterWa');
                if (refreshOnReturn === 'true') {
                    sessionStorage.removeItem('refreshAfterWa');
                    window.location.reload();
                }
            }
        });
    </script>
</x-layouts.app>