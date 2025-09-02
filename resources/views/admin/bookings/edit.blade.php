@extends('layouts.app')

@section('title', 'Edit Booking - Admin')

@push('styles')
    <style>
        /* Use same aesthetic as create page for 1:1 consistency */
        @import url('https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap');

        :root{
            --red: #dc2626;
            --muted: #6b7280;
            --card-bg: #f9fafb;
        }

        body { font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; color:#111827; }

        .container { max-width: 1100px; margin: 24px auto; padding: 20px; }
        .card { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 6px 24px rgba(0,0,0,0.04); border:1px solid #ececec; }

        .header-row { display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:18px; }
        .title { font-size:1.25rem; font-weight:700; color:#0f172a; }
        .subtitle { color:var(--muted); font-size:0.95rem; }

        /* Form fields like create */
        .form-grid { display:grid; gap:14px; }
        .row-2 { display:grid; grid-template-columns: 1fr 1fr; gap:14px; }
        .input, select, textarea { width:100%; padding:12px 14px; border-radius:10px; border:1px solid #d1d5db; background:#fff; font-size:0.95rem; }
        .input:focus, select:focus, textarea:focus { outline:none; border-color: var(--red); box-shadow: 0 0 0 6px rgba(220,38,38,0.06); }

        /* package cards — mimic create */
        .packages-grid { display:grid; grid-template-columns: repeat(auto-fill,minmax(220px,1fr)); gap:14px; margin-top:8px; }
        .package-card { border-radius:12px; padding:16px; border:2px solid #e5e7eb; background:var(--card-bg); cursor:pointer; transition:all .18s; }
        .package-card:hover { transform:translateY(-4px); border-color:var(--red); box-shadow:0 10px 25px rgba(0,0,0,0.06); }
        .package-card.selected { border-color:var(--red); background:#fff6f6; box-shadow:0 12px 30px rgba(220,38,38,0.06); }
        .package-title { font-family:'Dancing Script', cursive; font-size:1.15rem; font-weight:600; }
        .package-price { color:var(--red); font-weight:700; margin-top:6px; }

        /* background grid consistent with create */
        .bg-grid { display:grid; grid-template-columns: repeat(auto-fill,minmax(160px,1fr)); gap:12px; margin-top:10px; }
        .background-option { border-radius:10px; overflow:hidden; border:2px solid #e5e7eb; background:#fff; cursor:pointer; transition:all .18s; }
        .background-option img { width:100%; height:120px; object-fit:cover; display:block; }
        .background-option .meta { padding:8px; background:#f8fafc; }
        .background-option.selected { border-color:var(--red); background:#fff6f6; box-shadow:0 10px 25px rgba(220,38,38,0.06); }

        /* extras */
        .extras-grid { display:grid; grid-template-columns: repeat(auto-fit,minmax(200px,1fr)); gap:10px; margin-top:8px; }
        .extra-item { display:flex; gap:10px; align-items:center; padding:10px; border-radius:10px; border:1px solid #f3f4f6; background:#fff; }

        .availability-info { margin-top:8px; padding:10px; border-radius:8px; font-size:0.95rem; display:none; }
        .availability-info.available { display:block; background: linear-gradient(135deg, rgba(59,130,246,0.06), rgba(59,130,246,0.02)); border:1px solid #93c5fd; color:#0f172a; }
        .availability-info.limited { display:block; background: linear-gradient(135deg, rgba(250,204,21,0.06), rgba(250,204,21,0.02)); border:1px solid #facc15; color:#92400e; }
        .availability-info.full { display:block; background: linear-gradient(135deg, rgba(239,68,68,0.06), rgba(239,68,68,0.02)); border:1px solid #fecaca; color:#7f1d1d; }

        .actions { margin-top:12px; display:flex; gap:10px; align-items:center; }
        .btn { padding:10px 14px; border-radius:10px; font-weight:700; cursor:pointer; border:none; }
        .btn-primary { background:var(--red); color:#fff; box-shadow:0 8px 30px rgba(220,38,38,0.08); }
        .btn-ghost { background:#f3f4f6; color:#111827; }

        .small { font-size:0.88rem; color:var(--muted); }

        @media (min-width:1024px){
            .layout { display:grid; grid-template-columns: 2fr 1fr; gap:18px; align-items:start; }
        }
    </style>
@endpush

@section('content')
<div class="container">
    <div class="header-row">
        <div>
            <div class="title">Edit Booking #{{ $booking->id }}</div>
            <div class="subtitle">Sama persis dengan tampilan Tambah Booking — dioptimalkan agar konsisten. Editing hanya diizinkan untuk status <strong>booked</strong>.</div>
        </div>

        <div>
            <a href="{{ route('bookings.index') }}" class="btn btn-ghost">Kembali</a>
        </div>
    </div>

    {{-- Jika status bukan booked, tampilkan notice dan jangan tampilkan form --}}
    @if($booking->status !== 'booked')
        <div class="card">
            <p class="small">Booking ini berstatus <strong>{{ $booking->status }}</strong>. Untuk keamanan, editing lewat halaman ini tidak diperbolehkan.</p>
            <div style="margin-top:12px;">
                <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-ghost">Lihat Detail</a>
            </div>
        </div>
    @else

    <div class="layout">
        {{-- LEFT: form (mirip create) --}}
        <form action="{{ route('bookings.update', $booking->id) }}" method="POST" enctype="multipart/form-data" class="card" id="adminBookingForm">
            @csrf
            @method('PUT')

            <div class="form-grid">
                {{-- customer --}}
                <div class="row-2">
                    <div>
                        <label class="small">Pilih Customer</label>
                        <select name="customer_id" class="input" required>
                            <option value="">-- Pilih Customer --</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" {{ old('customer_id', $booking->customer_id) == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }} ({{ $c->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id') <div class="small" style="color:#b91c1c">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="small">Nama Kontak</label>
                        <input type="text" name="contact_name" class="input" required maxlength="100" value="{{ old('contact_name', $booking->contact_name) }}">
                        @error('contact_name') <div class="small" style="color:#b91c1c">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- whatsapp & session (session hidden input + display) --}}
                <div class="row-2">
                    <div>
                        <label class="small">Nomor WhatsApp</label>
                        <input type="text" name="whatsapp_number" class="input" required maxlength="20" value="{{ old('whatsapp_number', $booking->whatsapp_number) }}">
                        <div class="small">Contoh: 081234567890 atau +6281234567890</div>
                        @error('whatsapp_number') <div class="small" style="color:#b91c1c">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="small">Nama Sesi</label>
                        {{-- hidden authoritative field --}}
                        <input type="hidden" id="session_name" name="session_name" value="{{ old('session_name', $booking->session_name) }}">
                        {{-- visible label for UX --}}
                        <div class="input" id="sessionNameDisplay" style="background:#f8fafc; cursor:default;">{{ old('session_name', $booking->session_name) ?? '-' }}</div>
                    </div>
                </div>

                {{-- date & time --}}
                <div class="row-2">
                    <div>
                        <label class="small">Tanggal Pemotretan</label>
                        <input type="date" id="booking_date" name="booking_date" class="input" min="{{ now()->format('Y-m-d') }}" required value="{{ old('booking_date', $booking->booking_date) }}">
                        @error('booking_date') <div class="small" style="color:#b91c1c">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="small">Waktu Pemotretan</label>
                        {{-- ensure current booking time present so JS comparisons work --}}
                        <select id="booking_time" name="booking_time" class="input" required>
                            @if(old('booking_time', $booking->booking_time))
                                <option value="{{ old('booking_time', $booking->booking_time) }}">{{ old('booking_time', $booking->booking_time) }} WIB</option>
                            @else
                                <option value="">{{ 'Pilih tanggal terlebih dahulu' }}</option>
                            @endif
                        </select>
                        <div id="time-availability-info" class="availability-info"></div>
                        @error('booking_time') <div class="small" style="color:#b91c1c">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- payment method --}}
                <div>
                    <label class="small">Metode Pembayaran</label>
                    <select name="payment_method" id="payment_method" class="input" required>
                        <option value="cash" {{ old('payment_method', $booking->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="transfer" {{ old('payment_method', $booking->payment_method) == 'transfer' ? 'selected' : '' }}>Transfer</option>
                    </select>
                    <div id="uploadProofSection" style="margin-top:8px; {{ old('payment_method', $booking->payment_method) !== 'transfer' ? 'display:none;' : '' }}">
                        <label class="small">Upload Bukti Transfer</label>
                        <input type="file" name="payment_proof" class="input" accept="image/*">
                        @if($booking->payment_proof && $booking->payment_method === 'transfer')
                            <div class="small" style="margin-top:6px;">
                                Bukti sekarang: <a href="{{ asset('storage/' . $booking->payment_proof) }}" target="_blank" class="small" style="color:var(--red)">Lihat</a>
                            </div>
                        @endif
                        <div class="small">Hanya jika metode = Transfer</div>
                    </div>
                </div>

                {{-- paket (mirip create) --}}
                <div>
                    <label class="small">Pilih Paket</label>
                    <div class="packages-grid" id="packageGrid">
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
                            <div tabindex="0" class="package-card {{ (old('package_name', $booking->package_name) === $p['name']) ? 'selected' : '' }}"
                                 data-package="{{ $p['name'] }}" data-price="{{ $p['price'] }}" data-backgrounds="{{ $p['bg'] }}" data-category="{{ $p['cat'] }}">
                                <div class="package-title">{{ $p['name'] }}</div>
                                <div class="package-price">IDR {{ number_format($p['price'],0,',','.') }}</div>
                                <div class="small" style="margin-top:8px">Max Background: {{ $p['bg'] }}</div>
                            </div>
                        @endforeach
                    </div>

                    <input type="hidden" name="package_name" id="package_name" value="{{ old('package_name', $booking->package_name) }}">
                </div>

                {{-- session selector for group packages --}}
                <div id="sessionSelectWrapper" class="bg-gray-50 p-4 rounded-lg border mt-2 {{ in_array(old('package_name', $booking->package_name), ['Plain','Grande','Royal','plain','grande','royal']) ? '' : 'hidden' }}">
                    <label class="small">Jenis Sesi (hanya untuk Plain / Grande / Royal)</label>
                    <select id="sessionSelect" class="input" aria-describedby="session-error">
                        <option value="">-- Pilih Jenis Sesi --</option>
                        <option value="family" {{ old('session_name', $booking->session_name) == 'family' ? 'selected' : '' }}>Family</option>
                        <option value="graduation" {{ old('session_name', $booking->session_name) == 'graduation' ? 'selected' : '' }}>Graduation</option>
                        <option value="maternity" {{ old('session_name', $booking->session_name) == 'maternity' ? 'selected' : '' }}>Maternity</option>
                    </select>
                    <p id="session-error" class="text-red-500 text-sm mt-1 hidden"></p>
                </div>

                {{-- backgrounds (mirip create) --}}
                <div>
                    <div style="display:flex; justify-content:space-between; align-items:center">
                        <label class="small">Pilih Background (Maks: <span id="maxBackgroundsLabel">0</span>)</label>
                        <div class="small">Terpilih: <span id="backgroundCounter">0</span></div>
                    </div>

                    <div id="background-container" class="bg-grid" style="margin-top:10px;">
                        {{-- filled by JS --}}
                    </div>

                    @php
                        // Normalize $selectedBackgrounds to array of IDs for JS (string)
                        $selectedBgIds = [];
                        if(!empty($selectedBackgrounds ?? [])) {
                            foreach($selectedBackgrounds as $sb) {
                                if(is_array($sb) && isset($sb['id'])) $selectedBgIds[] = (string)$sb['id'];
                                elseif(is_object($sb) && isset($sb->id)) $selectedBgIds[] = (string)$sb->id;
                                else $selectedBgIds[] = (string)$sb;
                            }
                        }
                    @endphp

                    <input type="hidden" name="selected_backgrounds" id="selected_backgrounds" value='{{ old('selected_backgrounds') ? json_encode(old('selected_backgrounds')) : json_encode($selectedBgIds) }}'>

                    <div class="small" style="margin-top:8px">* Pilih minimal 1 background jika paket mewajibkan.</div>
                    @error('selected_backgrounds') <div class="small" style="color:#b91c1c">{{ $message }}</div> @enderror
                </div>

                {{-- extra items --}}
                <div>
                    <label class="small">Tambah & Item</label>
                    <div class="extras-grid">
                        <div>
                            <div class="small" style="font-weight:700">Cetak Foto</div>
                            @foreach($printItems as $it)
                                <label class="extra-item">
                                    <input type="checkbox" name="selected_extra_items[]" value="{{ $it->id }}" class="extra-checkbox" data-price="{{ $it->price }}" data-name="{{ $it->name }}" @if(in_array($it->id, (array)$selectedExtraItems)) checked @endif>
                                    <div>
                                        <div style="font-weight:700">{{ $it->name }}</div>
                                        <div class="small">IDR {{ number_format($it->price,0,',','.') }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <div>
                            <div class="small" style="font-weight:700">Frame Foto</div>
                            @foreach($frameItems as $it)
                                <label class="extra-item">
                                    <input type="checkbox" name="selected_extra_items[]" value="{{ $it->id }}" class="extra-checkbox" data-price="{{ $it->price }}" data-name="{{ $it->name }}" @if(in_array($it->id, (array)$selectedExtraItems)) checked @endif>
                                    <div>
                                        <div style="font-weight:700">{{ $it->name }}</div>
                                        <div class="small">IDR {{ number_format($it->price,0,',','.') }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <div>
                            <div class="small" style="font-weight:700">Tambahan & Layanan</div>
                            @foreach($serviceItems as $it)
                                <label class="extra-item">
                                    <input type="checkbox" name="selected_extra_items[]" value="{{ $it->id }}" class="extra-checkbox" data-price="{{ $it->price }}" data-name="{{ $it->name }}" @if(in_array($it->id, (array)$selectedExtraItems)) checked @endif>
                                    <div>
                                        <div style="font-weight:700">{{ $it->name }}</div>
                                        <div class="small">IDR {{ number_format($it->price,0,',','.') }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- baby info, notes --}}
                <div class="row-2">
                    <div>
                        <label class="small">Nama Bayi (opsional)</label>
                        <input type="text" name="baby_name" class="input" value="{{ old('baby_name', $booking->baby_name) }}">
                    </div>
                    <div>
                        <label class="small">Usia Bayi (opsional)</label>
                        <input type="text" name="baby_age" class="input" value="{{ old('baby_age', $booking->baby_age) }}">
                    </div>
                </div>

                <div>
                    <label class="small">Catatan Tambahan</label>
                    <textarea name="notes" rows="3" class="input">{{ old('notes', $booking->notes) }}</textarea>
                </div>

                {{-- total & submit --}}
                <div style="display:flex; justify-content:space-between; align-items:center; gap:12px;">
                    <div>
                        <div class="small">Total (server authoritative)</div>
                        <div id="totalPriceDisplay" style="font-weight:700; font-size:1.1rem; color:var(--red)">{{ 'IDR ' . number_format(old('total_price', $booking->total_price ?? 0),0,',','.') }}</div>
                    </div>

                    <div class="actions">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-ghost">Batal</a>
                    </div>
                </div>

                {{-- hidden server total (still send) --}}
                <input type="hidden" name="total_price" id="hidden_total_price" value="{{ old('total_price', $booking->total_price ?? 0) }}">
            </div>
        </form>

        {{-- RIGHT column: payment status, metadata --}}
        <aside>
            <div class="card">
                <div class="small" style="font-weight:700; margin-bottom:8px">Informasi Pembayaran</div>
                <div class="small">Status: <strong>{{ $booking->status }}</strong></div>
                <div class="small">Metode: <strong>{{ $booking->payment_method }}</strong></div>
                @if($booking->payment_deadline)
                    <div class="small">Deadline pembayaran: <strong>{{ $booking->payment_deadline->format('d M Y H:i') }}</strong></div>
                @endif

                {{-- Hanya tampilkan area bukti ketika metode = transfer --}}
                @if($booking->payment_method === 'transfer')
                    @if($booking->payment_proof)
                        <div style="margin-top:10px;">
                            <div class="small" style="font-weight:700">Bukti Pembayaran</div>
                            <img src="{{ asset('storage/' . $booking->payment_proof) }}" alt="Bukti" style="width:100%; border-radius:8px; margin-top:8px; border:1px solid #ececec;">
                            <a href="{{ asset('storage/' . $booking->payment_proof) }}" target="_blank" class="small" style="color:var(--red); display:block; margin-top:6px">Lihat Bukti</a>
                        </div>
                    @else
                        <div class="small" style="margin-top:10px; color:#92400e;">
                            Metode pembayaran: <strong>Transfer</strong> — belum ada bukti transfer yang diunggah.
                        </div>
                    @endif
                @endif
            </div>

            <div class="card" style="margin-top:12px;">
                <div class="small" style="font-weight:700; margin-bottom:8px">Informasi Lain</div>
                <div class="small">Dibuat: {{ $booking->created_at->format('d M Y H:i') }}</div>
                <div class="small">ID Booking: #{{ $booking->id }}</div>
            </div>
        </aside>
    </div>

    @endif {{-- end status check --}}
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ----- state & DOM refs -----
    let selectedPackage = null;
    let basePrice = 0;
    let maxBackgrounds = 0;
    let packageCategory = '';
    let selectedBackgrounds = @json($selectedBgIds ?? []);
    selectedBackgrounds = Array.isArray(selectedBackgrounds) ? selectedBackgrounds.map(String) : [];
    let selectedExtras = @json($selectedExtraItems ?? []);
    selectedExtras = Array.isArray(selectedExtras) ? selectedExtras.map(String) : [];

    const packageCards = document.querySelectorAll('.package-card');
    const packageNameInput = document.getElementById('package_name');
    const sessionNameInput = document.getElementById('session_name');
    const sessionNameDisplay = document.getElementById('sessionNameDisplay');
    const sessionSelectWrapper = document.getElementById('sessionSelectWrapper');
    const sessionSelect = document.getElementById('sessionSelect');
    const bgContainer = document.getElementById('background-container');
    const bgCounter = document.getElementById('backgroundCounter');
    const maxBgLabel = document.getElementById('maxBackgroundsLabel');
    const selectedBackgroundsInput = document.getElementById('selected_backgrounds');
    const extraCheckboxes = document.querySelectorAll('.extra-checkbox');
    const bookingDateInput = document.getElementById('booking_date');
    const bookingTimeSelect = document.getElementById('booking_time');
    const timeAvailabilityInfo = document.getElementById('time-availability-info');
    const paymentMethodSelect = document.getElementById('payment_method');
    const uploadProofSection = document.getElementById('uploadProofSection');
    const totalPriceDisplay = document.getElementById('totalPriceDisplay');
    const hiddenTotalPrice = document.getElementById('hidden_total_price');

    // server-original values (used to allow partial edits)
    const originalBookingDate = @json(old('booking_date', $booking->booking_date ?? ''));
    const originalBookingTime = @json(old('booking_time', $booking->booking_time ?? ''));
    const originalPackageName = @json(old('package_name', $booking->package_name ?? ''));

    // helper
    const idr = (n) => 'IDR ' + new Intl.NumberFormat('id-ID').format(n || 0);
    const safeLower = s => (s||'').toString().toLowerCase();

    // Background lists from controller (category buckets)
    const singleCategoryMap = {
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

    // ----- package select & init -----
    function initPackage() {
        const serverPackage = packageNameInput.value || null;
        let node = null;
        if (serverPackage) {
            node = Array.from(packageCards).find(c => safeLower(c.dataset.package) === safeLower(serverPackage));
        }
        if (!node && packageCards.length) node = Array.from(packageCards)[0];
        if (node) selectPackage(node, false);
    }

    function selectPackage(card, triggerRender=true) {
        packageCards.forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');

        selectedPackage = card.dataset.package;
        basePrice = parseInt(card.dataset.price || 0);
        maxBackgrounds = parseInt(card.dataset.backgrounds || 0);
        packageCategory = (card.dataset.category || '').toString().trim();

        packageNameInput.value = selectedPackage;

        // compute session name for display (not authoritative when group packages: real session chosen by sessionSelect)
        const computed = computeSessionName(selectedPackage);
        if (!sessionSelect || !sessionSelect.value) {
            sessionNameInput.value = computed;
            if (sessionNameDisplay) sessionNameDisplay.textContent = computed;
        }

        maxBgLabel.textContent = maxBackgrounds;
        bgCounter.textContent = selectedBackgrounds.length;

        // show/hide session selector for group packages
        if (['plain','grande','royal'].includes(packageCategory.toLowerCase())) {
            sessionSelectWrapper.classList.remove('hidden');
        } else {
            sessionSelectWrapper.classList.add('hidden');
            if (sessionSelect) sessionSelect.value = '';
            sessionNameInput.value = computed;
            if (sessionNameDisplay) sessionNameDisplay.textContent = computed;
        }

        if (maxBackgrounds === 0) {
            bgContainer.innerHTML = '<div class="small">Paket ini tidak membutuhkan pemilihan background.</div>';
            selectedBackgrounds = [];
            updateHiddenBackgrounds();
        } else {
            // If group package and session selected -> render union(pkgCategory, session)
            const sess = (sessionSelect && sessionSelect.value) ? sessionSelect.value : '';
            if (['plain','grande','royal'].includes(packageCategory.toLowerCase()) && sess) {
                renderBackgroundsForPackageAndSession(packageCategory.toLowerCase(), sess);
            } else {
                renderBackgroundsByCategory(packageCategory || 'all');
            }
        }

        updateTotalPreview();
    }

    function computeSessionName(pkgName) {
        const n = safeLower(pkgName || '');
        if (n.includes('baby')) return 'Baby Smash Cake';
        if (n.includes('prewed') || n.includes('pre-wedding')) return 'Pre-Wedding Session';
        return 'Photoshoot Session';
    }

    packageCards.forEach(card => {
        card.addEventListener('click', function () { selectPackage(this, true); });
        card.addEventListener('keydown', function(e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); selectPackage(this, true); } });
    });

    // ----- backgrounds rendering -----
    function renderBackgroundsByCategory(catKey) {
        const backgrounds = (singleCategoryMap[catKey] ?? singleCategoryMap['all'] ?? []);
        renderBackgroundList(backgrounds);
    }

    // Union of package category + session category (useful for group packages)
    function renderBackgroundsForPackageAndSession(pkgKey, sessionKey) {
        const pkgArr = singleCategoryMap[pkgKey] ?? [];
        const sessArr = singleCategoryMap[sessionKey] ?? [];
        // union by id
        const map = {};
        [...pkgArr, ...sessArr].forEach(b => { if (b && b.id) map[b.id] = b; });
        const unionArr = Object.values(map);
        renderBackgroundList(unionArr);
    }

    function renderBackgroundList(list) {
        bgContainer.innerHTML = '';
        if (!Array.isArray(list) || list.length === 0) {
            bgContainer.innerHTML = '<div class="small">Belum ada background untuk kategori ini.</div>';
            updateBackgroundCounter();
            return;
        }

        list.forEach(bg => {
            const id = String(bg.id);
            const selected = selectedBackgrounds.includes(id);
            const div = document.createElement('div');
            div.className = 'background-option' + (selected ? ' selected' : '');
            div.dataset.id = id;

            const imgSrc = bg.image ? `{{ asset('storage') }}/${bg.image}` : null;
            div.innerHTML = `
                ${ imgSrc ? `<img src="${imgSrc}" alt="${(bg.name||'Background')}">` : '<div style="height:120px;background:#f3f4f6"></div>' }
                <div class="meta"><div style="font-weight:600">${bg.name ?? 'Background'}</div></div>
            `;

            div.addEventListener('click', function () { toggleBackground(id, div); });
            div.addEventListener('keydown', function(e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); toggleBackground(id, div); } });

            bgContainer.appendChild(div);
        });

        // ensure classes for already-selected
        document.querySelectorAll('.background-option').forEach(el => {
            const id = el.dataset.id;
            if (selectedBackgrounds.includes(id)) el.classList.add('selected');
            else el.classList.remove('selected');
        });
        updateBackgroundCounter();
        updateHiddenBackgrounds();
    }

    function toggleBackground(id, el) {
        const idx = selectedBackgrounds.indexOf(id);
        if (idx > -1) {
            selectedBackgrounds.splice(idx, 1);
            el.classList.remove('selected');
        } else {
            if (maxBackgrounds > 0 && selectedBackgrounds.length >= maxBackgrounds) {
                alert(`Paket ${selectedPackage} hanya boleh ${maxBackgrounds} background.`);
                return;
            }
            selectedBackgrounds.push(id);
            el.classList.add('selected');
            try { el.animate([{ boxShadow: '0 0 0 0 rgba(220,38,38,0.3)' }, { boxShadow: '0 0 0 10px rgba(220,38,38,0)' }], { duration: 600 }); } catch(e){}
        }
        updateBackgroundCounter();
        updateHiddenBackgrounds();
    }

    function updateBackgroundCounter() {
        const selectedCount = selectedBackgrounds.length || 0;
        bgCounter.textContent = `${selectedCount}`;
        // visual disable when max reached
        const opts = document.querySelectorAll('.background-option');
        if (maxBackgrounds > 0 && selectedCount >= maxBackgrounds) {
            opts.forEach(o => { if (!o.classList.contains('selected')) { o.style.opacity = '0.45'; o.style.pointerEvents = 'none'; } });
        } else {
            opts.forEach(o => { o.style.opacity = ''; o.style.pointerEvents = ''; });
        }
    }

    function updateHiddenBackgrounds() {
        selectedBackgrounds = Array.from(new Set(selectedBackgrounds));
        const arr = selectedBackgrounds.map(s => (isNaN(s) ? s : Number(s)));
        selectedBackgroundsInput.value = JSON.stringify(arr);
    }

    // ----- session select handling (for group packages) -----
    if (sessionSelect) {
        sessionSelect.addEventListener('change', function () {
            const sess = this.value;
            sessionNameInput.value = sess || computeSessionName(packageNameInput.value);
            if (sessionNameDisplay) sessionNameDisplay.textContent = sessionNameInput.value || '-';

            // if group package selected, render union of package+session
            if (packageCategory && ['plain','grande','royal'].includes(packageCategory.toLowerCase())) {
                if (!sess) {
                    renderBackgroundsByCategory(packageCategory || 'all');
                } else {
                    renderBackgroundsForPackageAndSession(packageCategory.toLowerCase(), sess);
                }
            }
        });
    }

    // ----- extras binding & total preview -----
    function updateTotalPreview() {
        let extrasTotal = 0;
        const extras = Array.from(document.querySelectorAll('.extra-checkbox:checked'));
        extras.forEach(cb => { extrasTotal += parseInt(cb.dataset.price || 0); });
        const total = (basePrice || 0) + extrasTotal;
        totalPriceDisplay.textContent = idr(total);
        hiddenTotalPrice.value = total;
    }
    document.querySelectorAll('.extra-checkbox').forEach(cb => cb.addEventListener('change', updateTotalPreview));

    // ----- payment proof toggle -----
    if (paymentMethodSelect) {
        paymentMethodSelect.addEventListener('change', () => {
            if (paymentMethodSelect.value === 'transfer') uploadProofSection.style.display = 'block';
            else uploadProofSection.style.display = 'none';
        });
    }

    // ----- availability / times -----
    let availableTimes = [];
    async function fetchAvailableTimes() {
        const date = bookingDateInput.value;
        if (!date) return;
        bookingTimeSelect.disabled = true;
        bookingTimeSelect.innerHTML = '<option>Memuat...</option>';
        timeAvailabilityInfo.style.display = 'none';

        try {
            const res = await fetch(`/api/available-times?booking_date=${encodeURIComponent(date)}`);
            if (!res.ok) throw new Error('fetch error');
            const data = await res.json();
            availableTimes = data.available_times || [];
            bookingTimeSelect.innerHTML = '';

            if (!availableTimes.length) {
                const opt = document.createElement('option');
                opt.value = '';
                opt.disabled = true;
                opt.textContent = 'Hari ini penuh';
                bookingTimeSelect.appendChild(opt);
                bookingTimeSelect.disabled = true;

                timeAvailabilityInfo.className = 'availability-info full';
                timeAvailabilityInfo.textContent = 'Tidak ada slot pada tanggal ini.';
            } else {
                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = '-- Pilih Waktu --';
                bookingTimeSelect.appendChild(placeholder);

                availableTimes.forEach(t => {
                    const opt = document.createElement('option');
                    opt.value = t;
                    opt.textContent = `${t} WIB`;
                    bookingTimeSelect.appendChild(opt);
                });

                // restore old time if available, else keep current booking time as first option (so comparisons work)
                const oldTime = @json(old('booking_time', $booking->booking_time));
                if (oldTime && availableTimes.includes(oldTime)) {
                    bookingTimeSelect.value = oldTime;
                } else {
                    const currentBookingTime = @json($booking->booking_time);
                    if (currentBookingTime && !availableTimes.includes(currentBookingTime)) {
                        const cur = document.createElement('option');
                        cur.value = currentBookingTime;
                        cur.textContent = `${currentBookingTime} WIB (current)`;
                        bookingTimeSelect.prepend(cur);
                        bookingTimeSelect.value = currentBookingTime;
                    }
                }

                bookingTimeSelect.disabled = false;
                timeAvailabilityInfo.className = (data.status === 'limited') ? 'availability-info limited' : 'availability-info available';
                timeAvailabilityInfo.textContent = `Tersedia ${availableTimes.length} slot.`;
            }
            timeAvailabilityInfo.style.display = 'block';
        } catch (err) {
            console.error(err);
            bookingTimeSelect.innerHTML = '<option value="">Gagal muat</option>';
            bookingTimeSelect.disabled = true;
            timeAvailabilityInfo.className = 'availability-info';
            timeAvailabilityInfo.textContent = 'Gagal memuat slot waktu. Coba lagi nanti.';
            timeAvailabilityInfo.style.display = 'block';
            availableTimes = [];
        }
    }

    if (bookingDateInput) {
        bookingDateInput.addEventListener('change', fetchAvailableTimes);
        if (bookingDateInput.value) fetchAvailableTimes();
    }

    // ----- form submit validations (partial-edit friendly) -----
    const form = document.getElementById('adminBookingForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            // require package
            if (!packageNameInput.value) {
                e.preventDefault();
                alert('Silakan pilih paket.');
                return;
            }

            // if group package - require session
            const pkgCat = (packageCategory || '').toLowerCase();
            if (['plain','grande','royal'].includes(pkgCat) && (!sessionSelect || !sessionSelect.value)) {
                e.preventDefault();
                alert('Silakan pilih jenis sesi untuk paket ini.');
                return;
            }

            // background requirement
            if (maxBackgrounds > 0 && selectedBackgrounds.length === 0) {
                e.preventDefault();
                alert('Pilih minimal 1 background sesuai paket.');
                return;
            }

            // determine whether user changed date/time
            const chosenDate = bookingDateInput.value;
            const chosenTime = bookingTimeSelect.value;

            const dateChanged = String(chosenDate || '') !== String(originalBookingDate || '');
            const timeChanged = String(chosenTime || '') !== String(originalBookingTime || '');

            // if neither date nor time changed, skip availability validation (allow partial edit)
            if (!dateChanged && !timeChanged) {
                updateHiddenBackgrounds();
                return;
            }

            // if date or time changed, require both
            if (!chosenDate || !chosenTime) {
                e.preventDefault(); alert('Pilih tanggal dan waktu yang valid.'); return;
            }

            // if date changed and times not loaded => block (can't trust)
            if (dateChanged && availableTimes.length === 0) {
                e.preventDefault(); alert('Slot waktu belum dimuat atau hari penuh. Silakan pilih tanggal lain.'); return;
            }

            // if availableTimes present and chosenTime not included -> block
            if (availableTimes.length && !availableTimes.includes(chosenTime)) {
                e.preventDefault(); alert('Waktu yang dipilih tidak tersedia. Mohon pilih waktu lain dari daftar.'); return;
            }

            // payment proof when transfer
            const method = paymentMethodSelect ? paymentMethodSelect.value : 'cash';
            if (method === 'transfer') {
                const fileInput = document.querySelector('input[name="payment_proof"]');
                // IMPORTANT: editing may already have payment_proof on server; require upload only if none on server and no file selected
                const hasServerProof = @json(!empty($booking->payment_proof) ? true : false);
                if (!hasServerProof && (!fileInput || !fileInput.files.length)) {
                    e.preventDefault();
                    alert('Upload bukti transfer karena metode pembayaran transfer dan belum ada bukti sebelumnya.');
                    return;
                }
            }

            // ensure selected_backgrounds hidden input is updated
            updateHiddenBackgrounds();
        });
    }

    // ----- initialize UI (package, backgrounds, extras, totals) -----
    (function initAll(){
        // set base price if the package was already selected
        const pkg = packageNameInput.value;
        if (pkg) {
            const found = Array.from(packageCards).find(c => c.dataset.package && c.dataset.package.toLowerCase() === pkg.toLowerCase());
            if (found) selectPackage(found, false);
        } else if (packageCards.length) {
            // not necessary to auto-select if you want admin to choose; but keep first for convenience
            // selectPackage(packageCards[0], false);
        }

        // if session present in server data, ensure session wrapper visible
        const serverSession = @json(old('session_name', $booking->session_name ?? ''));
        if (serverSession && ['plain','grande','royal'].includes((packageCategory||'').toLowerCase())) {
            if (sessionSelectWrapper) sessionSelectWrapper.classList.remove('hidden');
            if (sessionSelect) {
                sessionSelect.value = serverSession;
                sessionNameInput.value = serverSession;
                if (sessionNameDisplay) sessionNameDisplay.textContent = serverSession;
            }
            // re-render backgrounds as union
            if (packageCategory) renderBackgroundsForPackageAndSession(packageCategory.toLowerCase(), serverSession);
        } else {
            // if selectedBackgrounds exist, render by package
            if (selectedBackgrounds.length && packageCategory) {
                renderBackgroundsByCategory(packageCategory || 'all');
            } else if (packageCategory) {
                renderBackgroundsByCategory(packageCategory || 'all');
            }
        }

        // ensure checkbox extras state read & total preview set
        updateTotalPreview();

        // ensure hidden backgrounds input current
        updateHiddenBackgrounds();
    })();

});
</script>
@endpush
