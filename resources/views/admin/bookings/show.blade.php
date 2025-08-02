<x-layouts.app :title="__('Ekstra')">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Detail Pesanan #{{ $booking->id }}</h1>
            <a href="{{ route('bookings.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">Kembali</a>
        </div>

        @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4" role="alert">
            {{ session('success') }}
        </div>
        @endif

        <div class="flex flex-wrap -mx-4">
            <div class="w-full md:w-2/3 px-4">
                <div class="bg-white shadow-lg rounded-lg mb-6">
                    <div class="bg-gray-100 px-6 py-4 rounded-t-lg">
                        <h4 class="text-lg font-semibold text-gray-800">Informasi Pesanan</h4>
                    </div>
                    <div class="p-6">
                        <p class="mb-2"><strong class="font-semibold">Nama Kontak:</strong> {{ $booking->contact_name }}</p>
                        <p class="mb-2"><strong class="font-semibold">Nomor WhatsApp:</strong> {{ $booking->whatsapp_number }}</p>
                        <p class="mb-2"><strong class="font-semibold">Tanggal:</strong> {{ \Carbon\Carbon::parse($booking->booking_date)->format('d F Y') }}</p>
                        <p class="mb-2"><strong class="font-semibold">Waktu:</strong> {{ $booking->booking_time }}</p>
                        <p class="mb-2"><strong class="font-semibold">Jenis Sesi:</strong> {{ $booking->session_name }}</p>
                        <p class="mb-2"><strong class="font-semibold">Paket:</strong> {{ $booking->package_name }}</p>
                        <p class="mb-2"><strong class="font-semibold">Total Harga:</strong> Rp.{{ number_format($booking->total_price, 0, ',', '.') }}</p>
                        <p class="mb-2"><strong class="font-semibold">Catatan Tambahan:</strong> {{ $booking->notes ?? '-' }}</p>
                    </div>
                </div>

                <div class="bg-white shadow-lg rounded-lg mb-6">
                    <div class="bg-gray-100 px-6 py-4 rounded-t-lg">
                        <h4 class="text-lg font-semibold text-gray-800">Pilihan Pengguna</h4>
                    </div>
                    <div class="p-6">
                        @if (!empty($booking->selected_backgrounds))
                        <p class="font-semibold mb-2">Background yang dipilih:</p>
                        <ul class="list-disc list-inside ml-4">
                            @foreach ($booking->selected_backgrounds as $bg)
                            <li>{{ $bg['name'] }}</li>
                            @endforeach
                        </ul>
                        @else
                   
                        @endif
                        <h5 class="font-semibold mb-2">Extra Items:</h5>
                        @if ($booking->selected_extra_items)
                        <ul class="list-disc list-inside ml-4">
                            @foreach ($booking->selected_extra_items as $extra)
                            <li>{{ $extra['name'] }} (Rp.{{ number_format($extra['price'], 0, ',', '.') }})</li>
                            @endforeach
                        </ul>
                        @else
                        <p class="text-gray-500">Tidak ada extra item.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="w-full md:w-1/3 px-4">
                <div class="bg-white shadow-lg rounded-lg mb-6">
                    <div class="bg-gray-100 px-6 py-4 rounded-t-lg">
                        <h4 class="text-lg font-semibold text-gray-800">Perbarui Status</h4>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('bookings.update', $booking->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-4">
                                <label for="status" class="block text-sm font-medium text-gray-700">
                                    Status Saat Ini:
                                    @php
                                    $badgeColor = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'confirmed' => 'bg-green-100 text-green-800',
                                    'completed' => 'bg-blue-100 text-blue-800',
                                    'cancelled' => 'bg-red-100 text-red-800'
                                    ][$booking->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $badgeColor }}">{{ ucfirst($booking->status) }}</span>
                                </label>
                                <select name="status" id="status" class="mt-2 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                                    <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="completed" {{ $booking->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded transition-colors duration-200">Simpan Perubahan</button>
                        </form>
                        <hr class="my-4 border-t border-gray-300">
                        <form action="{{ route('bookings.destroy', $booking->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesanan ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded transition-colors duration-200">Hapus Pesanan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>