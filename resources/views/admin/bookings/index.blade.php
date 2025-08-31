<x-layouts.app :title="__('Manajemen Pesanan')">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Manajemen Pesanan</h1>
            <a href="{{ route('bookings.create') }}"
               class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg shadow-md transition duration-200">
                + Tambah Pesanan
            </a>
        </div>

        <!-- Alert -->
        @if (session('successMessage'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4" role="alert">
                {{ session('successMessage') }}
            </div>
        @elseif (session('errorMessage'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4" role="alert">
                {{ session('errorMessage') }}
            </div>
        @endif

        <!-- Table -->
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
                                <td class="py-3 px-6 font-medium text-gray-800">#{{ $booking->id }}</td>
                                <td class="py-3 px-6">{{ $booking->contact_name }}</td>
                                <td class="py-3 px-6">{{ $booking->package_name }}</td>
                                <td class="py-3 px-6">
                                    {{ \Carbon\Carbon::parse($booking->booking_date)->translatedFormat('d F Y') }}
                                    <br>
                                    <span class="text-xs text-gray-500">{{ $booking->booking_time }}</span>
                                </td>
                                <td class="py-3 px-6">
                                    @php
                                        $badgeColor = match ($booking->status) {
                                            'waiting_payment' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                            'pending_verification' => 'bg-orange-100 text-orange-800 border-orange-200',
                                            'booked' => 'bg-green-100 text-green-800 border-green-200',
                                            'completed' => 'bg-blue-100 text-blue-800 border-blue-200',
                                            'cancelled' => 'bg-red-100 text-red-800 border-red-200',
                                            default => 'bg-gray-100 text-gray-800 border-gray-200'
                                        };

                                        $label = match ($booking->status) {
                                            'waiting_payment' => 'Menunggu Pembayaran',
                                            'pending_verification' => 'Menunggu Verifikasi',
                                            'booked' => 'Sudah Dibooking',
                                            'completed' => 'Selesai',
                                            'cancelled' => 'Dibatalkan',
                                            default => ucfirst($booking->status)
                                        };
                                    @endphp
                                    <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full border {{ $badgeColor }}">
                                        {{ $label }}
                                    </span>
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <div class="flex justify-center gap-2">
                                        <!-- Detail -->
                                        <a href="{{ route('bookings.show', $booking->id) }}"
                                           class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-sm">
                                            Detail
                                        </a>

                                        <!-- Dropdown Aksi -->
                                        <div class="relative">
                                            <button class="bg-gray-200 px-3 py-1.5 rounded-lg text-sm hover:bg-gray-300"
                                                onclick="toggleMenu('menu-{{ $booking->id }}')">
                                                Aksi ▼
                                            </button>
                                            <div id="menu-{{ $booking->id }}" class="hidden absolute bg-white shadow-lg rounded-lg mt-2 w-44 z-10">
                                                @if($booking->status === 'waiting_payment')
                                                    <form method="POST" action="{{ route('bookings.verifyPayment', $booking->id) }}">
                                                        @csrf
                                                        <button class="block w-full text-left px-4 py-2 hover:bg-gray-100 text-green-600">Verifikasi</button>
                                                    </form>
                                                @endif
                                                @if($booking->status === 'booked')
                                                    <form method="POST" action="{{ route('bookings.completeBooking', $booking->id) }}">
                                                        @csrf
                                                        <button class="block w-full text-left px-4 py-2 hover:bg-gray-100 text-blue-600">Tandai Selesai</button>
                                                    </form>
                                                @endif
                                                @if(!in_array($booking->status, ['cancelled','completed']))
                                                    <form method="POST" action="{{ route('bookings.forceCancel', $booking->id) }}">
                                                        @csrf
                                                        <button class="block w-full text-left px-4 py-2 hover:bg-gray-100 text-red-600">Batalkan</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
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

    <script>
        function toggleMenu(id) {
            document.querySelectorAll('[id^="menu-"]').forEach(menu => menu.classList.add('hidden'));
            document.getElementById(id).classList.toggle('hidden');
        }

        // klik luar dropdown close
        window.addEventListener('click', function(e) {
            document.querySelectorAll('[id^="menu-"]').forEach(menu => {
                if (!menu.contains(e.target) && !menu.previousElementSibling.contains(e.target)) {
                    menu.classList.add('hidden');
                }
            });
        });
    </script>
</x-layouts.app>
