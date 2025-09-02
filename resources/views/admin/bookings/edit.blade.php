@extends('layouts.app')
@section('title', 'Edit Booking — Admin')

@push('styles')
<style>
/* Small focused overrides to make UI polished */
:root{
  --brand: #dc2626;
  --muted: #6b7280;
  --card: #ffffff;
  --surface: #f8fafc;
  --accent-100: rgba(220,38,38,0.06);
}
.fade { transition: all .18s ease; }
.package-card { border: 1px solid #e6e9ef; border-radius: 12px; background: var(--card); padding: 14px; cursor: pointer; display:flex; flex-direction:column; gap:8px; }
.package-card:focus { outline: none; box-shadow: 0 8px 30px rgba(59,130,246,0.08); transform: translateY(-3px); }
.package-card.selected { border-color: var(--brand); background: linear-gradient(90deg, var(--accent-100), #fff6f6); box-shadow: 0 14px 40px rgba(220,38,38,0.06); }
.background-option { border: 1px solid #e6e9ef; border-radius: 10px; overflow:hidden; cursor:pointer; }
.background-option.selected { border-color: var(--brand); box-shadow: 0 12px 30px rgba(220,38,38,0.06); transform: translateY(-4px); }
.segmented button { padding:8px 12px; border-radius:8px; border:1px solid #e6e9ef; background:#fff; cursor:pointer; }
.segmented button.active { background: linear-gradient(90deg,#fee2e2,#fff6f6); border-color:#fca5a5; color:#7f1d1d; }
.small-muted { font-size:0.86rem; color:var(--muted); }
.kv { font-weight:600; color:#0f172a; }
.sticky-right { position: sticky; top:22px; }
.qty-input { width:74px; }
.badge { font-size:0.78rem; background:#f1f5f9; padding:4px 8px; border-radius:999px; color:#0f172a; }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
  <div class="flex items-start justify-between mb-6 gap-4">
    <div>
      <h1 class="text-2xl font-semibold">Edit Booking #{{ $booking->id }}</h1>
      <p class="text-sm text-gray-500 mt-1">Desain dipoles: fokus ke kecepatan kerja admin & visibilitas info. Editing hanya untuk status <strong>booked</strong>.</p>
    </div>
    <div class="flex items-center gap-3">
      <a href="{{ route('bookings.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-md text-sm">← Kembali</a>
    </div>
  </div>

  @if($booking->status !== \App\Models\Booking::STATUS_BOOKED)
    <div class="bg-white p-6 rounded-lg shadow">
      <p class="text-sm text-gray-700">Booking berstatus <strong>{{ $booking->status }}</strong>. Editing tidak diperbolehkan lewat halaman ini. Gunakan view detail jika perlu tindakan lain.</p>
      <div class="mt-4">
        <a href="{{ route('bookings.show', $booking->id) }}" class="px-4 py-2 rounded-md border">Lihat detail</a>
      </div>
    </div>
  @else

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- MAIN FORM (left 2/3) --}}
    <form id="mainForm" action="{{ route('bookings.update', $booking->id) }}" method="POST" enctype="multipart/form-data" class="lg:col-span-2 space-y-6">
      @csrf @method('PUT')

      {{-- Customer + Contact --}}
      <div class="bg-white p-6 rounded-lg shadow">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Customer</label>
            <select name="customer_id" class="mt-2 block w-full rounded-md border-slate-200" required>
              <option value="">-- Pilih Customer --</option>
              @foreach($customers as $c)
                <option value="{{ $c->id }}" {{ old('customer_id', $booking->customer_id) == $c->id ? 'selected' : '' }}>
                  {{ $c->name }} — {{ $c->email }}
                </option>
              @endforeach
            </select>
            @error('customer_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Nama Kontak</label>
            <input name="contact_name" value="{{ old('contact_name', $booking->contact_name) }}" maxlength="100" required class="mt-2 block w-full rounded-md border-slate-200">
            @error('contact_name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Nomor WhatsApp</label>
            <input name="whatsapp_number" value="{{ old('whatsapp_number', $booking->whatsapp_number) }}" maxlength="25" required class="mt-2 block w-full rounded-md border-slate-200">
            <p class="text-xs text-gray-400 mt-1">Format: 0812... atau +628...</p>
            @error('whatsapp_number') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Tanggal Pemotretan</label>
            <input id="booking_date" name="booking_date" type="date" min="{{ now()->format('Y-m-d') }}" value="{{ old('booking_date', $booking->booking_date) }}" required class="mt-2 block w-full rounded-md border-slate-200">
            @error('booking_date') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Waktu Pemotretan</label>
            <select id="booking_time" name="booking_time" required class="mt-2 block w-full rounded-md border-slate-200">
              <option value="{{ old('booking_time', $booking->booking_time) }}">{{ old('booking_time', $booking->booking_time) }} WIB</option>
            </select>
            <div id="time-message" class="text-xs text-gray-500 mt-1">Memuat status slot...</div>
            @error('booking_time') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Metode Pembayaran</label>
            <select id="payment_method" name="payment_method" required class="mt-2 block w-full rounded-md border-slate-200">
              <option value="cash" {{ old('payment_method', $booking->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
              <option value="transfer" {{ old('payment_method', $booking->payment_method) == 'transfer' ? 'selected' : '' }}>Transfer</option>
            </select>

            <div id="proofArea" class="mt-3 {{ old('payment_method', $booking->payment_method) !== 'transfer' ? 'hidden' : '' }}">
              <label class="block text-sm font-medium text-gray-700">Upload Bukti Transfer</label>
              <input type="file" name="payment_proof" accept="image/*" class="mt-2 block w-full">
              @if($booking->payment_proof && $booking->payment_method === 'transfer')
                <a href="{{ asset('storage/' . $booking->payment_proof) }}" target="_blank" class="text-sm text-brand mt-2 inline-block" style="color:var(--brand)">Lihat bukti sekarang</a>
              @endif
            </div>
          </div>
        </div>
      </div>

      {{-- Paket --}}
      <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center justify-between">
          <h2 class="text-lg font-medium">Pilih Paket</h2>
          <div class="text-sm text-gray-500">Klik / tekan Enter untuk memilih</div>
        </div>

        <div id="packageGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
          @php
            $packages = [
              ['name'=>'Baby Smash Cake','price'=>550000,'bg'=>0,'cat'=>'baby-smash'],
              ['name'=>'Plain','price'=>300000,'bg'=>1,'cat'=>'plain'],
              ['name'=>'Grande','price'=>500000,'bg'=>2,'cat'=>'grande'],
              ['name'=>'Royal','price'=>700000,'bg'=>4,'cat'=>'royal'],
              ['name'=>'Prewed I','price'=>700000,'bg'=>2,'cat'=>'pre-wedding'],
              ['name'=>'Prewed II','price'=>1000000,'bg'=>3,'cat'=>'pre-wedding'],
            ];
          @endphp

          @foreach($packages as $p)
            <div tabindex="0" role="button" aria-pressed="{{ (old('package_name', $booking->package_name) === $p['name']) ? 'true' : 'false' }}"
                 class="package-card fade {{ old('package_name', $booking->package_name) === $p['name'] ? 'selected' : '' }}"
                 data-package="{{ $p['name'] }}" data-price="{{ $p['price'] }}" data-bg="{{ $p['bg'] }}" data-cat="{{ $p['cat'] }}">
              <div class="flex items-center justify-between">
                <div class="text-lg font-semibold">{{ $p['name'] }}</div>
                <div class="text-red-600 font-bold">IDR {{ number_format($p['price'],0,',','.') }}</div>
              </div>
              <div class="text-sm text-gray-500">Max background: <span class="kv">{{ $p['bg'] }}</span></div>
              <div class="text-xs text-gray-400 mt-1">Klik untuk memilih paket ini</div>
            </div>
          @endforeach
        </div>

        <input type="hidden" name="package_name" id="package_name" value="{{ old('package_name', $booking->package_name) }}">
        @error('package_name') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror

        {{-- Session segmented control --}}
        <div id="sessionWrap" class="mt-4 hidden">
          <label class="text-sm font-medium text-gray-700">Jenis Sesi (Plain / Grande / Royal)</label>
          <div class="segmented inline-flex gap-2 mt-2">
            <button type="button" data-val="family">Family</button>
            <button type="button" data-val="graduation">Graduation</button>
            <button type="button" data-val="maternity">Maternity</button>
          </div>
          <input type="hidden" id="session_name" name="session_name" value="{{ old('session_name', $booking->session_name) }}">
          <p class="text-xs text-gray-400 mt-2">Wajib dipilih untuk paket Group (Plain, Grande, Royal).</p>
        </div>
      </div>

      {{-- Backgrounds: search + grid --}}
      <div id="bgSection" class="bg-white p-6 rounded-lg shadow hidden">
        <div class="flex items-center justify-between gap-4">
          <h3 class="text-lg font-medium">Pilih Background</h3>
          <div class="flex items-center gap-2">
            <input id="bgSearch" placeholder="Cari background..." class="rounded-md border-slate-200 px-3 py-2 text-sm" />
            <span class="badge" id="bgCounterBadge">0</span>
          </div>
        </div>

        <div id="bgGrid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 mt-4" aria-live="polite">
          {{-- JS akan render tiles --}}
        </div>

        <input type="hidden" name="selected_backgrounds" id="selected_backgrounds" value='{{ json_encode($selectedBackgrounds ?? []) }}'>
        <p class="text-xs text-gray-400 mt-3">* Pilih maksimal sesuai paket. Gunakan search untuk cepat menemukan background.</p>
        @error('selected_backgrounds') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
      </div>

      {{-- Extras with quantity & subtotal calculation --}}
      <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-medium">Extra Items</h3>
          <div class="text-sm text-gray-500">Pilih dan atur jumlah</div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
          <div>
            <h4 class="text-sm font-semibold">Cetak Foto</h4>
            <div class="mt-3 space-y-3">
              @foreach($printItems as $it)
                <div class="flex items-center justify-between gap-3">
                  <label class="flex items-center gap-3">
                    <input type="checkbox" class="extra-checkbox" data-id="{{ $it->id }}" data-price="{{ $it->price }}" name="selected_extra_items[]" value="{{ $it->id }}" @if(in_array($it->id, (array)$selectedExtraItems)) checked @endif>
                    <div>
                      <div class="text-sm font-medium">{{ $it->name }}</div>
                      <div class="text-xs text-gray-400">IDR {{ number_format($it->price,0,',','.') }}</div>
                    </div>
                  </label>
                  <input type="number" min="1" value="{{ (in_array($it->id, (array)$selectedExtraItems) ? 1 : 1) }}" class="qty-input rounded-md border-slate-200 p-1 text-sm" data-extra-id="{{ $it->id }}" name="extra_qty[{{ $it->id }}]">
                </div>
              @endforeach
            </div>
          </div>

          <div>
            <h4 class="text-sm font-semibold">Frame Foto</h4>
            <div class="mt-3 space-y-3">
              @foreach($frameItems as $it)
                <div class="flex items-center justify-between gap-3">
                  <label class="flex items-center gap-3">
                    <input type="checkbox" class="extra-checkbox" data-id="{{ $it->id }}" data-price="{{ $it->price }}" name="selected_extra_items[]" value="{{ $it->id }}" @if(in_array($it->id, (array)$selectedExtraItems)) checked @endif>
                    <div>
                      <div class="text-sm font-medium">{{ $it->name }}</div>
                      <div class="text-xs text-gray-400">IDR {{ number_format($it->price,0,',','.') }}</div>
                    </div>
                  </label>
                  <input type="number" min="1" value="1" class="qty-input rounded-md border-slate-200 p-1 text-sm" data-extra-id="{{ $it->id }}" name="extra_qty[{{ $it->id }}]">
                </div>
              @endforeach
            </div>
          </div>

          <div>
            <h4 class="text-sm font-semibold">Tambahan & Layanan</h4>
            <div class="mt-3 space-y-3">
              @foreach($serviceItems as $it)
                <div class="flex items-center justify-between gap-3">
                  <label class="flex items-center gap-3">
                    <input type="checkbox" class="extra-checkbox" data-id="{{ $it->id }}" data-price="{{ $it->price }}" name="selected_extra_items[]" value="{{ $it->id }}" @if(in_array($it->id, (array)$selectedExtraItems)) checked @endif>
                    <div>
                      <div class="text-sm font-medium">{{ $it->name }}</div>
                      <div class="text-xs text-gray-400">IDR {{ number_format($it->price,0,',','.') }}</div>
                    </div>
                  </label>
                  <input type="number" min="1" value="1" class="qty-input rounded-md border-slate-200 p-1 text-sm" data-extra-id="{{ $it->id }}" name="extra_qty[{{ $it->id }}]">
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>

      {{-- Baby info & notes --}}
      <div class="bg-white p-6 rounded-lg shadow grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Nama Bayi (opsional)</label>
          <input name="baby_name" value="{{ old('baby_name', $booking->baby_name) }}" class="mt-2 block w-full rounded-md border-slate-200" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Usia Bayi (opsional)</label>
          <input name="baby_age" value="{{ old('baby_age', $booking->baby_age) }}" class="mt-2 block w-full rounded-md border-slate-200" />
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700">Catatan Tambahan</label>
          <textarea name="notes" rows="3" class="mt-2 block w-full rounded-md border-slate-200">{{ old('notes', $booking->notes) }}</textarea>
        </div>
      </div>

      {{-- invisible submit hook (we use sidebar button) --}}
      <div class="text-right lg:text-left mt-2">
        <p class="text-xs text-gray-400">Tekan "Simpan" di panel kanan untuk menyimpan perubahan.</p>
      </div>
    </form>

    {{-- RIGHT SIDEBAR (summary & actions) --}}
    <aside class="lg:col-span-1">
      <div class="bg-white p-6 rounded-lg shadow sticky-right">
        <h3 class="text-lg font-semibold">Ringkasan & Aksi</h3>

        <div class="mt-4 space-y-3">
          <div class="flex items-center justify-between">
            <div class="text-sm text-gray-500">Paket</div>
            <div id="summaryPackage" class="kv">{{ $booking->package_name }}</div>
          </div>
          <div class="flex items-center justify-between">
            <div class="text-sm text-gray-500">Sesi</div>
            <div id="summarySession" class="kv">{{ $booking->session_name ?? '-' }}</div>
          </div>
          <div class="flex items-center justify-between">
            <div class="text-sm text-gray-500">Tanggal</div>
            <div id="summaryDate" class="kv">{{ $booking->booking_date }}</div>
          </div>
          <div class="flex items-center justify-between">
            <div class="text-sm text-gray-500">Waktu</div>
            <div id="summaryTime" class="kv">{{ $booking->booking_time }}</div>
          </div>

          <div class="border-t pt-3">
            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-500">Harga Paket</div>
              <div id="packagePrice" class="kv">IDR {{ number_format($booking->total_price ?? 0,0,',','.') }}</div>
            </div>
            <div class="mt-2 text-sm text-gray-500">Extras</div>
            <div id="extrasBreakdown" class="mt-2 text-sm text-gray-700"></div>

            <div class="mt-4 flex items-center justify-between">
              <div class="text-sm font-medium">Total</div>
              <div id="grandTotal" class="text-2xl font-bold text-red-600">IDR {{ number_format($booking->total_price ?? 0,0,',','.') }}</div>
            </div>
          </div>

          <div class="mt-4">
            <button id="submitBtn" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md bg-red-600 text-white font-semibold hover:bg-red-700">💾 Simpan Perubahan</button>
            <a href="{{ route('bookings.show', $booking->id) }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 mt-2 rounded-md border text-sm">Batal</a>
          </div>
        </div>

      </div>

      <div class="bg-white p-4 rounded-lg shadow mt-4 text-sm text-gray-500">
        <div>Dibuat: {{ $booking->created_at->format('d M Y H:i') }}</div>
        <div class="mt-2">ID Booking: #{{ $booking->id }}</div>
        <div class="mt-2">Status: <span class="badge">{{ $booking->status }}</span></div>
      </div>
    </aside>
  </div>

  {{-- Image preview modal --}}
  <div id="previewModal" class="fixed inset-0 z-50 hidden items-center justify-center">
    <div class="absolute inset-0 bg-black/60" tabindex="-1" data-modal-backdrop></div>
    <div class="relative z-10 max-w-3xl w-full mx-4">
      <button id="closePreview" class="absolute right-2 top-2 bg-white rounded-full p-1 shadow">✕</button>
      <img id="previewImg" src="" alt="Preview" class="rounded-md w-full object-contain max-h-[80vh]">
    </div>
  </div>
  @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){

  // --- Helpers ---
  const idr = n => 'IDR ' + new Intl.NumberFormat('id-ID').format(n || 0);
  const $ = s => document.querySelector(s);
  const $$ = s => Array.from(document.querySelectorAll(s));

  // --- Elements ---
  const mainForm = $('#mainForm');
  const submitBtn = $('#submitBtn');
  const packageGrid = document.getElementById('packageGrid');
  const packageInput = $('#package_name');
  const sessionWrap = $('#sessionWrap');
  const sessionHidden = $('#session_name');
  const sessionButtons = sessionWrap ? sessionWrap.querySelectorAll('button') : [];
  const bgSection = $('#bgSection');
  const bgGrid = $('#bgGrid');
  const bgSearch = $('#bgSearch');
  const selectedBgInput = $('#selected_backgrounds');
  const bgCounterBadge = $('#bgCounterBadge');
  const bookingDate = $('#booking_date');
  const bookingTime = $('#booking_time');
  const timeMsg = $('#time-message');
  const paymentMethod = $('#payment_method');
  const proofArea = $('#proofArea');
  const summaryPackage = $('#summaryPackage');
  const summarySession = $('#summarySession');
  const summaryDate = $('#summaryDate');
  const summaryTime = $('#summaryTime');
  const extrasBreakdown = $('#extrasBreakdown');
  const grandTotal = $('#grandTotal');
  const packagePriceEl = $('#packagePrice');
  const qtyInputs = () => $$('.qty-input');
  const extraCheckboxes = $$('.extra-checkbox');

  // data maps (from controller)
  const maps = {
    'baby-smash': @json($babySmashBackgrounds ?? []),
    'plain': @json($plainBackgrounds ?? []),
    'grande': @json($grandeBackgrounds ?? []),
    'royal': @json($royalBackgrounds ?? []),
    'pre-wedding': @json($prewedBackgrounds ?? []),
    'family': @json($familyBackgrounds ?? []),
    'graduation': @json($graduationBackgrounds ?? []),
    'maternity': @json($maternityBackgrounds ?? []),
    'all': @json($backgroundItems ?? []),
  };

  // initial state from server
  let selectedBackgrounds = Array.isArray(@json($selectedBackgrounds ?? [])) ? @json($selectedBackgrounds ?? []).map(String) : [];
  let selectedExtras = Array.from(document.querySelectorAll('.extra-checkbox:checked')).map(cb => ({ id: cb.dataset.id, price: Number(cb.dataset.price || 0), qty: 1 }));
  // initialize qtys from DOM if present
  qtyInputs().forEach(q => {
    const id = q.dataset.extraId;
    const cb = document.querySelector('.extra-checkbox[data-id="'+id+'"]');
    if (cb && cb.checked) {
      const ex = selectedExtras.find(e => e.id === id);
      if (ex) ex.qty = Number(q.value || 1);
    }
  });

  let currentPkg = packageInput.value || '{{ $booking->package_name }}';
  let currentCat = (packageGrid.querySelector('.selected') || {}).dataset?.cat || '';
  let currentMaxBg = parseInt((packageGrid.querySelector('.selected') || {}).dataset?.bg || 0) || 0;

  // utility: render backgrounds list
  function renderBackgroundTiles(list) {
    bgGrid.innerHTML = '';
    if (!Array.isArray(list) || list.length === 0) {
      bgGrid.innerHTML = `<div class="text-sm text-gray-400">Belum ada background untuk kategori ini.</div>`;
      return;
    }
    list.forEach(bg => {
      const id = String(bg.id);
      const selected = selectedBackgrounds.includes(id);
      const card = document.createElement('div');
      card.className = 'background-option p-0 fade ' + (selected ? 'selected' : '');
      card.tabIndex = 0;
      card.innerHTML = `
        ${ bg.image ? `<img src="{{ asset('storage') }}/${bg.image}" class="w-full h-40 object-cover">` : `<div class="w-full h-40 bg-slate-100"></div>` }
        <div class="p-2"><div class="font-medium text-sm">${bg.name || 'Background'}</div></div>
      `;
      card.addEventListener('click', () => toggleBg(id, card));
      card.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); toggleBg(id, card); } });

      // image preview
      const imgEl = card.querySelector('img');
      if (imgEl) {
        imgEl.style.cursor = 'zoom-in';
        imgEl.addEventListener('click', ev => { ev.stopPropagation(); openPreview(`{{ asset('storage') }}/${bg.image}`); });
      }
      bgGrid.appendChild(card);
    });
    updateBgCounter();
  }

  function toggleBg(id, el) {
    const idx = selectedBackgrounds.indexOf(id);
    if (idx > -1) {
      selectedBackgrounds.splice(idx,1);
      el.classList.remove('selected');
    } else {
      if (currentMaxBg > 0 && selectedBackgrounds.length >= currentMaxBg) {
        alert(`Paket hanya boleh memilih maksimal ${currentMaxBg} background.`);
        return;
      }
      selectedBackgrounds.push(id);
      el.classList.add('selected');
    }
    updateBgCounter();
    pushSelectedBg();
  }

  function updateBgCounter(){
    bgCounterBadge.textContent = `${selectedBackgrounds.length}${currentMaxBg ? ' / '+currentMaxBg : ''}`;
  }

  function pushSelectedBg(){
    const normalized = selectedBackgrounds.map(s => isNaN(s) ? s : Number(s));
    selectedBgInput.value = JSON.stringify(normalized);
  }

  // package selection handling
  $$('.package-card').forEach(card => {
    card.addEventListener('click', () => {
      $$('.package-card').forEach(c => c.classList.remove('selected'));
      card.classList.add('selected');
      const pkg = card.dataset.package;
      const bg = Number(card.dataset.bg || 0);
      const cat = card.dataset.cat || '';
      packageInput.value = pkg;
      currentPkg = pkg;
      currentCat = cat;
      currentMaxBg = bg;
      summaryPackage.textContent = pkg;
      packagePriceEl.textContent = idr(Number(card.dataset.price || 0));
      // session control only for group packages
      if (['plain','grande','royal'].includes((cat || '').toLowerCase())) {
        sessionWrap.classList.remove('hidden');
      } else {
        sessionWrap.classList.add('hidden');
        sessionHidden.value = '';
        summarySession.textContent = '-';
      }

      // show bg section according to max bg
      if (currentMaxBg === 0) {
        bgSection.classList.add('hidden');
        selectedBackgrounds = [];
        pushSelectedBg();
      } else {
        bgSection.classList.remove('hidden');
        // render backgrounds for category (union with session if session active)
        const sessVal = sessionHidden.value || '';
        let arr = maps[cat] || maps['all'];
        if (sessVal) {
          const sessArr = maps[sessVal] || [];
          // union
          const merged = [...arr, ...sessArr].reduce((acc, cur) => { if (!acc.find(x=>String(x.id) === String(cur.id))) acc.push(cur); return acc; }, []);
          arr = merged;
        }
        renderBackgroundTiles(arr);
      }
      updateTotals();
    });
    card.addEventListener('keydown', (e)=>{ if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); card.click(); } });
  });

  // session segments
  sessionButtons.forEach(btn=>{
    btn.addEventListener('click', ()=>{
      sessionButtons.forEach(b=>b.classList.remove('active'));
      btn.classList.add('active');
      const val = btn.dataset.val;
      sessionHidden.value = val;
      summarySession.textContent = btn.textContent;
      // re-render backgrounds with union
      const cat = currentCat || 'all';
      const arr = (maps[cat] || []).concat(maps[val] || []);
      const merged = arr.reduce((acc, cur) => { if (!acc.find(x=>String(x.id) === String(cur.id))) acc.push(cur); return acc; }, []);
      renderBackgroundTiles(merged);
    });
  });

  // bg search
  if (bgSearch) {
    bgSearch.addEventListener('input', function(){
      const q = this.value.trim().toLowerCase();
      const cat = currentCat || 'all';
      let arr = maps[cat] || maps['all'];
      if (sessionHidden.value) arr = [...arr, ...(maps[sessionHidden.value]||[])].reduce((acc, cur) => { if (!acc.find(x=>String(x.id) === String(cur.id))) acc.push(cur); return acc; }, []);
      if (q) {
        arr = arr.filter(b => (b.name || '').toLowerCase().includes(q));
      }
      renderBackgroundTiles(arr);
    });
  }

  // extras handling: checkbox + qty
  function updateSelectedExtrasFromDom(){
    selectedExtras = [];
    $$('.extra-checkbox').forEach(cb=>{
      const id = cb.dataset.id;
      const price = Number(cb.dataset.price || 0);
      const qty = Number(document.querySelector(`input[data-extra-id="${id}"]`)?.value || 1);
      if (cb.checked) selectedExtras.push({ id, price, qty });
    });
    renderExtrasBreakdown();
    updateTotals();
  }
  $$('.extra-checkbox').forEach(cb => cb.addEventListener('change', updateSelectedExtrasFromDom));
  qtyInputs().forEach(q => q.addEventListener('input', updateSelectedExtrasFromDom));

  // initial extras
  updateSelectedExtrasFromDom();

  function renderExtrasBreakdown(){
    extrasBreakdown.innerHTML = '';
    if (selectedExtras.length === 0) {
      extrasBreakdown.innerHTML = '<div class="text-xs text-gray-400">Tidak ada extras.</div>';
      return;
    }
    selectedExtras.forEach(e => {
      const div = document.createElement('div');
      div.className = 'flex items-center justify-between text-sm';
      div.innerHTML = `<div>${e.id} × ${e.qty}</div><div>${idr(e.price * e.qty)}</div>`;
      extrasBreakdown.appendChild(div);
    });
  }

  // totals calculation: package price + extras
  function updateTotals(){
    const pkgBtn = $$('.package-card').find(c => c.classList.contains('selected')) || null;
    const pkgPrice = pkgBtn ? Number(pkgBtn.dataset.price || 0) : 0;
    const extrasTotal = selectedExtras.reduce((s, ex) => s + (ex.price * (ex.qty || 1)), 0);
    packagePriceEl.textContent = idr(pkgPrice);
    grandTotal.textContent = idr(pkgPrice + extrasTotal);
  }

  // booking times fetch
  async function fetchTimes(date){
    if (!date) return;
    bookingTime.disabled = true;
    bookingTime.innerHTML = '<option>Memuat…</option>';
    timeMsg.textContent = '';
    try {
      const res = await fetch(`/api/available-times?booking_date=${encodeURIComponent(date)}`);
      if (!res.ok) throw new Error('fail');
      const data = await res.json();
      const times = data.available_times || [];
      bookingTime.innerHTML = '';
      if (!times.length) {
        bookingTime.innerHTML = '<option value="">Hari ini penuh</option>';
        bookingTime.disabled = true;
        timeMsg.textContent = 'Tidak ada slot pada tanggal ini.';
      } else {
        const placeholder = document.createElement('option'); placeholder.value=''; placeholder.text = '-- Pilih Waktu --';
        bookingTime.appendChild(placeholder);
        times.forEach(t => { const o = document.createElement('option'); o.value = t; o.text = `${t} WIB`; bookingTime.appendChild(o); });
        // restore old/current booking time if present
        const oldTime = "{{ old('booking_time', $booking->booking_time) }}";
        if (oldTime && times.includes(oldTime)) bookingTime.value = oldTime;
        else {
          const cur = @json($booking->booking_time);
          if (cur && !times.includes(cur)) {
            const opt = document.createElement('option'); opt.value = cur; opt.text = `${cur} WIB (current)`; bookingTime.prepend(opt);
            bookingTime.value = cur;
          }
        }
        bookingTime.disabled = false;
        timeMsg.textContent = `Ada ${times.length} slot tersedia.`;
      }
    } catch (e) {
      bookingTime.innerHTML = '<option value="">Gagal muat</option>';
      bookingTime.disabled = true;
      timeMsg.textContent = 'Gagal memuat slot waktu.';
    }
  }

  bookingDate.addEventListener('change', e => {
    summaryDate.textContent = e.target.value || '-';
    fetchTimes(e.target.value);
  });

  bookingTime.addEventListener('change', e => summaryTime.textContent = e.target.value || '-');

  // init fetch if date exists
  if (bookingDate.value) fetchTimes(bookingDate.value);

  // payment method toggle
  paymentMethod.addEventListener('change', () => proofArea.classList.toggle('hidden', paymentMethod.value !== 'transfer'));

  // preview modal
  const previewModal = $('#previewModal');
  const previewImg = $('#previewImg');
  const closePreview = $('#closePreview');
  if (closePreview) closePreview.addEventListener('click', ()=> { previewModal.classList.add('hidden'); previewImg.src = ''; });
  function openPreview(src){ previewImg.src = src; previewModal.classList.remove('hidden'); }

  // submit handling with client-side validation
  submitBtn.addEventListener('click', (ev) => {
    // require package
    if (!packageInput.value) { alert('Silakan pilih paket.'); return; }
    // if group package require session
    if (['plain','grande','royal'].includes((currentCat||'').toLowerCase()) && !sessionHidden.value) { alert('Silakan pilih jenis sesi (Family/Graduation/Maternity).'); return; }
    // background requirement
    if (currentMaxBg > 0 && selectedBackgrounds.length === 0) { alert('Pilih minimal 1 background sesuai paket.'); return; }
    // ensure booking time selected if changed (server will enforce anyway)
    if (!bookingTime.value) { if (!confirm('Waktu belum dipilih. Lanjutkan?')) return; }
    // ensure push selections
    pushSelectedBg();
    updateSelectedExtrasFromDom();
    // submit main form
    mainForm.requestSubmit();
  });

  // initial UI population
  (function init(){
    // mark selected package button if server had one
    const serverPkg = packageInput.value || '{{ $booking->package_name }}';
    $$('.package-card').forEach(c => {
      if ((c.dataset.package||'').toLowerCase() === (serverPkg||'').toLowerCase()) {
        c.classList.add('selected');
        currentCat = c.dataset.cat || currentCat;
        currentMaxBg = Number(c.dataset.bg || currentMaxBg);
      }
    });

    // show session (if server had it)
    const serverSess = "{{ old('session_name', $booking->session_name ?? '') }}";
    if (['plain','grande','royal'].includes((currentCat||'').toLowerCase())) {
      sessionWrap.classList.remove('hidden');
      if (serverSess) {
        sessionButtons.forEach(b => { if (b.dataset.val === serverSess) b.classList.add('active'); });
        sessionHidden.value = serverSess;
        summarySession.textContent = serverSess;
      }
    }

    // show background section if needed and render initial backgrounds
    if (currentMaxBg === 0) {
      bgSection.classList.add('hidden');
    } else {
      bgSection.classList.remove('hidden');
      // union if session selected
      let arr = maps[currentCat] || maps['all'];
      if (sessionHidden.value) {
        arr = [...arr, ...(maps[sessionHidden.value]||[])].reduce((acc, cur) => { if (!acc.find(x=>String(x.id)===String(cur.id))) acc.push(cur); return acc; }, []);
      }
      renderBackgroundTiles(arr);
    }

    // set initial selected background tiles class after render (renderBackgroundTiles handles selectedBackgrounds)
    updateBgCounter();

    // show initial summary values
    summaryPackage.textContent = packageInput.value || '{{ $booking->package_name }}';
    summaryDate.textContent = '{{ $booking->booking_date }}';
    summaryTime.textContent = '{{ $booking->booking_time }}';
    updateTotals();
    renderExtrasBreakdown();
  })();

});
</script>
@endpush
