{{-- resources/views/admin/dashboard.blade.php --}}
<x-layouts.app :title="__('Dashboard')">
    <div class="max-w-7xl mx-auto px-4 py-8 space-y-6">
        {{-- Header --}}
        <div class="rounded-2xl border bg-white p-6 shadow-sm flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold">Dashboard — Laporan Ringkas</h1>
                <p class="text-sm text-gray-500 mt-1">Ringkasan keuangan, produk & pemesanan. Simple — profesional.</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('bookings.index') }}" class="px-3 py-2 bg-gray-100 rounded text-sm">Kelola Booking</a>
                <a href="{{ route('dashboard.export.bookings', request()->query()) }}"
                   class="px-3 py-2 bg-green-600 text-white rounded text-sm">Export Bookings CSV</a>
            </div>
        </div>

        {{-- KPI --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-4 bg-white rounded border">
                <div class="text-sm text-gray-500">Total Booking</div>
                <div class="text-xl font-semibold">{{ number_format($totalBookings) }}</div>
                <div class="text-xs text-gray-400 mt-1">Booking terdaftar</div>
            </div>

            <div class="p-4 bg-white rounded border">
                <div class="text-sm text-gray-500">Pendapatan (excl. cancelled)</div>
                <div class="text-xl font-semibold">Rp {{ number_format($totalRevenue,0,',','.') }}</div>
                <div class="text-xs text-gray-400 mt-1">Refunds: Rp {{ number_format($totalRefunds,0,',','.') }} — Net: Rp {{ number_format($netRevenue,0,',','.') }}</div>
            </div>

            <div class="p-4 bg-white rounded border">
                <div class="text-sm text-gray-500">Menunggu Pembayaran</div>
                <div class="text-xl font-semibold">{{ number_format($pendingPayments) }}</div>
                <div class="text-xs text-gray-400 mt-1">Belum upload bukti</div>
            </div>

            <div class="p-4 bg-white rounded border">
                <div class="text-sm text-gray-500">Permohonan Batal</div>
                <div class="text-xl font-semibold text-yellow-700">{{ number_format($cancellationRequests) }}</div>
                <div class="text-xs text-gray-400 mt-1">Diajukan customer</div>
            </div>
        </div>

        {{-- Filters (sama seperti sebelumnya) --}}
        <div class="bg-white rounded border p-4">
            <form method="GET" action="{{ route('dashboard') }}" class="grid grid-cols-1 md:grid-cols-6 gap-2 items-end">
                <div class="md:col-span-2">
                    <label class="text-xs text-gray-500">Cari</label>
                    <input type="search" name="q" value="{{ request('q') }}" class="w-full border rounded px-2 py-2 text-sm" placeholder="Nama / WA / Paket">
                </div>

                <div>
                    <label class="text-xs text-gray-500">Status</label>
                    <select name="status" class="w-full border rounded px-2 py-2 text-sm">
                        <option value="all" {{ request('status','all')==='all' ? 'selected':'' }}>Semua</option>
                        <option value="waiting_payment" {{ request('status')==='waiting_payment' ? 'selected':''}}>Menunggu Bayar</option>
                        <option value="pending_verification" {{ request('status')==='pending_verification' ? 'selected':''}}>Menunggu Verif</option>
                        <option value="booked" {{ request('status')==='booked' ? 'selected':''}}>Dibooking</option>
                        <option value="completed" {{ request('status')==='completed' ? 'selected':''}}>Selesai</option>
                        <option value="cancelled" {{ request('status')==='cancelled' ? 'selected':''}}>Dibatalkan</option>
                    </select>
                </div>

                <div>
                    <label class="text-xs text-gray-500">Dari (tgl)</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full border rounded px-2 py-2 text-sm">
                </div>

                <div>
                    <label class="text-xs text-gray-500">Sampai (tgl)</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full border rounded px-2 py-2 text-sm">
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Terapkan</button>
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-100 rounded">Reset</a>
                </div>
            </form>
        </div>

        {{-- Charts: Bookings by day & Revenue by month --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="bg-white rounded border p-4">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h3 class="font-semibold">Bookings per Hari ({{ $bookingsDays }} hari)</h3>
                        <div class="text-xs text-gray-500">Jumlah booking per tanggal</div>
                    </div>
                    <div class="text-xs text-gray-500">Fallback: Tabel di bawah</div>
                </div>

                <canvas id="chartBookingsDays" height="120"></canvas>

                {{-- fallback table for days (also useful for accessibility) --}}
                <div class="mt-3 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-xs text-gray-600">
                            <tr>
                                <th class="px-3 py-2 text-left">Tanggal</th>
                                <th class="px-3 py-2 text-right">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookingsByDay as $row)
                                <tr class="border-t">
                                    <td class="px-3 py-2">{{ $row['day'] }}</td>
                                    <td class="px-3 py-2 text-right">{{ $row['count'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded border p-4">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h3 class="font-semibold">Pendapatan per Bulan ({{ $financeMonths }} bulan)</h3>
                        <div class="text-xs text-gray-500">Revenue / Refund / Net</div>
                    </div>
                    <div class="text-xs text-gray-500">Export CSV tersedia</div>
                </div>

                <canvas id="chartRevenueMonths" height="120"></canvas>

                <div class="mt-3 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-xs text-gray-600">
                            <tr>
                                <th class="px-3 py-2 text-left">Periode</th>
                                <th class="px-3 py-2 text-right">Pendapatan</th>
                                <th class="px-3 py-2 text-right">Refunds</th>
                                <th class="px-3 py-2 text-right">Net</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(array_values($months) as $m)
                                <tr class="border-t">
                                    <td class="px-3 py-2">{{ $m['label'] }}</td>
                                    <td class="px-3 py-2 text-right">Rp {{ number_format($m['revenue'] ?? 0,0,',','.') }}</td>
                                    <td class="px-3 py-2 text-right">Rp {{ number_format($m['refunds'] ?? 0,0,',','.') }}</td>
                                    <td class="px-3 py-2 text-right font-semibold">Rp {{ number_format($m['net'] ?? 0,0,',','.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        {{-- Top packages, cancellations, bookings list (sama seperti sebelum) --}}
        {{-- Top packages --}}
        <div class="bg-white rounded border p-4">
            <h3 class="font-semibold mb-2">Top Paket (12 bulan)</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                @forelse($topPackages as $tp)
                    <div class="p-3 border rounded">
                        <div class="font-medium">{{ $tp->package_name ?: '-' }}</div>
                        <div class="text-xs text-gray-500">Bookings: {{ $tp->cnt }} — Revenue: Rp {{ number_format($tp->revenue,0,',','.') }}</div>
                    </div>
                @empty
                    <div class="text-sm text-gray-400">Belum ada data paket.</div>
                @endforelse
            </div>
        </div>

        {{-- cancellations --}}
        <div class="bg-white rounded border p-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold">Permohonan Pembatalan Terbaru</h3>
                <a href="{{ route('bookings.cancellations.index') }}" class="text-xs text-indigo-600">Lihat semua</a>
            </div>
            @if($cancellations->isEmpty())
                <div class="text-sm text-gray-400">Tidak ada permohonan pembatalan.</div>
            @else
                <div class="space-y-2">
                    @foreach($cancellations as $c)
                        <div class="p-3 rounded border bg-gray-50 flex justify-between items-start">
                            <div>
                                <div class="font-medium">#{{ $c->id }} — {{ $c->contact_name }} ({{ $c->package_name }})</div>
                                <div class="text-xs text-gray-500 mt-1">{{ \Illuminate\Support\Str::limit($c->cancellation_reason ?? '-', 120) }}</div>
                            </div>
                            <div class="text-xs text-gray-400 text-right">
                                <div>{{ optional($c->cancellation_requested_at)->format('Y-m-d H:i') }}</div>
                                <a href="{{ route('bookings.show', $c->id) }}" class="text-indigo-600 text-xs">Detail</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- bookings list --}}
        <div class="bg-white rounded border p-4">
            <h3 class="font-semibold mb-3">Daftar Booking</h3>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-600">
                        <tr>
                            <th class="px-3 py-2 text-left">ID</th>
                            <th class="px-3 py-2 text-left">Kontak</th>
                            <th class="px-3 py-2 text-left">Paket</th>
                            <th class="px-3 py-2 text-left">Tanggal</th>
                            <th class="px-3 py-2 text-right">Total</th>
                            <th class="px-3 py-2 text-left">Status</th>
                            <th class="px-3 py-2 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookingsList as $b)
                            <tr class="border-t">
                                <td class="px-3 py-2">#{{ $b->id }}</td>
                                <td class="px-3 py-2">
                                    <div class="font-medium">{{ $b->contact_name }}</div>
                                    <div class="text-xs text-gray-400">{{ $b->whatsapp_number }}</div>
                                </td>
                                <td class="px-3 py-2">{{ $b->package_name }}</td>
                                <td class="px-3 py-2">
                                    {{ \Carbon\Carbon::parse($b->booking_date)->format('Y-m-d') }}
                                    <div class="text-xs text-gray-400">{{ $b->booking_time }}</div>
                                </td>
                                <td class="px-3 py-2 text-right">Rp {{ number_format($b->total_price ?? 0,0,',','.') }}</td>
                                <td class="px-3 py-2">{{ ucfirst($b->status) }}</td>
                                <td class="px-3 py-2"><a href="{{ route('bookings.show', $b->id) }}" class="text-indigo-600 text-xs">Detail</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-3 py-6 text-center text-gray-400">Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $bookingsList->links('pagination::tailwind') }}
            </div>
        </div>
    </div>

    {{-- Chart.js via CDN. Jika gagal, tabel tetap tampil. --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" defer></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // parse server JSON (already JSON-safe in controller)
            const bookingsDaysData = {!! $chartBookingsDays ?? '[]' !!};
            const monthsData = {!! $chartMonths ?? '[]' !!};

            // Bookings per day chart
            try {
                const ctx1 = document.getElementById('chartBookingsDays').getContext('2d');
                const labels1 = bookingsDaysData.map(r => r.day);
                const values1 = bookingsDaysData.map(r => r.count);

                new Chart(ctx1, {
                    type: 'line',
                    data: {
                        labels: labels1,
                        datasets: [{
                            label: 'Bookings per day',
                            data: values1,
                            fill: true,
                            tension: 0.2,
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            x: { display: true },
                            y: { display: true, beginAtZero: true }
                        }
                    }
                });
            } catch (e) {
                // if Chart not available: do nothing (fallback table already present)
                console.warn('Chart bookings error', e);
            }

            // Revenue per month chart (stacked revenue/refund/net)
            try {
                const ctx2 = document.getElementById('chartRevenueMonths').getContext('2d');
                const labels2 = monthsData.map(m => m.label);
                const revenue = monthsData.map(m => Number(m.revenue || 0));
                const refunds = monthsData.map(m => Number(m.refunds || 0));
                const net = monthsData.map(m => Number(m.net || 0));

                new Chart(ctx2, {
                    type: 'bar',
                    data: {
                        labels: labels2,
                        datasets: [
                            { label: 'Revenue', data: revenue, stack: 'stack1', },
                            { label: 'Refunds', data: refunds, stack: 'stack1' },
                            { label: 'Net', data: net, stack: 'stack2' }
                        ]
                    },
                    options: {
                        responsive: true,
                        interaction: { mode: 'index', intersect: false },
                        plugins: { legend: { position: 'top' } },
                        scales: {
                            x: { stacked: true },
                            y: { stacked: false, beginAtZero: true }
                        }
                    }
                });
            } catch (e) {
                console.warn('Chart revenue error', e);
            }
        });
    </script>
</x-layouts.app>
