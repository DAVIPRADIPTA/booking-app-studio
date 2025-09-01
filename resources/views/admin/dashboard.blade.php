{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard - Admin')

@push('styles')
<style>
  /* Minimal polished styles (tailwind utilities used in markup) */
  .stat-card { border-radius: 12px; padding: 18px; background: #fff; box-shadow: 0 6px 20px rgba(0,0,0,0.04); border: 1px solid #eef2f7; }
  .chip { display:inline-flex; align-items:center; gap:.5rem; padding:.35rem .6rem; border-radius:999px; background:#f3f4f6; font-weight:600; font-size:.85rem; }
  .controls .btn { cursor:pointer; padding:.4rem .7rem; border-radius:.5rem; border:1px solid transparent; }
  .controls .btn.active { background:#4f46e5; color:#fff; border-color: rgba(79,70,229,.9); }
  /* make canvas responsive container */
  .chart-box { min-height:160px; }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl font-bold">Dashboard</h1>
      <p class="text-sm text-gray-500">Laporan & statistik — ringkasan aktivitas, pendapatan, dan top-sellers.</p>
    </div>

    <div class="flex items-center gap-3">
      <div class="chip">Data up to <strong id="dataRangeLabel">30 hari</strong></div>
      <button id="exportCsv" class="ml-2 px-3 py-1 rounded bg-gray-100 hover:bg-gray-200 text-sm">Export CSV</button>
    </div>
  </div>

  {{-- TOP STATS --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="stat-card">
      <div class="text-xs text-gray-500">Total Booking</div>
      <div class="text-2xl font-bold" id="statTotal">{{ number_format($totalBookings ?? 0) }}</div>
    </div>
    <div class="stat-card">
      <div class="text-xs text-gray-500">Booking Hari Ini</div>
      <div class="text-2xl font-bold" id="statToday">{{ number_format($bookingsToday ?? 0) }}</div>
    </div>
    <div class="stat-card">
      <div class="text-xs text-gray-500">Booking Minggu Ini</div>
      <div class="text-2xl font-bold" id="statWeek">{{ number_format($bookingsThisWeek ?? 0) }}</div>
    </div>
    <div class="stat-card">
      <div class="text-xs text-gray-500">Booking Bulan Ini</div>
      <div class="text-2xl font-bold" id="statMonth">{{ number_format($bookingsThisMonth ?? 0) }}</div>
    </div>
  </div>

  {{-- REVENUE --}}
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="stat-card">
      <div class="text-xs text-gray-500">Pendapatan Hari Ini</div>
      <div class="text-2xl font-bold text-red-600" id="revToday">Rp{{ number_format($revenueToday ?? 0,0,',','.') }}</div>
    </div>
    <div class="stat-card">
      <div class="text-xs text-gray-500">Pendapatan Minggu Ini</div>
      <div class="text-2xl font-bold text-red-600" id="revWeek">Rp{{ number_format($revenueWeek ?? 0,0,',','.') }}</div>
    </div>
    <div class="stat-card">
      <div class="text-xs text-gray-500">Pendapatan Bulan Ini</div>
      <div class="text-2xl font-bold text-red-600" id="revMonth">Rp{{ number_format($revenueMonth ?? 0,0,',','.') }}</div>
    </div>
  </div>

  {{-- CONTROLS --}}
  <div class="flex items-center gap-3 mb-4 controls">
    <button class="btn px-3 py-1 bg-white border rounded shadow-sm" data-days="30">30d</button>
    <button class="btn px-3 py-1 bg-white border rounded shadow-sm" data-days="90">90d</button>
    <button class="btn px-3 py-1 bg-white border rounded shadow-sm" data-days="365">365d</button>
    <button class="btn px-3 py-1 bg-white border rounded shadow-sm" data-days="1000">1000d</button>
    <div class="ml-4 flex items-center gap-2">
      <label class="text-sm text-gray-600">Custom:</label>
      <input id="customDays" type="number" min="1" max="1000" placeholder="days" class="input px-2 py-1 border rounded" style="width:90px;">
      <button id="btnCustom" class="btn px-3 py-1 bg-indigo-600 text-white rounded">Apply</button>
    </div>
  </div>

  {{-- CHARTS ROW --}}
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white rounded-lg p-4 shadow">
      <div class="flex items-center justify-between mb-3">
        <div>
          <h3 class="font-semibold">Booking (per hari)</h3>
          <div class="text-xs text-gray-500">Trend jumlah booking per hari (klik range untuk ubah periode)</div>
        </div>
        <div class="text-sm text-gray-500">Points cap: <strong id="chartCap">1000</strong></div>
      </div>
      <div class="chart-box">
        <canvas id="lineBookings"></canvas>
      </div>
    </div>

    <div class="bg-white rounded-lg p-4 shadow">
      <h3 class="font-semibold mb-2">Status Booking</h3>
      <div class="chart-box">
        <canvas id="pieStatus"></canvas>
      </div>
      <div class="mt-3 text-sm text-gray-500">Distribusi berdasarkan status.</div>
    </div>
  </div>

  {{-- BAR + TOP EXTRAS --}}
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg p-4 shadow lg:col-span-2">
      <h3 class="font-semibold mb-2">Top Paket (periode)</h3>
      <div class="chart-box">
        <canvas id="barPackages"></canvas>
      </div>
    </div>

    <div class="bg-white rounded-lg p-4 shadow">
      <h3 class="font-semibold mb-2">Top Item Tambahan</h3>
      <div class="text-sm text-gray-600">
        @if(!empty($topExtras) && count($topExtras))
          <ol class="list-decimal pl-5 space-y-2">
            @foreach($topExtras as $ex)
              <li>
                <div class="font-medium">{{ $ex['name'] }}</div>
                <div class="text-xs text-gray-500">Terpilih: {{ $ex['count'] }} × — Pendapatan: Rp{{ number_format($ex['revenue'],0,',','.') }}</div>
              </li>
            @endforeach
          </ol>
        @else
          <div class="text-sm text-gray-500">Belum ada data item tambahan.</div>
        @endif
      </div>
    </div>
  </div>

  {{-- RECENT BOOKINGS WITH LOAD MORE (AJAX SAFE) --}}
  <div class="bg-white rounded-lg p-4 shadow mb-6">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-semibold">Booking Terbaru</h3>
      <a href="{{ route('bookings.index') }}" class="text-sm text-indigo-600 hover:underline">Lihat semua</a>
    </div>

    <div id="recentBookingsContainer" class="overflow-x-auto">
      <table class="w-full text-sm text-left">
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
                {{ $b->contact_name }}
                <div class="text-xs text-gray-400">{{ $b->whatsapp_number }}</div>
              </td>
              <td class="py-3 px-3">{{ $b->package_name }}</td>
              <td class="py-3 px-3">{{ $b->created_at->format('d M Y H:i') }}</td>
              <td class="py-3 px-3">Rp{{ number_format($b->total_price ?? 0,0,',','.') }}</td>
              <td class="py-3 px-3">
                <span class="inline-block px-2 py-1 rounded text-xs {{ $b->status === 'booked' ? 'bg-green-100 text-green-700' : ($b->status === 'waiting_payment' ? 'bg-yellow-100 text-yellow-800' : ($b->status === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700')) }}">
                  {{ $b->status }}
                </span>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="py-4 text-center text-gray-500">Belum ada booking terbaru.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-4 flex justify-center">
      <button id="loadMoreRecent" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded">Load more</button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
/*
  Dashboard frontend logic:
  - Fetch dynamic data from /api/dashboard/stats?days=X (if available)
  - Fallback to server-passed datasets ($lineChart, $barChart, $pieChart)
  - Client-side downsample to maxPoints to keep Chart.js responsive (default cap 1000)
  - "Load more" for recent bookings uses /api/bookings/recent?page=2 style endpoint (implement on backend)
*/

/* ---------- Helpers ---------- */
const safeParse = (v, d = []) => { try { return JSON.parse(v); } catch(e) { return d; } };
const sampleArray = (arr, max) => {
  // simple uniform downsample: take approx every step
  if (!Array.isArray(arr) || arr.length <= max) return arr.slice();
  const step = arr.length / max;
  const out = [];
  for (let i=0;i<max;i++){
    out.push(arr[Math.floor(i*step)]);
  }
  return out;
};

document.addEventListener('DOMContentLoaded', () => {
  // DOM refs
  const btns = Array.from(document.querySelectorAll('.controls .btn'));
  const customDaysInput = document.getElementById('customDays');
  const btnCustom = document.getElementById('btnCustom');
  const dataRangeLabel = document.getElementById('dataRangeLabel');
  const chartCapEl = document.getElementById('chartCap');
  const loadMoreBtn = document.getElementById('loadMoreRecent');

  // charts
  let lineChart = null, barChart = null, pieChart = null;
  const MAX_POINTS = 1000; // cap (adjustable)
  chartCapEl.textContent = MAX_POINTS;

  // initial datasets (server fallback)
  const initialLine = @json($lineChart ?? ['labels'=>[], 'data'=>[]]);
  const initialBar = @json($barChart ?? ['labels'=>[], 'data'=>[]]);
  const initialPie = @json($pieChart ?? ['labels'=>[], 'data'=>[]]);

  // Chart creation helpers
  const createLine = (ctx, labels, data) => {
    return new Chart(ctx, {
      type:'line',
      data: { labels, datasets:[{ label: 'Bookings', data, fill:true, tension:0.25, borderWidth:2, borderColor:'#4f46e5', backgroundColor:'rgba(79,70,229,0.08)', pointRadius:2 }]},
      options: { responsive:true, plugins:{ legend:{ display:false } }, scales:{ y:{ beginAtZero:true, ticks:{ precision:0 } } } }
    });
  };

  const createBar = (ctx, labels, data) => {
    return new Chart(ctx, {
      type:'bar',
      data: { labels, datasets:[{ label:'Jumlah', data, borderRadius:6, backgroundColor: labels.map((_,i)=>['#ef4444','#f59e0b','#10b981','#3b82f6','#8b5cf6','#ec4899','#06b6d4','#f97316'][i % 8]) }]},
      options: { responsive:true, plugins:{ legend:{ display:false } }, scales:{ y:{ beginAtZero:true, ticks:{ precision:0 } } } }
    });
  };

  const createPie = (ctx, labels, data) => {
    return new Chart(ctx, {
      type:'pie',
      data: { labels, datasets:[{ data, backgroundColor:['#f59e0b','#f97316','#10b981','#3b82f6','#ef4444','#9ca3af'] }]},
      options:{ responsive:true }
    });
  };

  // instantiate canvas contexts
  const lineCtx = document.getElementById('lineBookings')?.getContext('2d');
  const barCtx  = document.getElementById('barPackages')?.getContext('2d');
  const pieCtx  = document.getElementById('pieStatus')?.getContext('2d');

  // local function to render using (labels,data) with downsampling
  function renderLine(labels, data) {
    // if too many points -> sample
    if (labels.length > MAX_POINTS) {
      const sampledLabels = sampleArray(labels, MAX_POINTS);
      const sampledData = sampleArray(data, MAX_POINTS);
      labels = sampledLabels; data = sampledData;
    }

    if (lineChart) { lineChart.destroy(); lineChart = null; }
    if (lineCtx) lineChart = createLine(lineCtx, labels, data);
  }

  function renderBar(labels, data) {
    if (barChart) { barChart.destroy(); barChart = null; }
    if (barCtx) barChart = createBar(barCtx, labels, data);
  }

  function renderPie(labels, data) {
    if (pieChart) { pieChart.destroy(); pieChart = null; }
    if (pieCtx) pieChart = createPie(pieCtx, labels, data);
  }

  // Data loader: first try API endpoint '/api/dashboard/stats?days=X'
  // If API fails / not available, fall back to server-provided initial* objects
  async function loadDashboard(days = 30) {
    dataRangeLabel.textContent = `${days} hari`;
    document.querySelectorAll('.controls .btn').forEach(b => b.classList.remove('active'));
    const activeBtn = document.querySelector(`.controls .btn[data-days="${days}"]`);
    if (activeBtn) activeBtn.classList.add('active');

    // attempt fetch
    const apiUrl = `/api/dashboard/stats?days=${encodeURIComponent(days)}`;
    try {
      const res = await fetch(apiUrl, { headers:{ 'Accept':'application/json' }});
      if (res.ok) {
        const payload = await res.json();
        // expected payload shape:
        // { line: {labels:[], data:[]}, bar: {labels:[], data:[]}, pie: {labels:[], data:[]}, stats: {total, today, week, month, revenueToday, ...}, recent: {html or array}, topExtras: [...] }
        const line = payload.line || initialLine;
        const bar = payload.bar || initialBar;
        const pie = payload.pie || initialPie;

        renderLine(line.labels || [], line.data || []);
        renderBar(bar.labels || [], bar.data || []);
        renderPie(pie.labels || [], pie.data || []);

        // update stats if provided
        if (payload.stats) {
          document.getElementById('statTotal').textContent = (payload.stats.totalBookings ?? '{{ $totalBookings ?? 0 }}').toLocaleString();
          document.getElementById('statToday').textContent = (payload.stats.bookingsToday ?? '{{ $bookingsToday ?? 0 }}').toLocaleString();
          document.getElementById('statWeek').textContent = (payload.stats.bookingsThisWeek ?? '{{ $bookingsThisWeek ?? 0 }}').toLocaleString();
          document.getElementById('statMonth').textContent = (payload.stats.bookingsThisMonth ?? '{{ $bookingsThisMonth ?? 0 }}').toLocaleString();
          document.getElementById('revToday').textContent = 'Rp' + (payload.stats.revenueToday ?? {{ $revenueToday ?? 0 }}).toLocaleString('id-ID');
          document.getElementById('revWeek').textContent = 'Rp' + (payload.stats.revenueWeek ?? {{ $revenueWeek ?? 0 }}).toLocaleString('id-ID');
          document.getElementById('revMonth').textContent = 'Rp' + (payload.stats.revenueMonth ?? {{ $revenueMonth ?? 0 }}).toLocaleString('id-ID');
        }

        // if recent bookings returned as html (string), replace container
        if (payload.recent_html) {
          document.getElementById('recentBookingsBody').innerHTML = payload.recent_html;
        }
        // top extras update supported similarly if needed
      } else {
        // fallback to server data
        renderLine(initialLine.labels || [], initialLine.data || []);
        renderBar(initialBar.labels || [], initialBar.data || []);
        renderPie(initialPie.labels || [], initialPie.data || []);
      }
    } catch (err) {
      // network or endpoint missing — fallback to server data
      console.warn('dashboard API not available, using server-side data', err);
      renderLine(initialLine.labels || [], initialLine.data || []);
      renderBar(initialBar.labels || [], initialBar.data || []);
      renderPie(initialPie.labels || [], initialPie.data || []);
    }
  }

  // init charts (use server-side initial values immediately)
  renderLine(initialLine.labels || [], initialLine.data || []);
  renderBar(initialBar.labels || [], initialBar.data || []);
  renderPie(initialPie.labels || [], initialPie.data || []);

  // attach range buttons
  btns.forEach(b => {
    b.addEventListener('click', () => {
      const days = parseInt(b.dataset.days || 30, 10);
      loadDashboard(days);
    });
  });

  btnCustom.addEventListener('click', () => {
    const days = parseInt(customDaysInput.value || 30, 10);
    if (!days || days < 1) return alert('Masukkan jumlah hari valid (1-1000).');
    loadDashboard(Math.min(days, 1000));
  });

  // "Load more" for recent bookings (backend should expose page param)
  let recentPage = 1;
  loadMoreBtn?.addEventListener('click', async () => {
    recentPage++;
    const url = `/api/bookings/recent?page=${recentPage}`;
    try {
      const r = await fetch(url, { headers:{ 'Accept':'application/json' }});
      if (!r.ok) throw new Error('no more');
      const payload = await r.json();
      // payload.html expected: rows <tr>...</tr>
      if (payload.html && payload.html.length) {
        document.getElementById('recentBookingsBody').insertAdjacentHTML('beforeend', payload.html);
      } else {
        loadMoreBtn.disabled = true; loadMoreBtn.textContent = 'No more';
      }
    } catch(e) {
      loadMoreBtn.disabled = true; loadMoreBtn.textContent = 'No more';
    }
  });

  // export CSV button (simple trigger, backend endpoint recommended)
  document.getElementById('exportCsv')?.addEventListener('click', () => {
    // download CSV for current range (server implement /dashboard/export?days=...)
    const daysText = dataRangeLabel.textContent.replace(' hari','') || '30';
    const days = parseInt(daysText, 10) || 30;
    window.location = `/dashboard/export?days=${days}`;
  });

  // initial load: try API with default 30 days
  loadDashboard(30);
});
</script>
@endpush
