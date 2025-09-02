<x-layouts.app :title="__('Manajemen Pesanan')">
    <div class="max-w-7xl mx-auto px-4 py-8">
        {{-- Header: single-line, tidak menumpuk --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <div class="flex items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800 leading-tight">Manajemen Pesanan</h1>
                    <p class="mt-1 text-sm text-gray-500">Tampilan ringkas & profesional — cari, filter, sunting.</p>
                </div>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button onclick="history.back()"
                    class="flex items-center gap-2 text-sm px-3 py-2 rounded bg-gray-100 hover:bg-gray-200 text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </button>

                <a href="{{ route('bookings.create') }}"
                    class="text-sm px-4 py-2 rounded bg-green-600 hover:bg-green-700 text-white shadow-sm">+ Tambah Pesanan</a>
            </div>
        </div>

        {{-- Alerts --}}
        @if (session('successMessage'))
            <div class="mb-4 p-3 rounded bg-green-50 border border-green-100 text-green-800 text-sm shadow-sm">
                {{ session('successMessage') }}
            </div>
        @endif
        @if (session('errorMessage'))
            <div class="mb-4 p-3 rounded bg-red-50 border border-red-100 text-red-800 text-sm shadow-sm">
                {{ session('errorMessage') }}
            </div>
        @endif

        {{-- Filters (compact) --}}
        <form method="GET" action="{{ route('bookings.index') }}" class="mb-6 bg-white border border-gray-100 rounded p-4 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama / WA / paket"
                    class="w-full border rounded px-3 py-2 text-sm focus:ring-1 focus:ring-indigo-300" aria-label="Cari"/>

                <select name="status" class="w-full border rounded px-3 py-2 text-sm focus:ring-1 focus:ring-indigo-300" aria-label="Filter status">
                    <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>Semua status</option>
                    <option value="waiting_payment" {{ request('status') === 'waiting_payment' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                    <option value="pending_verification" {{ request('status') === 'pending_verification' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="booked" {{ request('status') === 'booked' ? 'selected' : '' }}>Sudah Dibooking</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    <option value="cancellation_requested" {{ request('status') === 'cancellation_requested' ? 'selected' : '' }}>Permohonan Batal</option>
                </select>

                <select name="package_name" class="w-full border rounded px-3 py-2 text-sm focus:ring-1 focus:ring-indigo-300" aria-label="Filter paket">
                    <option value="all" {{ request('package_name', 'all') === 'all' ? 'selected' : '' }}>Semua paket</option>
                    @foreach($packages as $pkg)
                        <option value="{{ $pkg }}" {{ request('package_name') === $pkg ? 'selected' : '' }}>{{ $pkg }}</option>
                    @endforeach
                </select>

                <div class="flex gap-2">
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="w-1/2 border rounded px-3 py-2 text-sm focus:ring-1 focus:ring-indigo-300" aria-label="Dari tanggal" />
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="w-1/2 border rounded px-3 py-2 text-sm focus:ring-1 focus:ring-indigo-300" aria-label="Sampai tanggal" />
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-between mt-3 gap-3">
                <div class="flex items-center gap-3 text-sm text-gray-600">
                    <label class="text-xs text-gray-600 mr-2">Urutkan</label>
                    <select name="sort_by" class="border rounded px-2 py-1 text-sm" aria-label="Urutkan daftar booking">
                        <option value="created_at_desc" {{ request('sort_by', 'created_at_desc') === 'created_at_desc' ? 'selected' : '' }}>Terbaru dibuat (baru → lama)</option>
                        <option value="created_at_asc" {{ request('sort_by') === 'created_at_asc' ? 'selected' : '' }}>Terlama dibuat (lama → baru)</option>
                        <option value="booking_date_desc" {{ request('sort_by') === 'booking_date_desc' ? 'selected' : '' }}>Tanggal sesi — terlama dulu</option>
                        <option value="booking_date_asc" {{ request('sort_by') === 'booking_date_asc' ? 'selected' : '' }}>Tanggal sesi — terdekat dulu</option>
                    </select>

                    <label class="ml-2">Per halaman</label>
                    <select name="per_page" class="border rounded px-2 py-1 text-sm" aria-label="Per halaman">
                        <option value="10" {{ (int) request('per_page', 10) === 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ (int) request('per_page') === 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ (int) request('per_page') === 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="bg-indigo-600 text-white text-sm px-3 py-2 rounded shadow-sm hover:bg-indigo-700">Terapkan</button>
                    <a href="{{ route('bookings.index') }}" class="bg-gray-100 text-gray-700 text-sm px-3 py-2 rounded hover:bg-gray-200">Reset</a>
                </div>
            </div>
        </form>

        {{-- Stats ringkas --}}
        @php
            $pendingCount = $pendingCount ?? \App\Models\Booking::where('cancellation_requested', true)->count();
        @endphp
        <div class="flex flex-wrap gap-3 items-center mb-4 text-sm">
            <div class="px-3 py-2 bg-white border rounded text-gray-700">Total: <span class="font-semibold">{{ $bookings->total() }}</span></div>
            <div class="px-3 py-2 bg-yellow-50 border rounded text-yellow-800">Menunggu Bayar: <span class="font-semibold">{{ $statusCounts['waiting_payment'] ?? 0 }}</span></div>
            <div class="px-3 py-2 bg-orange-50 border rounded text-orange-800">Menunggu Verif: <span class="font-semibold">{{ $statusCounts['pending_verification'] ?? 0 }}</span></div>
            <div class="px-3 py-2 bg-green-50 border rounded text-green-800">Dibooking: <span class="font-semibold">{{ $statusCounts['booked'] ?? 0 }}</span></div>
            <div class="px-3 py-2 bg-blue-50 border rounded text-blue-800">Selesai: <span class="font-semibold">{{ $statusCounts['completed'] ?? 0 }}</span></div>
            <div class="px-3 py-2 bg-red-50 border rounded text-red-800">Dibatalkan: <span class="font-semibold">{{ $statusCounts['cancelled'] ?? 0 }}</span></div>
            <div class="px-3 py-2 bg-yellow-50 border rounded text-yellow-800">Permohonan Batal: <span class="font-semibold">{{ $pendingCount }}</span></div>
        </div>

        {{-- Responsive: mobile cards (visible on small) --}}
        <div class="space-y-3 md:hidden">
            @forelse($bookings as $booking)
                @php
                    $status = $booking->status;
                    $badge = match($status) {
                        'waiting_payment' => ['bg'=>'bg-yellow-100','text'=>'text-yellow-800','label'=>'Menunggu Bayar'],
                        'pending_verification' => ['bg'=>'bg-orange-100','text'=>'text-orange-800','label'=>'Menunggu Verif'],
                        'booked' => ['bg'=>'bg-green-100','text'=>'text-green-800','label'=>'Dibooking'],
                        'completed' => ['bg'=>'bg-blue-100','text'=>'text-blue-800','label'=>'Selesai'],
                        'cancelled' => ['bg'=>'bg-red-100','text'=>'text-red-800','label'=>'Dibatalkan'],
                        default => ['bg'=>'bg-gray-100','text'=>'text-gray-800','label'=>ucfirst($status)],
                    };
                @endphp

                <article class="bg-white border rounded-lg p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-semibold text-gray-800 truncate">#{{ $booking->id }} — {{ $booking->contact_name }}</h3>
                                <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</span>
                            </div>
                            <div class="text-xs text-gray-500 mt-1 truncate">{{ $booking->whatsapp_number }}</div>
                            <div class="text-xs text-gray-500 mt-2">{{ $booking->package_name }} @if($booking->session_name) • {{ $booking->session_name }} @endif</div>
                        </div>

                        <div class="text-right flex-shrink-0">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border {{ $badge['bg'] }} {{ $badge['text'] }}">{{ $badge['label'] }}</span>
                            <div class="mt-3 space-y-2">
                                <a href="{{ route('bookings.show', $booking->id) }}" class="block text-center text-xs px-3 py-1 rounded bg-blue-600 text-white">Detail</a>
                                @if($booking->status === 'booked')
                                    <a href="{{ route('bookings.edit', $booking->id) }}" class="block text-center text-xs px-3 py-1 rounded bg-indigo-600 text-white">Edit</a>
                                @else
                                    <span class="block text-center text-xs px-3 py-1 rounded bg-gray-100 text-gray-400">Edit</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($booking->cancellation_requested)
                        <div class="mt-3 text-xs text-yellow-800 font-medium">Permintaan Batal — {{ \Illuminate\Support\Str::limit($booking->cancellation_reason ?? '-', 60) }}</div>
                    @endif
                </article>
            @empty
                <div class="text-center text-gray-500 p-6">Belum ada pesanan.</div>
            @endforelse
        </div>

        {{-- Table clean & compact (desktop) --}}
        <div class="hidden md:block bg-white border rounded shadow-sm overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="py-3 px-4 text-left">ID</th>
                        <th class="py-3 px-4 text-left">Kontak & Status</th>
                        <th class="py-3 px-4 text-left">Paket</th>
                        <th class="py-3 px-4 text-left">Tanggal / Waktu</th>
                        <th class="py-3 px-4 text-left">Total</th>
                        <th class="py-3 px-4 text-left">Pembatalan</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody class="text-gray-700">
                    @forelse($bookings as $booking)
                        @php
                            $status = $booking->status;
                            switch ($status) {
                                case 'waiting_payment':
                                    $badgeBg = 'bg-yellow-100'; $badgeText = 'text-yellow-800'; $badgeBorder = 'border-yellow-200'; $label = 'Menunggu Bayar'; break;
                                case 'pending_verification':
                                    $badgeBg = 'bg-orange-100'; $badgeText = 'text-orange-800'; $badgeBorder = 'border-orange-200'; $label = 'Menunggu Verif'; break;
                                case 'booked':
                                    $badgeBg = 'bg-green-100'; $badgeText = 'text-green-800'; $badgeBorder = 'border-green-200'; $label = 'Dibooking'; break;
                                case 'completed':
                                    $badgeBg = 'bg-blue-100'; $badgeText = 'text-blue-800'; $badgeBorder = 'border-blue-200'; $label = 'Selesai'; break;
                                case 'cancelled':
                                    $badgeBg = 'bg-red-100'; $badgeText = 'text-red-800'; $badgeBorder = 'border-red-200'; $label = 'Dibatalkan'; break;
                                default:
                                    $badgeBg = 'bg-gray-100'; $badgeText = 'text-gray-800'; $badgeBorder = 'border-gray-200'; $label = ucfirst($status); break;
                            }
                        @endphp

                        <tr class="border-t hover:bg-gray-50">
                            <td class="py-3 px-4 font-medium w-28">#{{ $booking->id }}</td>

                            {{-- CONTACT + STATUS (compact) --}}
                            <td class="py-3 px-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="truncate w-56 font-medium text-gray-800">{{ $booking->contact_name }}</div>
                                        <div class="text-xs text-gray-400 mt-1">{{ $booking->whatsapp_number }}</div>
                                    </div>

                                    <div class="shrink-0 ml-3 text-right">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border {{ $badgeBg }} {{ $badgeText }} {{ $badgeBorder }}" aria-label="Status: {{ $label }}">
                                            {{ $label }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3 px-4 w-48">
                                <div class="font-medium text-gray-800">{{ $booking->package_name }}</div>
                                @if(!empty($booking->session_name))
                                    <div class="text-xs text-gray-500 mt-1">{{ $booking->session_name }}</div>
                                @endif
                            </td>

                            <td class="py-3 px-4 w-40">
                                <div class="text-gray-800">{{ \Carbon\Carbon::parse($booking->booking_date)->translatedFormat('d M Y') }}</div>
                                <div class="text-xs text-gray-400 mt-1">{{ $booking->booking_time }}</div>
                            </td>

                            <td class="py-3 px-4 w-36">
                                <div class="font-semibold text-gray-900">Rp {{ number_format($booking->total_price ?? 0, 0, ',', '.') }}</div>
                                <div class="text-xs text-gray-400 mt-1">Metode: {{ ucfirst($booking->payment_method ?? 'transfer') }}</div>
                            </td>

                            <td class="py-3 px-4 w-56">
                                @if($booking->cancellation_requested)
                                    <div class="text-xs text-yellow-800 font-medium">Permintaan Batal</div>
                                    <div class="text-xs text-gray-500 truncate w-48">{{ \Illuminate\Support\Str::limit($booking->cancellation_reason ?? '-', 60) }}</div>
                                    <div class="text-xs text-gray-400 mt-1">Diajukan: {{ optional($booking->cancellation_requested_at)->format('Y-m-d') }}</div>
                                @else
                                    <div class="text-xs text-gray-400">-</div>
                                @endif
                            </td>

                            <td class="py-3 px-4 text-center w-40">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('bookings.show', $booking->id) }}" class="px-3 py-1 rounded bg-blue-600 text-white text-sm hover:bg-blue-700">Detail</a>

                                    @if($booking->status === 'booked')
                                        <a href="{{ route('bookings.edit', $booking->id) }}" class="px-3 py-1 rounded bg-indigo-600 text-white text-sm hover:bg-indigo-700">Edit</a>
                                    @else
                                        <span class="px-3 py-1 rounded bg-gray-100 text-gray-400 text-sm cursor-not-allowed">Edit</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-gray-500">Belum ada pesanan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $bookings->links('pagination::tailwind') }}
        </div>
    </div>
</x-layouts.app>
