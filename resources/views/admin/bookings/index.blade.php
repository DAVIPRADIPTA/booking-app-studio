<x-layouts.app :title="__('Manajemen Pesanan')">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Manajemen Pesanan</h1>
                <p class="text-sm text-gray-500 mt-1">Kelola booking — cari, filter, dan edit pesanan.</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('bookings.create') }}"
                   class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg shadow-md transition duration-200">
                    + Tambah Pesanan
                </a>
            </div>
        </div>

        <!-- Alerts -->
        @if (session('successMessage'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4" role="alert">
                {{ session('successMessage') }}
            </div>
        @elseif (session('errorMessage'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4" role="alert">
                {{ session('errorMessage') }}
            </div>
        @endif

        <!-- Filters -->
        <form method="GET" action="{{ route('bookings.index') }}" class="bg-white rounded-lg shadow p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div>
                    <label class="text-xs text-gray-600">Cari (nama / WA / paket)</label>
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Contoh: Budi, +6281..."
                           class="mt-1 w-full border rounded px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="text-xs text-gray-600">Status</label>
                    <select name="status" class="mt-1 w-full border rounded px-3 py-2 text-sm">
                        <option value="all" {{ request('status','all') === 'all' ? 'selected' : '' }}>Semua</option>
                        <option value="waiting_payment" {{ request('status') === 'waiting_payment' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                        <option value="pending_verification" {{ request('status') === 'pending_verification' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                        <option value="booked" {{ request('status') === 'booked' ? 'selected' : '' }}>Sudah Dibooking</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>

                <div>
                    <label class="text-xs text-gray-600">Paket</label>
                    <select name="package_name" class="mt-1 w-full border rounded px-3 py-2 text-sm">
                        <option value="all" {{ request('package_name','all') === 'all' ? 'selected' : '' }}>Semua Paket</option>
                        @foreach($packages as $pkg)
                            <option value="{{ $pkg }}" {{ request('package_name') === $pkg ? 'selected' : '' }}>
                                {{ $pkg }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2 items-end">
                    <div class="w-1/2">
                        <label class="text-xs text-gray-600">Dari</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="mt-1 w-full border rounded px-3 py-2 text-sm">
                    </div>
                    <div class="w-1/2">
                        <label class="text-xs text-gray-600">Sampai</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="mt-1 w-full border rounded px-3 py-2 text-sm">
                    </div>
                </div>
            </div>

            <div class="flex justify-between items-center mt-3">
                <div class="flex items-center gap-2">
                    <label class="text-xs text-gray-600 mr-2">Sort</label>

                    {{-- Default sort di sini diset ke created_at_desc (Dibuat: baru → lama) --}}
                    @php $currentSort = request('sort_by', 'created_at_desc'); @endphp

                    <select name="sort_by" class="border rounded px-3 py-2 text-sm">
                        <option value="created_at_desc" {{ $currentSort === 'created_at_desc' ? 'selected' : '' }}>Dibuat (baru → lama)</option>
                        <option value="created_at_asc" {{ $currentSort === 'created_at_asc' ? 'selected' : '' }}>Dibuat (lama → baru)</option>
                        <option value="booking_date_desc" {{ $currentSort === 'booking_date_desc' ? 'selected' : '' }}>Tanggal Booking (baru → lama)</option>
                        <option value="booking_date_asc" {{ $currentSort === 'booking_date_asc' ? 'selected' : '' }}>Tanggal Booking (lama → baru)</option>
                    </select>

                    <label class="text-xs text-gray-600 ml-4 mr-2">Per halaman</label>
                    @php $perPage = (int) request('per_page', 10); @endphp
                    <select name="per_page" class="border rounded px-3 py-2 text-sm">
                        <option value="10" {{ $perPage === 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ $perPage === 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $perPage === 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Terapkan</button>
                    <a href="{{ route('bookings.index') }}" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded text-gray-700">Reset</a>
                </div>
            </div>
        </form>

        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-6 gap-3 mb-6">
            <div class="bg-white p-3 rounded shadow text-sm">
                <div class="text-xs text-gray-500">Total</div>
                <div class="text-lg font-semibold">{{ $bookings->total() }}</div>
            </div>
            @php
                $statuses = [
                    'waiting_payment' => 'Menunggu Pembayaran',
                    'pending_verification' => 'Menunggu Verifikasi',
                    'booked' => 'Dibooking',
                    'completed' => 'Selesai',
                    'cancelled' => 'Dibatalkan'
                ];
            @endphp
            @foreach($statuses as $key => $label)
                <div class="bg-white p-3 rounded shadow text-sm">
                    <div class="text-xs text-gray-500">{{ $label }}</div>
                    <div class="text-lg font-semibold">{{ $statusCounts[$key] ?? 0 }}</div>
                </div>
            @endforeach
        </div>

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
                                <td class="py-3 px-6">
                                    {{ $booking->contact_name }}
                                    <div class="text-xs text-gray-400">{{ $booking->whatsapp_number }}</div>
                                </td>
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
                                    <div class="flex justify-center space-x-2">
                                        <a href="{{ route('bookings.show', $booking->id) }}"
                                           class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-sm transition-colors">
                                            Detail
                                        </a>

                                        @if($booking->status === 'booked')
                                            <a href="{{ route('bookings.edit', $booking->id) }}"
                                               class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-sm transition-colors">
                                                Edit
                                            </a>
                                        @else
                                            <span class="bg-gray-400 text-white px-3 py-1.5 rounded-lg text-sm cursor-not-allowed opacity-50"
                                                  title="Hanya booking dengan status 'Sudah Dibooking' yang bisa di-edit">
                                                Edit
                                            </span>
                                        @endif
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
</x-layouts.app>
