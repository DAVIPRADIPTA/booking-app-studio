<x-layouts.app :title="__('Manajemen Pesanan')">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Manajemen Pesanan</h1>
        </div>

        <!-- Alert Success -->
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <!-- Table Container -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">ID</th>
                            <th class="py-3 px-6 text-left">Nama Kontak</th>
                            <th class="py-3 px-6 text-left">Paket</th>
                            <th class="py-3 px-6 text-left">Tanggal & Waktu</th>
                            <th class="py-3 px-6 text-left">Status</th>
                            <th class="py-3 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                        @forelse ($bookings as $booking)
                            <tr class="border-b border-gray-200 hover:bg-gray-50 transition duration-150">
                                <td class="py-3 px-6 whitespace-nowrap font-medium text-gray-800">
                                    #{{ $booking->id }}
                                </td>
                                <td class="py-3 px-6">
                                    {{ $booking->contact_name }}
                                </td>
                                <td class="py-3 px-6">
                                    {{ $booking->package_name }}
                                </td>
                                <td class="py-3 px-6">
                                    {{ \Carbon\Carbon::parse($booking->booking_date)->translatedFormat('d F Y') }}
                                    <br>
                                    <span class="text-xs text-gray-500">{{ $booking->booking_time }}</span>
                                </td>
                                <td class="py-3 px-6">
                                    @php
                                        $badgeColor = match ($booking->status) {
                                            'waiting' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                            'booked' => 'bg-green-100 text-green-800 border-green-200',
                                            'completed' => 'bg-blue-100 text-blue-800 border-blue-200',
                                            default => 'bg-gray-100 text-gray-800 border-gray-200'
                                        };
                                    @endphp
                                    <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full border {{ $badgeColor }}">
                                        {{ ucfirst($booking->status === 'waiting' ? 'Menunggu DP' : ($booking->status === 'booked' ? 'Sudah Dibooking' : 'Selesai')) }}
                                    </span>
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <a href="{{ route('bookings.show', $booking->id) }}"
                                       class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm py-1.5 px-3 rounded-lg transition duration-200 shadow-sm">
                                        Lihat Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 px-6 text-center text-gray-500 italic">
                                    Tidak ada pesanan saat ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $bookings->links('pagination::tailwind') }}
        </div>
    </div>
</x-layouts.app>