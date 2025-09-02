@extends('layouts.app')
@section('title', 'Tambah Booking Manual - Admin')

@push('styles')
    <style>
        /* ... (CSS tidak berubah, potong di sini untuk singkat) ... */
        /* Paste CSS yang sama seperti sebelumnya */
    </style>
@endpush

@section('content')
    <div class="max-w-6xl mx-auto bg-white shadow-lg rounded-xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Tambah Booking Manual</h1>

            <div class="flex items-center gap-2">
                <a href="{{ route('bookings.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold rounded-lg transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <form action="{{ route('bookings.store') }}" method="POST" enctype="multipart/form-data" id="adminBookingForm"
            class="space-y-8">
            @csrf

            <!-- Pilih Customer -->
            <div class="bg-gray-50 p-6 rounded-lg border">
                <label class="block font-semibold text-gray-700 mb-3">Pilih Customer</label>
                <select name="customer_id"
                    class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    required>
                    <option value="">-- Pilih Customer --</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }} ({{ $customer->email }})
                        </option>
                    @endforeach
                </select>
                @error('customer_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nama Kontak & WhatsApp -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 p-6 rounded-lg border">
                    <label class="block font-semibold text-gray-700 mb-2">Nama Kontak</label>
                    <input type="text" name="contact_name" value="{{ old('contact_name') }}"
                        class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        required>
                    @error('contact_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="bg-gray-50 p-6 rounded-lg border">
                    <label class="block font-semibold text-gray-700 mb-2">Nomor WhatsApp</label>
                    <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number') }}"
                        class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="081234567890" required>
                    <p class="text-gray-500 text-sm mt-1">Contoh: 081234567890 atau +6281234567890</p>
                    @error('whatsapp_number')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Tanggal & Waktu -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 p-6 rounded-lg border">
                    <label class="block font-semibold text-gray-700 mb-2">Tanggal Pemotretan</label>
                    <input type="date" name="booking_date" id="booking_date" min="{{ now()->format('Y-m-d') }}"
                        value="{{ old('booking_date') }}"
                        class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        required>
                    @error('booking_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="bg-gray-50 p-6 rounded-lg border">
                    <label class="block font-semibold text-gray-700 mb-2">Waktu Pemotretan</label>
                    <select id="booking_time" name="booking_time"
                        class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        required {{ (empty($availableTimes['available_times'] ?? []) && !old('booking_time')) ? 'disabled' : '' }}>
                        @php $oldTime = old('booking_time'); @endphp
                        @php $initialTimes = $availableTimes['available_times'] ?? []; @endphp
                        @if(!empty($initialTimes) && old('booking_date'))
                            <option value="">-- Pilih Waktu --</option>
                            @foreach($initialTimes as $t)
                                <option value="{{ $t }}" {{ $oldTime === $t ? 'selected' : '' }}>{{ $t }} WIB</option>
                            @endforeach
                        @elseif($oldTime)
                            <option value="{{ $oldTime }}" selected>{{ $oldTime }} WIB</option>
                        @else
                            <option value="">Pilih tanggal dulu...</option>
                        @endif
                    </select>
                    <div id="time-availability-info" class="hidden mt-2 p-3 rounded-lg border">
                        <span id="availability-message"></span>
                    </div>
                    @error('booking_time')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Metode Pembayaran -->
            <div class="bg-gray-50 p-6 rounded-lg border">
                <label class="block font-semibold text-gray-700 mb-2">Metode Pembayaran</label>
                <select name="payment_method" id="payment_method"
                    class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    required>
                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                </select>

                <!-- Upload Bukti Transfer -->
                <div id="uploadProofSection" class="mt-4 {{ old('payment_method') !== 'transfer' ? 'hidden' : '' }}">
                    <label class="block font-semibold text-gray-700 mb-2">Upload Bukti Transfer</label>
                    <input type="file" name="payment_proof" class="w-full border border-gray-300 rounded-lg p-3"
                        accept="image/*">
                    <p class="text-gray-500 text-sm mt-1">Hanya jika metode pembayaran = Transfer</p>
                </div>
            </div>

            <!-- Pilih Paket -->
            <div class="package-section-container bg-gray-50 p-6 rounded-lg border">
                <h2 class="text-xl font-semibold text-gray-700 mb-6">Pilih Paket</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- packages (same markup as you already have) -->
                    <div class="package-card cursor-pointer border-2 rounded-lg p-4 hover:border-red-500 transition {{ in_array(old('package_name'), ['Baby Smash Cake', 'babysmash']) ? 'border-red-500 bg-red-50' : 'border-gray-200' }}"
                        data-package="Baby Smash Cake" data-price="550000" data-backgrounds="0" data-category="baby-smash" tabindex="0" role="button" aria-pressed="false">
                        <div class="package-header">
                            <h3 class="package-title font-dancing text-2xl">Baby Smash Cake</h3>
                            <div class="package-price text-lg font-bold">IDR 550k</div>
                        </div>
                        <ul class="package-features text-sm mt-3 space-y-1">
                            <li>30 Minutes Photoshoot</li>
                            <li>1 Concept Background</li>
                            <li>10 Edited Photos</li>
                            <li>1 Printed + Frame 12Rs</li>
                            <li>Max 2 Wardrobes</li>
                            <li>Google Drive Access (1 Month)</li>
                        </ul>
                        <div class="package-note text-xs text-red-500 mt-2">
                            <strong>Note:</strong> Cake Not From Studio
                        </div>
                    </div>

                    <div class="package-card cursor-pointer border-2 rounded-lg p-4 hover:border-red-500 transition {{ old('package_name') == 'Plain' ? 'border-red-500 bg-red-50' : 'border-gray-200' }}"
                        data-package="Plain" data-price="300000" data-backgrounds="1" data-category="plain" tabindex="0" role="button" aria-pressed="false">
                        <div class="package-header">
                            <h3 class="package-title font-dancing text-2xl">Plain</h3>
                            <div class="package-price text-lg font-bold">IDR 300k</div>
                        </div>
                        <ul class="package-features text-sm mt-3 space-y-1">
                            <li>20 Menit Photoshoot</li>
                            <li>Max 4 Persons</li>
                            <li>1 Background</li>
                            <li>10 Edited Photos</li>
                            <li>1 Photo Printed 12RS</li>
                            <li>Max 1 Wardrobe</li>
                            <li>Google Drive Access (1 Month)</li>
                        </ul>
                    </div>

                    <div class="package-card cursor-pointer border-2 rounded-lg p-4 hover:border-red-500 transition {{ old('package_name') == 'Grande' ? 'border-red-500 bg-red-50' : 'border-gray-200' }}"
                        data-package="Grande" data-price="500000" data-backgrounds="2" data-category="grande" tabindex="0" role="button" aria-pressed="false">
                        <div class="package-header">
                            <h3 class="package-title font-dancing text-2xl">Grande</h3>
                            <div class="package-price text-lg font-bold">IDR 500k</div>
                        </div>
                        <ul class="package-features text-sm mt-3 space-y-1">
                            <li>35 Menit Photoshoot</li>
                            <li>Max 4 Persons</li>
                            <li>2 Background</li>
                            <li>20 Edited Photos</li>
                            <li>1 Photo Printed 16RS</li>
                            <li>Max 2 Wardrobes</li>
                            <li>Google Drive Access (1 Month)</li>
                        </ul>
                    </div>

                    <div class="package-card cursor-pointer border-2 rounded-lg p-4 hover:border-red-500 transition {{ old('package_name') == 'Royal' ? 'border-red-500 bg-red-50' : 'border-gray-200' }}"
                        data-package="Royal" data-price="700000" data-backgrounds="4" data-category="royal" tabindex="0" role="button" aria-pressed="false">
                        <div class="package-header">
                            <h3 class="package-title font-dancing text-2xl">Royal</h3>
                            <div class="package-price text-lg font-bold">IDR 700k</div>
                        </div>
                        <ul class="package-features text-sm mt-3 space-y-1">
                            <li>50 Menit Photoshoot</li>
                            <li>Max 4 Persons</li>
                            <li>4 Background</li>
                            <li>30 Edited Photos</li>
                            <li>1 Photo Printed 16RS</li>
                            <li>Max 2 Wardrobes</li>
                            <li>Google Drive Access (1 Month)</li>
                        </ul>
                    </div>

                    <div class="package-card cursor-pointer border-2 rounded-lg p-4 hover:border-red-500 transition {{ in_array(old('package_name'), ['Prewed I', 'prewed1']) ? 'border-red-500 bg-red-50' : 'border-gray-200' }}"
                        data-package="Prewed I" data-price="700000" data-backgrounds="2" data-category="pre-wedding" tabindex="0" role="button" aria-pressed="false">
                        <div class="package-header">
                            <h3 class="package-title font-dancing text-2xl">Prewed I</h3>
                            <div class="package-price text-lg font-bold">IDR 700k</div>
                        </div>
                        <ul class="package-features text-sm mt-3 space-y-1">
                            <li>50 Minutes Photosession</li>
                            <li>2 Background</li>
                            <li>20 Edited Photo</li>
                            <li>Max 1 Wardrobe</li>
                            <li>1 Photo Printed 12rs + Frame</li>
                            <li>Google Drive Expired 1 Month</li>
                        </ul>
                    </div>

                    <div class="package-card cursor-pointer border-2 rounded-lg p-4 hover:border-red-500 transition {{ in_array(old('package_name'), ['Prewed II', 'prewed2']) ? 'border-red-500 bg-red-50' : 'border-gray-200' }}"
                        data-package="Prewed II" data-price="1000000" data-backgrounds="3" data-category="pre-wedding" tabindex="0" role="button" aria-pressed="false">
                        <div class="package-header">
                            <h3 class="package-title font-dancing text-2xl">Prewed II</h3>
                            <div class="package-price text-lg font-bold">IDR 1000k</div>
                        </div>
                        <ul class="package-features text-sm mt-3 space-y-1">
                            <li>70 Minutes Photosession</li>
                            <li>3 Background</li>
                            <li>40 Edited Photo</li>
                            <li>Max 2 Wardrobe</li>
                            <li>2 Photo Printed 16rs + Frame</li>
                            <li>Google Drive Expired 1 Month</li>
                        </ul>
                    </div>
                </div>

                <input type="hidden" name="package_name" id="package_name" value="{{ old('package_name') }}">
                <!-- session_name will be filled by JS when required -->
                <input type="hidden" name="session_name" id="session_name" value="{{ old('session_name') ?? '' }}">
                @error('package_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Session selector (only visible when group package selected) -->
            <div id="sessionSelectWrapper" class="bg-gray-50 p-6 rounded-lg border hidden">
                <label class="block font-semibold text-gray-700 mb-2">Jenis Sesi (hanya untuk Plain/Grande/Royal)</label>
                <select id="sessionSelect" class="w-full border border-gray-300 rounded-lg p-3" aria-describedby="session-error">
                    <option value="">-- Pilih Jenis Sesi --</option>
                    <option value="family" {{ old('session_name') == 'family' ? 'selected' : '' }}>Family</option>
                    <option value="graduation" {{ old('session_name') == 'graduation' ? 'selected' : '' }}>Graduation</option>
                    <option value="maternity" {{ old('session_name') == 'maternity' ? 'selected' : '' }}>Maternity</option>
                </select>
                <p id="session-error" class="text-red-500 text-sm mt-1 hidden"></p>
            </div>

            <!-- Pilih Background -->
            <div id="background-section"
                class="bg-gray-50 p-6 rounded-lg border {{ in_array(old('package_name'), ['Baby Smash Cake', 'babysmash']) || !old('package_name') ? 'hidden' : '' }}">
                <div class="flex justify-between items-center mb-3">
                    <label class="block font-semibold text-gray-700">Pilih Background (Maks: <span
                            id="maxBackgroundsLabel">0</span>)</label>
                    <span class="background-counter"
                        id="backgroundCounter">{{ is_array($selectedBackgrounds ?? null) ? count($selectedBackgrounds) : 0 }}</span>
                </div>
                <div id="background-container" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <!-- Background akan diisi oleh JavaScript -->
                </div>
                <input type="hidden" name="selected_backgrounds" id="selected_backgrounds"
                    value="{{ old('selected_backgrounds') ? json_encode($selectedBackgrounds) : json_encode($selectedBackgrounds ?? []) }}">
                <p class="text-gray-500 text-sm mt-4">* Silakan pilih minimal 1 background</p>
                @error('selected_backgrounds')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Extra Items -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Cetak Foto -->
                <div class="bg-gray-50 p-6 rounded-lg border">
                    <h3 class="font-semibold text-gray-700 mb-3">Cetak Foto</h3>
                    <div class="space-y-3">
                        @foreach($printItems as $item)
                            <label class="flex items-center p-2 border rounded hover:bg-gray-50">
                                <input type="checkbox" name="selected_extra_items[]" value="{{ $item->id }}"
                                    class="h-4 w-4 text-red-600 rounded focus:ring-red-500 extra-checkbox"
                                    data-name="{{ $item->name }}" data-price="{{ $item->price }}"
                                    @if(old('selected_extra_items') && in_array($item->id, old('selected_extra_items'))) checked
                                    @endif>
                                <span class="ml-2 text-gray-700">{{ $item->name }} (IDR
                                    {{ number_format($item->price) }})</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Frame Foto -->
                <div class="bg-gray-50 p-6 rounded-lg border">
                    <h3 class="font-semibold text-gray-700 mb-3">Frame Foto</h3>
                    <div class="space-y-3">
                        @foreach($frameItems as $item)
                            <label class="flex items-center p-2 border rounded hover:bg-gray-50">
                                <input type="checkbox" name="selected_extra_items[]" value="{{ $item->id }}"
                                    class="h-4 w-4 text-red-600 rounded focus:ring-red-500 extra-checkbox"
                                    data-name="{{ $item->name }}" data-price="{{ $item->price }}"
                                    @if(old('selected_extra_items') && in_array($item->id, old('selected_extra_items'))) checked
                                    @endif>
                                <span class="ml-2 text-gray-700">{{ $item->name }} (IDR
                                    {{ number_format($item->price) }})</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Tambahan & Layanan -->
                <div class="bg-gray-50 p-6 rounded-lg border">
                    <h3 class="font-semibold text-gray-700 mb-3">Tambahan & Layanan</h3>
                    <div class="space-y-3">
                        @foreach($serviceItems as $item)
                            <label class="flex items-center p-2 border rounded hover:bg-gray-50">
                                <input type="checkbox" name="selected_extra_items[]" value="{{ $item->id }}"
                                    class="h-4 w-4 text-red-600 rounded focus:ring-red-500 extra-checkbox"
                                    data-name="{{ $item->name }}" data-price="{{ $item->price }}"
                                    @if(old('selected_extra_items') && in_array($item->id, old('selected_extra_items'))) checked
                                    @endif>
                                <span class="ml-2 text-gray-700">{{ $item->name }} (IDR
                                    {{ number_format($item->price) }})</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Informasi Bayi (Opsional) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 p-6 rounded-lg border">
                    <label class="block font-semibold text-gray-700 mb-2">Nama Bayi (Opsional)</label>
                    <input type="text" name="baby_name" value="{{ old('baby_name') }}"
                        class="w-full border border-gray-300 rounded-lg p-3">
                </div>

                <div class="bg-gray-50 p-6 rounded-lg border">
                    <label class="block font-semibold text-gray-700 mb-2">Usia Bayi (Opsional)</label>
                    <input type="text" name="baby_age" value="{{ old('baby_age') }}"
                        class="w-full border border-gray-300 rounded-lg p-3">
                </div>
            </div>

            <!-- Catatan Tambahan -->
            <div class="bg-gray-50 p-6 rounded-lg border">
                <label class="block font-semibold text-gray-700 mb-2">Catatan Tambahan</label>
                <textarea name="notes" rows="3"
                    class="w-full border border-gray-300 rounded-lg p-3">{{ old('notes') }}</textarea>
            </div>

            <!-- Total Payment -->
            <div class="bg-gray-50 p-6 rounded-lg border">
                <div class="flex justify-between items-center">
                    <span class="font-semibold text-lg">Total Pembayaran:</span>
                    <span id="totalPriceDisplay" class="text-2xl font-bold text-red-600">IDR
                        {{ number_format($totalPrice ?? 0) }}</span>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-center">
                <button type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white font-bold px-8 py-3 rounded-lg shadow transition text-lg">
                    Simpan Booking
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Data dari controller
            const combinedBackgroundsMap = @json($combinedBackgroundsMap ?? []);
            const singleCategoryMap = @json($singleCategoryMap ?? []);
            let availableTimes = @json($availableTimes['available_times'] ?? []);
            let selectedBackgrounds = @json($selectedBackgrounds ?? []);
            selectedBackgrounds = Array.isArray(selectedBackgrounds) ? selectedBackgrounds.map(String) : [];

            // extras from initial checked boxes
            let selectedExtras = Array.from(document.querySelectorAll('.extra-checkbox:checked')).map(cb => ({
                id: cb.value,
                name: cb.dataset.name,
                price: parseInt(cb.dataset.price || 0)
            }));

            let selectedPackage = null;
            let basePrice = 0;
            let maxBackgrounds = 0;
            let packageCategory = '';

            // DOM refs
            const packageCards = document.querySelectorAll('.package-card');
            const packageInput = document.getElementById('package_name');
            const sessionInputHidden = document.getElementById('session_name');
            const sessionSelectWrapper = document.getElementById('sessionSelectWrapper');
            const sessionSelect = document.getElementById('sessionSelect');
            const backgroundSection = document.getElementById('background-section');
            const bgContainer = document.getElementById('background-container');
            const bgCounter = document.getElementById('backgroundCounter');
            const maxBgLabel = document.getElementById('maxBackgroundsLabel');
            const selectedBackgroundsInput = document.getElementById('selected_backgrounds');
            const totalPriceDisplay = document.getElementById('totalPriceDisplay');
            const paymentMethod = document.getElementById('payment_method');
            const proofSection = document.getElementById('uploadProofSection');

            const idr = v => 'IDR ' + new Intl.NumberFormat('id-ID').format(v || 0);

            // Helpers
            function stratifyName(name) {
                return (name || '').toString().trim().toLowerCase().replace(/\s+/g, ' ').replace(/ /g, ' ');
            }

            function selectPackageCard(card, triggerUpdate = true) {
                packageCards.forEach(c => c.classList.remove('selected', 'border-red-500', 'bg-red-50'));
                card.classList.add('selected', 'border-red-500', 'bg-red-50');

                selectedPackage = String(card.dataset.package || '');
                basePrice = parseInt(card.dataset.price || 0);
                maxBackgrounds = parseInt(card.dataset.backgrounds || 0);
                packageCategory = (card.dataset.category || '').toString().trim();

                packageInput.value = selectedPackage;
                maxBgLabel.textContent = maxBackgrounds;
                bgCounter.textContent = selectedBackgrounds.length;

                // Show/hide session selector for group packages (plain/grande/royal)
                const pkgKey = packageCategory.toLowerCase();
                const isGroup = ['plain','grande','royal'].includes(pkgKey);
                if (isGroup) {
                    sessionSelectWrapper.classList.remove('hidden');
                } else {
                    sessionSelectWrapper.classList.add('hidden');
                    // clear session if package doesn't need it
                    sessionSelect.value = '';
                    sessionInputHidden.value = '';
                }

                // render appropriate backgrounds:
                if (maxBackgrounds === 0) {
                    backgroundSection.classList.add('hidden');
                    selectedBackgrounds = [];
                    updateHiddenBackgrounds();
                } else {
                    // if group and session selected -> use combined map
                    const sessionVal = (sessionSelect && sessionSelect.value) ? sessionSelect.value : '';
                    if (isGroup && sessionVal) {
                        renderBackgroundsForCombo(pkgKey, sessionVal);
                    } else {
                        // use single category map
                        renderBackgroundsByCategory(packageCategory || 'all');
                    }
                    backgroundSection.classList.remove('hidden');
                }

                if (triggerUpdate) updateTotal();
            }

            packageCards.forEach(card => {
                card.addEventListener('click', function () {
                    selectPackageCard(this, true);
                });
                card.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        selectPackageCard(this, true);
                    }
                });
            });

            // init package if old value present
            (function initPackageFromInput() {
                const pkg = (packageInput.value || '').toString().trim();
                if (!pkg) return;
                const found = Array.from(packageCards).find(c => (c.dataset.package || '').toLowerCase() === pkg.toLowerCase());
                if (found) selectPackageCard(found, false);
            })();

            // Session select handling
            if (sessionSelect) {
                sessionSelect.addEventListener('change', function () {
                    const sessionVal = this.value || '';
                    sessionInputHidden.value = sessionVal;
                    // if package selected and is group -> render combined backgrounds
                    if (packageCategory && ['plain','grande','royal'].includes(packageCategory.toLowerCase())) {
                        if (!sessionVal) {
                            // require selecting session
                            renderBackgroundsByCategory(packageCategory || 'all');
                        } else {
                            renderBackgroundsForCombo(packageCategory.toLowerCase(), sessionVal.toLowerCase());
                        }
                    }
                });
            }

            // Render backgrounds for combo (package_session)
            function renderBackgroundsForCombo(pkgKey, sessionKey) {
                const comboKey = `${pkgKey}_${sessionKey}`;
                const arr = combinedBackgroundsMap[comboKey] ?? [];
                // fallback if none: try single category (pkg) or session
                const fallback = (singleCategoryMap[pkgKey] ?? []).concat(singleCategoryMap[sessionKey] ?? []);
                const backgrounds = (arr.length ? arr : fallback);

                renderBackgroundsList(backgrounds);
            }

            function renderBackgroundsByCategory(categoryKey) {
                const arr = singleCategoryMap[categoryKey] ?? singleCategoryMap['all'] ?? [];
                renderBackgroundsList(arr);
            }

            function renderBackgroundsList(backgrounds) {
                bgContainer.innerHTML = '';
                if (!Array.isArray(backgrounds) || backgrounds.length === 0) {
                    bgContainer.innerHTML = '<div class="text-sm text-gray-500">Belum ada background untuk kategori ini.</div>';
                    return;
                }

                backgrounds.forEach(bg => {
                    const idStr = String(bg.id ?? bg['id'] ?? '');
                    const div = document.createElement('div');
                    div.className = 'background-option cursor-pointer border-2 rounded-lg overflow-hidden transition ' + (selectedBackgrounds.includes(idStr) ? 'border-red-500 bg-red-50' : 'border-gray-200');
                    div.dataset.background = idStr;
                    div.dataset.name = bg.name ?? ('BG ' + idStr);

                    const imgSrc = bg.image ? `{{ asset('storage') }}/${bg.image}` : null;
                    div.innerHTML = `
                        ${imgSrc ? `<img src="${imgSrc}" alt="${(bg.name||'Background')}" class="w-full h-32 object-cover">` : '<div style="height:128px;background:#f3f4f6"></div>'}
                        <div class="p-2 bg-gray-50"><p class="text-sm font-medium text-gray-700">${bg.name ?? 'Background'}</p></div>
                    `;

                    div.addEventListener('click', function () {
                        handleBackgroundToggle(this);
                    });
                    div.addEventListener('keydown', function(e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); handleBackgroundToggle(this); } });

                    bgContainer.appendChild(div);
                });

                updateBackgroundCounter();
            }

            function handleBackgroundToggle(el) {
                const id = String(el.dataset.background);
                const idx = selectedBackgrounds.indexOf(id);
                if (idx > -1) {
                    selectedBackgrounds.splice(idx, 1);
                    el.classList.remove('border-red-500', 'bg-red-50');
                } else {
                    if (selectedBackgrounds.length >= maxBackgrounds && maxBackgrounds > 0) {
                        alert(`Paket ${selectedPackage} hanya boleh ${maxBackgrounds} background.`);
                        return;
                    }
                    selectedBackgrounds.push(id);
                    el.classList.add('border-red-500', 'bg-red-50', 'pulse');
                    setTimeout(() => el.classList.remove('pulse'), 1400);
                }
                updateBackgroundCounter();
                updateHiddenBackgrounds();
            }

            function updateBackgroundCounter() {
                const selectedCount = selectedBackgrounds.length || 0;
                bgCounter.textContent = `${selectedCount}/${maxBackgrounds} dipilih`;
                // visually disable non-selected when max reached
                const options = document.querySelectorAll('.background-option');
                if (selectedCount >= maxBackgrounds && maxBackgrounds > 0) {
                    options.forEach(opt => { if (!opt.classList.contains('border-red-500')) { opt.style.opacity = '0.35'; opt.style.pointerEvents = 'none'; } });
                } else {
                    options.forEach(opt => { opt.style.opacity = ''; opt.style.pointerEvents = ''; });
                }
            }

            function updateHiddenBackgrounds() {
                selectedBackgrounds = Array.from(new Set(selectedBackgrounds));
                // store numbers where possible
                const arr = selectedBackgrounds.map(s => (isNaN(s) ? s : Number(s)));
                selectedBackgroundsInput.value = JSON.stringify(arr);
            }

            // extras binding
            document.querySelectorAll('.extra-checkbox').forEach(cb => {
                cb.addEventListener('change', () => {
                    selectedExtras = Array.from(document.querySelectorAll('.extra-checkbox:checked')).map(x => ({
                        id: x.value,
                        name: x.dataset.name,
                        price: parseInt(x.dataset.price || 0)
                    }));
                    updateTotal();
                });
            });

            // payment method toggle
            if (paymentMethod) {
                paymentMethod.addEventListener('change', () => {
                    if (paymentMethod.value === 'transfer') proofSection.classList.remove('hidden');
                    else proofSection.classList.add('hidden');
                });
            }

            // Fetch available times
            async function fetchAvailableTimes() {
                const date = document.getElementById('booking_date').value;
                if (!date) return;
                const select = document.getElementById('booking_time');
                const info = document.getElementById('time-availability-info');
                const msg = document.getElementById('availability-message');

                select.disabled = true;
                select.innerHTML = '<option>Memuat...</option>';
                info.classList.add('hidden');

                try {
                    const res = await fetch(`/api/available-times?booking_date=${encodeURIComponent(date)}`);
                    if (!res.ok) throw new Error('Fetch error');
                    const data = await res.json();

                    availableTimes = data.available_times || [];

                    select.innerHTML = '';
                    if (!availableTimes.length) {
                        const opt = document.createElement('option');
                        opt.value = '';
                        opt.disabled = true;
                        opt.textContent = 'Hari ini penuh';
                        select.appendChild(opt);
                        select.disabled = true;

                        info.className = 'availability-info full';
                        msg.textContent = 'Tidak ada slot tersedia.';
                    } else {
                        const placeholder = document.createElement('option');
                        placeholder.value = '';
                        placeholder.textContent = '-- Pilih Waktu --';
                        select.appendChild(placeholder);

                        availableTimes.forEach(t => {
                            const opt = document.createElement('option');
                            opt.value = t;
                            opt.textContent = `${t} WIB`;
                            select.appendChild(opt);
                        });

                        const oldTime = "{{ old('booking_time') }}";
                        if (oldTime && availableTimes.includes(oldTime)) {
                            select.value = oldTime;
                        }

                        select.disabled = false;
                        info.className = (data.status === 'limited') ? 'availability-info limited' : 'availability-info available';
                        msg.textContent = `Ada ${availableTimes.length} slot tersedia.`;
                    }
                    info.classList.remove('hidden');
                } catch (err) {
                    console.error(err);
                    const select = document.getElementById('booking_time');
                    select.innerHTML = '<option value="">Gagal muat</option>';
                    select.disabled = true;
                    const info = document.getElementById('time-availability-info');
                    info.className = 'availability-info';
                    info.textContent = 'Gagal memuat slot waktu. Coba lagi nanti.';
                    info.classList.remove('hidden');
                }
            }

            const bookingDateInput = document.getElementById('booking_date');
            if (bookingDateInput) {
                bookingDateInput.addEventListener('change', fetchAvailableTimes);
                if (bookingDateInput.value) fetchAvailableTimes();
            }

            // submit validations
            const form = document.getElementById('adminBookingForm');
            if (form) {
                form.addEventListener('submit', function (e) {
                    // require package
                    if (!packageInput.value) {
                        e.preventDefault();
                        alert('Silakan pilih paket.');
                        return;
                    }

                    // if package is group - require session
                    const pkgCat = (packageCategory || '').toLowerCase();
                    if (['plain','grande','royal'].includes(pkgCat) && (!sessionSelect || !sessionSelect.value)) {
                        e.preventDefault();
                        alert('Silakan pilih jenis sesi untuk paket ini.');
                        return;
                    }

                    // require background if package requires
                    if (maxBackgrounds > 0 && selectedBackgrounds.length === 0) {
                        e.preventDefault();
                        alert('Pilih minimal 1 background.');
                        return;
                    }

                    // client-side availability check (best-effort)
                    const bookingTime = document.getElementById('booking_time').value;
                    const bookingDate = document.getElementById('booking_date').value;
                    if (bookingDate && bookingTime && Array.isArray(availableTimes) && availableTimes.length > 0) {
                        if (!availableTimes.includes(bookingTime)) {
                            e.preventDefault();
                            alert('Waktu yang dipilih tidak tersedia. Silakan pilih dari daftar waktu yang tersedia.');
                            return;
                        }
                    }

                    // payment proof when transfer
                    const method = document.getElementById('payment_method').value;
                    if (method === 'transfer') {
                        const fileInput = document.querySelector('input[name="payment_proof"]');
                        if (!fileInput || !fileInput.files.length) {
                            e.preventDefault();
                            alert('Upload bukti transfer.');
                            return;
                        }
                    }

                    // ensure selected_backgrounds hidden input is updated
                    updateHiddenBackgrounds();
                });
            }

            function updateTotal() {
                const extrasTotal = selectedExtras.reduce((s, it) => s + (it.price || 0), 0);
                const total = (basePrice || 0) + extrasTotal;
                totalPriceDisplay.textContent = idr(total);
            }

            // initial inits
            (function initTotalsAndBackgrounds() {
                // set base price if a package was already selected via hidden input
                const pkg = packageInput.value;
                if (pkg) {
                    const found = Array.from(packageCards).find(c => c.dataset.package && c.dataset.package.toLowerCase() === pkg.toLowerCase());
                    if (found) selectPackageCard(found, false);
                }

                // if selectedBackgrounds exist from server, render current package backgrounds
                if (selectedBackgrounds.length && packageInput.value) {
                    selectedBackgrounds = selectedBackgrounds.map(String);
                    const found = Array.from(packageCards).find(c => c.dataset.package && c.dataset.package.toLowerCase() === packageInput.value.toLowerCase());
                    if (found) {
                        maxBackgrounds = parseInt(found.dataset.backgrounds || 0);
                        packageCategory = found.dataset.category || '';
                        // if session already provided
                        const sessVal = "{{ old('session_name') }}";
                        if (sessVal && ['plain','grande','royal'].includes((packageCategory||'').toLowerCase())) {
                            sessionSelectWrapper.classList.remove('hidden');
                            sessionSelect.value = sessVal;
                            sessionInputHidden.value = sessVal;
                            renderBackgroundsForCombo(packageCategory.toLowerCase(), sessVal);
                        } else {
                            renderBackgroundsByCategory(packageCategory || 'all');
                        }
                    } else {
                        renderBackgroundsByCategory('');
                    }
                }

                updateTotal();
                updateHiddenBackgrounds();
            })();

        });
    </script>
@endpush
