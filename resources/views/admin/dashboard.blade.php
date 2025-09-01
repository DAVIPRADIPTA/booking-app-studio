<x-layouts.app :title="__('Dashboard')">

@push('styles')
<style>
  /* small helpers */
  .stat-card { border-radius: 10px; padding: 14px; background:#fff; box-shadow:0 6px 18px rgba(2,6,23,0.04); border:1px solid #eef2f7; }
  .chip { display:inline-flex; align-items:center; gap:.5rem; padding:.35rem .6rem; border-radius:999px; background:#f3f4f6; font-weight:600; font-size:.85rem; }
  .controls .btn { cursor:pointer; padding:.35rem .6rem; border-radius:.5rem; border:1px solid transparent; }
  .controls .btn.active { background:#4f46e5; color:#fff; border-color: rgba(79,70,229,.9); }
  .chart-box { min-height:160px; }
  .recent-table td, .recent-table th { vertical-align: middle; }
</style>
@endpush

@php
  // safe defaults if controller not set
  $lineChart = $lineChart ?? ['labels'=>[], 'data'=>[]];
  $barChart  = $barChart ?? ['labels'=>[], 'data'=>[]];
  $pieChart  = $pieChart ?? ['labels'=>[], 'data'=>[]];
  $topExtras = $topExtras ?? [];
  $recentBookings = $recentBookings ?? collect();
@endphp

<div class="container mx-auto px-4 py-8">
  <div class="mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
      <p class="text-sm text-gray-500">Ringkasan cepat — laporan, grafik, dan riwayat booking.</p>
    </div>

    <div class="flex items-center gap-3">
      <div class="chip">Periode: <strong id="dataRangeLabel">14 hari</strong></div>
      <button id="exportCsv" class="ml-2 px-3 py-1 rounded bg-gray-100 hover:bg-gray-200 text-sm">Export CSV</button>
    </div>
  </div>

  {{-- Top stats --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="stat-card">
      <div class="text-xs text-gray-500">Total Booking</div>
      <div class="text-2xl font-bold" id="statTotal">{{ number_format($totalBookings ?? 0) }}</div>
    </div>
    <div class="stat-card">
      <div class="text-xs text-gray-500">Hari Ini</div>
      <div class="text-2xl font-bold" id="statToday">{{ number_format($bookingsToday ?? 0) }}</div>
    </div>
    <div class="stat-card">
      <div class="text-xs text-gray-500">Minggu Ini</div>
      <div class="text-2xl font-bold" id="statWeek">{{ number_format($bookingsThisWeek ?? 0) }}</div>
    </div>
    <div class="stat-card">
      <div class="text-xs text-gray-500">Bulan Ini</div>
      <div class="text-2xl font-bold" id="statMonth">{{ number_format($bookingsThisMonth ?? 0) }}</div>
    </div>
  </div>

  {{-- Charts --}}
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white rounded-lg p-4 shadow">
      <div class="flex items-center justify-between mb-3">
        <div>
          <h3 class="font-semibold">Booking (per hari)</h3>
          <div class="text-xs text-gray-500">Tren selama 14 hari terakhir</div>
        </div>
        <div class="text-sm text-gray-500">Cap: <strong id="chartCap">1000</strong></div>
      </div>
      <div class="chart-box"><canvas id="lineBookings"></canvas></div>
    </div>

    <div class="bg-white rounded-lg p-4 shadow">
      <h3 class="font-semibold mb-2">Status Booking (periode)</h3>
      <div class="chart-box"><canvas id="pieStatus"></canvas></div>
      <div class="mt-3 text-sm text-gray-500">Distribusi status booking.</div>
    </div>
  </div>

  {{-- Bar + extras --}}
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg p-4 shadow lg:col-span-2">
      <h3 class="font-semibold mb-2">Top Paket (periode)</h3>
      <div class="chart-box"><canvas id="barPackages"></canvas></div>
    </div>

    <div class="bg-white rounded-lg p-4 shadow">
      <h3 class="font-semibold mb-2">Top Item Tambahan</h3>
      @if(count($topExtras))
        <ol class="list-decimal pl-5 space-y-2 text-sm text-gray-700">
          @foreach($topExtras as $ex)
            <li>
              <div class="font-medium">{{ $ex['name'] }}</div>
              <div class="text-xs text-gray-500">Terpilih: {{ $ex['count'] }} — Pendapatan: Rp{{ number_format($ex['revenue'],0,',','.') }}</div>
            </li>
          @endforeach
        </ol>
      @else
        <div class="text-sm text-gray-500">Belum ada data item tambahan.</div>
      @endif
    </div>
  </div>

  {{-- Recent bookings --}}
  <div class="bg-white rounded-lg p-4 shadow mb-6">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-semibold">Booking Terbaru</h3>
      <a href="{{ route('bookings.index') }}" class="text-sm text-indigo-600 hover:underline">Lihat semua</a>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-sm text-left recent-table">
        <thead class="text-xs text-gray-500 uppercase">
          <tr>
            <th class="py-2 px-3">ID</th>
            <th class="py-2 px-3">Customer</th>
            <th class="py-2 px-3">Paket</th>
            <th class="py-2 px-3">Tanggal</th>
            <th class="py-2 px-3">Total</th>
            <th class="py-2 px-3">Status</th>
          </tr>
        </thead>
        <tbody id="recentBookingsBody">
          @forelse($recentBookings as $b)
            <tr class="border-t">
              <td class="py-3 px-3 font-medium">#{{ $b->id }}</td>
              <td class="py-3 px-3">
                {{ $b->contact_name ?? optional($b->customer)->name ?? '-' }}
                <div class="text-xs text-gray-400">{{ $b->whatsapp_number ?? '' }}</div>
              </td>
              <td class="py-3 px-3">{{ $b->package_name ?? '-' }}</td>
              <td class="py-3 px-3">{{ optional($b->created_at)->format('d M Y H:i') }}</td>
              <td class="py-3 px-3">Rp{{ number_format($b->total_price ?? 0,0,',','.') }}</td>
              <td class="py-3 px-3">
                <span class="inline-block px-2 py-1 rounded text-xs {{ $b->status === 'booked' ? 'bg-green-100 text-green-700' : ($b->status === 'waiting_payment' ? 'bg-yellow-100 text-yellow-800' : ($b->status === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700')) }}">
                  {{ $b->status ?? 'unknown' }}
                </span>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="py-4 text-center text-gray-500">Belum ada booking terbaru.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  // Data from server (safe encoded)
  const line = @json($lineChart, JSON_UNESCAPED_UNICODE);
  const bar  = @json($barChart, JSON_UNESCAPED_UNICODE);
  const pie  = @json($pieChart, JSON_UNESCAPED_UNICODE);

  // contexts
  const lineCtx = document.getElementById('lineBookings')?.getContext('2d');
  const barCtx  = document.getElementById('barPackages')?.getContext('2d');
  const pieCtx  = document.getElementById('pieStatus')?.getContext('2d');

  // simple chart constructors
  if (lineCtx) {
    new Chart(lineCtx, {
      type: 'line',
      data: { labels: line.labels || [], datasets: [{ label: 'Bookings', data: line.data || [], fill: true, tension: .3, borderColor:'#3b82f6', backgroundColor:'rgba(59,130,246,0.08)' }] },
      options: { responsive: true, plugins:{ legend:{ display:false } }, scales:{ y:{ beginAtZero:true } } }
    });
  }

  if (barCtx) {
    new Chart(barCtx, {
      type: 'bar',
      data: { labels: bar.labels || [], datasets: [{ label: 'Popular Packages', data: bar.data || [], borderRadius:6 }] },
      options: { responsive:true, plugins:{ legend:{ display:false } }, scales:{ y:{ beginAtZero:true } } }
    });
  }

  if (pieCtx) {
    new Chart(pieCtx, {
      type: 'pie',
      data: { labels: pie.labels || [], datasets: [{ data: pie.data || [] }] },
      options: { responsive:true }
    });
  }

  // Export CSV button (simple frontend trigger --> you can implement export route later)
  document.getElementById('exportCsv')?.addEventListener('click', () => {
    // Example: redirect to backend export route if implemented: /dashboard/export?days=14
    const days = 14;
    window.location.href = `/dashboard/export?days=${days}`;
  });
});
</script>
@endpush

</x-layouts.app>
