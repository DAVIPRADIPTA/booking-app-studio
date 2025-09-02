{{-- resources/views/bookings/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Riwayat Pemesanan')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Riwayat Pemesanan</h1>
                <p class="mt-1 text-sm text-gray-500">Lihat status, total, dan kelola pembayaran / permohonan pembatalan.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('homepage') }}" class="inline-flex items-center px-3 py-2 bg-white border border-gray-200 rounded-md text-sm text-gray-700 hover:shadow">
                    ← Kembali ke Beranda
                </a>
            </div>
        </div>

        {{-- Alerts --}}
        @if(session('successMessage'))
            <div class="rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3">{{ session('successMessage') }}</div>
        @endif
        @if(session('errorMessage'))
            <div class="rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3">{{ session('errorMessage') }}</div>
        @endif

        {{-- No bookings --}}
        @if($bookings->isEmpty())
            <div class="bg-white rounded-2xl shadow p-8 text-center">
                <svg class="mx-auto w-12 h-12 text-gray-300" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 7h18M3 12h18M3 17h18" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                <h2 class="mt-4 text-lg font-medium text-gray-700">Belum ada riwayat pemesanan</h2>
                <p class="mt-2 text-sm text-gray-500">Silakan pilih paket untuk membuat pemesanan.</p>
            </div>
        @else

            {{-- Desktop table --}}
            <div class="hidden md:block bg-white rounded-2xl shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Tanggal / Waktu</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Paket / Sesi</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Backgrounds / Extra</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-600">Total</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Status</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($bookings as $booking)
                            @php
                                // date/time safe display
                                try { $displayDate = \Carbon\Carbon::parse($booking->booking_date)->translatedFormat('d F Y'); } catch (\Throwable $e) { $displayDate = $booking->booking_date; }
                                try { $displayTime = \Carbon\Carbon::createFromFormat('H:i', $booking->booking_time)->format('H:i'); } catch (\Throwable $e) { $displayTime = $booking->booking_time; }

                                // compute cancellation eligibility
                                $canRequestCancel = true;
                                try {
                                    $bdt = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $booking->booking_date . ' ' . $booking->booking_time);
                                } catch (\Throwable $e) {
                                    try { $bdt = \Carbon\Carbon::parse($booking->booking_date . ' ' . $booking->booking_time); } catch (\Throwable $e2) { $bdt = null; }
                                }
                                if (in_array($booking->status, [\App\Models\Booking::STATUS_CANCELLED, \App\Models\Booking::STATUS_COMPLETED], true)) $canRequestCancel = false;
                                if ($booking->cancellation_requested) $canRequestCancel = false;
                                if ($bdt && now()->greaterThanOrEqualTo($bdt->copy()->subHours(24))) $canRequestCancel = false;

                                // status badge metadata from model accessor
                                $badge = is_array($booking->status_badge ?? null) ? $booking->status_badge : [
                                    'label' => $booking->status_label ?? ucfirst($booking->status),
                                    'color' => 'bg-gray-100 text-gray-700',
                                    'dot'   => 'bg-gray-400'
                                ];

                                // status API URL (adjust if your route differs)
                                $statusUrl = url('booking/' . $booking->id . '/status');
                            @endphp

                            <tr class="hover:bg-gray-50 booking-row" data-booking-id="{{ $booking->id }}" data-status-url="{{ $statusUrl }}">
                                <td class="px-4 py-4 text-sm font-medium text-gray-800">#{{ $booking->id }}</td>

                                <td class="px-4 py-4 text-sm text-gray-700">
                                    {{ $displayDate }}
                                    <div class="text-xs text-gray-500 mt-1">{{ $displayTime }}</div>
                                </td>

                                <td class="px-4 py-4 text-sm text-gray-800">
                                    <div class="font-medium">{{ $booking->package_name }}</div>
                                    @if($booking->session_name)
                                        <div class="text-xs text-gray-500 mt-1">{{ $booking->session_name }}</div>
                                    @endif
                                    @if($booking->baby_name)
                                        <div class="text-xs text-gray-500 mt-1">Nama bayi: {{ $booking->baby_name }} ({{ $booking->baby_age ?? '-' }})</div>
                                    @endif
                                </td>

                                <td class="px-4 py-4 text-sm text-gray-700">
                                    <div class="text-xs text-gray-600">Backgrounds:</div>
                                    <div class="text-sm mt-1">
                                        @if(!empty($booking->selected_backgrounds))
                                            @foreach($booking->selected_backgrounds as $bg)
                                                <span class="inline-block bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded mr-1 mb-1">
                                                    {{ $bg['name'] ?? '-' }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </div>

                                    <div class="text-xs text-gray-600 mt-2">Extra items:</div>
                                    <div class="text-sm mt-1">
                                        @if(!empty($booking->selected_extra_items))
                                            @foreach($booking->selected_extra_items as $it)
                                                <div class="flex items-center justify-between">
                                                    <div class="text-xs text-gray-700">{{ $it['name'] ?? '-' }}</div>
                                                    <div class="text-xs text-gray-500">Rp {{ number_format($it['price'] ?? 0,0,',','.') }}</div>
                                                </div>
                                            @endforeach
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-4 py-4 text-sm text-right font-semibold text-gray-800">
                                    Rp {{ number_format($booking->total_price ?? 0,0,',','.') }}
                                    <div class="text-xs text-gray-500 mt-1">Metode: {{ ucfirst($booking->payment_method ?? 'transfer') }}</div>
                                </td>

                                <td class="px-4 py-4 text-sm">
                                    {{-- Badge wrapper (updateable by JS) --}}
                                    <div class="booking-badge inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $badge['color'] ?? 'bg-gray-100 text-gray-700' }}" data-booking-id="{{ $booking->id }}">
                                        <span class="w-2 h-2 mr-2 rounded-full {{ $badge['dot'] ?? 'bg-gray-400' }}"></span>
                                        <span class="booking-badge-label">{{ $badge['label'] ?? ucfirst($booking->status) }}</span>
                                    </div>

                                    {{-- Remaining time (updateable by JS) --}}
                                    @if($booking->status === \App\Models\Booking::STATUS_WAITING_PAYMENT && method_exists($booking, 'getRemainingTimeFormatted'))
                                        <div class="text-xs text-gray-400 mt-1 booking-remaining" data-booking-id="{{ $booking->id }}">{{ $booking->getRemainingTimeFormatted() }}</div>
                                    @else
                                        <div class="text-xs text-gray-400 mt-1 booking-remaining hidden" data-booking-id="{{ $booking->id }}"></div>
                                    @endif

                                    @if($booking->cancellation_requested)
                                        <div class="mt-2 text-xs text-yellow-700 font-medium">Permohonan pembatalan terkirim</div>
                                    @endif

                                    @if(!empty($booking->payment_proof_url))
                                        <div class="mt-2 text-xs">
                                            <a href="{{ $booking->payment_proof_url }}" target="_blank" class="text-blue-600 underline">Lihat bukti pembayaran</a>
                                        </div>
                                    @endif
                                </td>

                                <td class="px-4 py-4 text-sm text-center">
                                    <div class="flex flex-col items-center space-y-2">
                                        @if($booking->status === \App\Models\Booking::STATUS_WAITING_PAYMENT)
                                            <a href="{{ route('booking.payment', $booking->id) }}" class="w-full inline-flex justify-center px-3 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">Bayar Sekarang</a>

                                            <button
                                                type="button"
                                                class="w-full inline-flex justify-center px-3 py-2 bg-white border border-gray-200 rounded-md text-sm hover:shadow upload-proof-btn"
                                                data-booking="{{ $booking->id }}">
                                                Unggah Bukti
                                            </button>
                                        @elseif($booking->status === \App\Models\Booking::STATUS_PENDING_VERIFICATION)
                                            <span class="inline-flex items-center px-3 py-2 bg-orange-50 text-orange-700 rounded-md text-sm">Menunggu verifikasi</span>
                                        @elseif($booking->status === \App\Models\Booking::STATUS_BOOKED)
                                            <span class="inline-flex items-center px-3 py-2 bg-green-50 text-green-700 rounded-md text-sm">Dikonfirmasi</span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-2 bg-white border border-gray-200 rounded-md text-sm text-gray-700">—</span>
                                        @endif

                                        @if($canRequestCancel)
                                            <button
                                                type="button"
                                                class="w-full inline-flex justify-center px-3 py-2 bg-red-600 text-white rounded-md text-sm hover:bg-red-700 request-cancel-btn"
                                                data-booking="{{ $booking->id }}">
                                                Ajukan Pembatalan
                                            </button>
                                        @elseif($booking->cancellation_requested)
                                            <span class="text-xs text-gray-500">Pembatalan: diajukan {{ optional($booking->cancellation_requested_at)->format('Y-m-d H:i') }}</span>
                                        @endif

                                        @if(!is_null($booking->refund_amount) && $booking->status === \App\Models\Booking::STATUS_CANCELLED)
                                            <div class="text-xs text-gray-600 mt-1">Refund: Rp {{ number_format($booking->refund_amount,0,',','.') }}</div>
                                            @if(!empty($booking->refund_proof_url))
                                                <a href="{{ $booking->refund_proof_url }}" target="_blank" class="text-xs text-blue-600 underline">Lihat bukti refund</a>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="p-4 border-t bg-gray-50">
                    {{ $bookings->onEachSide(1)->links() }}
                </div>
            </div>

            {{-- Mobile: cards --}}
            <div class="md:hidden space-y-4">
                @foreach($bookings as $booking)
                    @php
                        try { $displayDate = \Carbon\Carbon::parse($booking->booking_date)->format('d M Y'); } catch (\Throwable $e) { $displayDate = $booking->booking_date; }
                        try { $displayTime = \Carbon\Carbon::createFromFormat('H:i', $booking->booking_time)->format('H:i'); } catch (\Throwable $e) { $displayTime = $booking->booking_time; }
                        $canRequestCancelMobile = true;
                        try { $bdtMobile = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $booking->booking_date . ' ' . $booking->booking_time); } catch (\Throwable $e) { try { $bdtMobile = \Carbon\Carbon::parse($booking->booking_date . ' ' . $booking->booking_time); } catch (\Throwable $e2) { $bdtMobile = null; } }
                        if (in_array($booking->status, [\App\Models\Booking::STATUS_CANCELLED, \App\Models\Booking::STATUS_COMPLETED], true)) $canRequestCancelMobile = false;
                        if ($booking->cancellation_requested) $canRequestCancelMobile = false;
                        if ($bdtMobile && now()->greaterThanOrEqualTo($bdtMobile->copy()->subHours(24))) $canRequestCancelMobile = false;
                        $badge = is_array($booking->status_badge ?? null) ? $booking->status_badge : [
                            'label' => $booking->status_label ?? ucfirst($booking->status),
                            'color' => 'bg-gray-100 text-gray-700',
                            'dot'   => 'bg-gray-400'
                        ];
                        $statusUrl = url('booking/' . $booking->id . '/status');
                    @endphp

                    <div class="bg-white rounded-2xl shadow p-4 booking-card" data-booking-id="{{ $booking->id }}" data-status-url="{{ $statusUrl }}">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="text-xs text-gray-500">{{ $displayDate }} • {{ $displayTime }}</div>
                                <div class="mt-1 text-sm font-medium text-gray-800">{{ $booking->package_name }}</div>
                                @if($booking->session_name)
                                    <div class="text-xs text-gray-500 mt-1">{{ $booking->session_name }}</div>
                                @endif
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-semibold text-gray-800">Rp {{ number_format($booking->total_price ?? 0,0,',','.') }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ ucfirst($booking->payment_method ?? 'transfer') }}</div>
                                <div class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold mt-2 {{ $badge['color'] ?? 'bg-gray-100 text-gray-700' }}">
                                    <span class="w-2 h-2 mr-2 rounded-full {{ $badge['dot'] ?? 'bg-gray-400' }}"></span>
                                    {{ $badge['label'] ?? ucfirst($booking->status) }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach($booking->selected_backgrounds ?? [] as $bg)
                                <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">{{ $bg['name'] }}</span>
                            @endforeach
                        </div>

                        <div class="mt-3 flex gap-2">
                            @if($booking->status === \App\Models\Booking::STATUS_WAITING_PAYMENT)
                                <a href="{{ route('booking.payment', $booking->id) }}" class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-blue-600 text-white rounded-md text-sm">Bayar Sekarang</a>
                                <button class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-white border border-gray-200 rounded-md text-sm upload-proof-btn" data-booking="{{ $booking->id }}">Unggah Bukti</button>
                            @elseif($booking->status === \App\Models\Booking::STATUS_PENDING_VERIFICATION)
                                <div class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-orange-50 text-orange-700 rounded-md text-sm">Menunggu Verifikasi</div>
                            @else
                                <a href="#" class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-white border border-gray-200 rounded-md text-sm text-gray-700">—</a>
                            @endif
                        </div>

                        <div class="mt-3">
                            @if($booking->cancellation_requested)
                                <div class="text-xs text-yellow-700">Permohonan pembatalan diajukan: {{ optional($booking->cancellation_requested_at)->format('Y-m-d H:i') }}</div>
                            @else
                                @if($canRequestCancelMobile)
                                    <button class="mt-2 w-full inline-flex items-center justify-center px-3 py-2 bg-red-600 text-white rounded-md text-sm request-cancel-btn" data-booking="{{ $booking->id }}">Ajukan Pembatalan</button>
                                @endif
                            @endif
                        </div>

                        @if(!empty($booking->payment_proof_url))
                            <div class="mt-3 text-xs text-gray-500">
                                <a href="{{ $booking->payment_proof_url }}" target="_blank" class="underline text-blue-600">Lihat bukti pembayaran</a>
                            </div>
                        @endif
                    </div>
                @endforeach

                <div class="mt-4">
                    {{ $bookings->onEachSide(1)->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

{{-- UPLOAD PROOF MODAL --}}
<div id="uploadProofModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40 px-4">
    <div class="bg-white rounded-lg w-full max-w-lg shadow-lg overflow-hidden">
        <form id="uploadProofForm" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="p-4 border-b flex items-center justify-between">
                <h3 class="text-lg font-medium">Unggah Bukti Pembayaran</h3>
                <button type="button" id="uploadClose" class="text-gray-500 hover:text-gray-700">&times;</button>
            </div>

            <div class="p-4 space-y-3">
                <input type="hidden" name="booking_id" id="upload_booking_id" value="">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Pilih file (jpg/png)</label>
                    <input type="file" name="payment_proof" id="payment_proof_input" accept="image/*" required class="mt-1 block w-full">
                </div>

                <div id="paymentPreview" class="hidden mt-2">
                    <div class="text-xs text-gray-500 mb-1">Preview:</div>
                    <img id="paymentPreviewImg" src="#" alt="Preview" class="max-h-48 rounded shadow">
                </div>

                <div class="text-xs text-gray-500">
                    Maks. 2MB. Setelah mengunggah, status akan menjadi <strong>Menunggu Verifikasi</strong>.
                </div>
            </div>

            <div class="p-4 border-t flex justify-end gap-2">
                <button type="button" id="uploadCancelBtn" class="px-4 py-2 bg-gray-100 rounded hover:bg-gray-200">Batal</button>
                <button type="submit" id="uploadSubmitBtn" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Unggah</button>
            </div>
        </form>
    </div>
</div>

{{-- REQUEST CANCELLATION MODAL --}}
<div id="requestCancelModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40 px-4">
    <div class="bg-white rounded-lg w-full max-w-lg shadow-lg overflow-hidden">
        <form id="requestCancelForm" method="POST">
            @csrf
            <div class="p-4 border-b flex items-center justify-between">
                <h3 class="text-lg font-medium">Ajukan Pembatalan</h3>
                <button type="button" id="cancelClose" class="text-gray-500 hover:text-gray-700">&times;</button>
            </div>

            <div class="p-4 space-y-3">
                <input type="hidden" name="booking_id" id="cancel_booking_id" value="">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Alasan pembatalan</label>
                    <textarea name="reason" id="cancel_reason" rows="4" required class="mt-1 block w-full border rounded px-3 py-2"></textarea>
                </div>

                <div class="flex items-start space-x-2">
                    <input type="checkbox" id="cancel_agree" name="agree" value="1" required>
                    <label for="cancel_agree" class="text-xs text-gray-600">Saya setuju pembatalan tunduk pada kebijakan pengembalian dana dan biaya administrasi.</label>
                </div>

                <div class="text-xs text-gray-500">Pembatalan hanya dapat diajukan minimal 24 jam sebelum jadwal sesi.</div>
            </div>

            <div class="p-4 border-t flex justify-end gap-2">
                <button type="button" id="cancelCancelBtn" class="px-4 py-2 bg-gray-100 rounded hover:bg-gray-200">Batal</button>
                <button type="submit" id="cancelSubmitBtn" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Ajukan Pembatalan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
(function () {
    // ======================
    // Upload / cancel modals
    // ======================
    const uploadModal = document.getElementById('uploadProofModal');
    const uploadForm = document.getElementById('uploadProofForm');
    const uploadBookingInput = document.getElementById('upload_booking_id');
    const paymentInput = document.getElementById('payment_proof_input');
    const paymentPreview = document.getElementById('paymentPreview');
    const paymentPreviewImg = document.getElementById('paymentPreviewImg');
    const uploadClose = document.getElementById('uploadClose');
    const uploadCancelBtn = document.getElementById('uploadCancelBtn');

    const cancelModal = document.getElementById('requestCancelModal');
    const cancelForm = document.getElementById('requestCancelForm');
    const cancelBookingInput = document.getElementById('cancel_booking_id');
    const cancelClose = document.getElementById('cancelClose');
    const cancelCancelBtn = document.getElementById('cancelCancelBtn');

    // Open upload modal
    document.querySelectorAll('.upload-proof-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const bookingId = btn.dataset.booking;
            uploadBookingInput.value = bookingId;
            paymentInput.value = '';
            paymentPreview.style.display = 'none';
            paymentPreviewImg.src = '#';
            // Update form action to your route (adjust if needed)
            uploadForm.action = `{{ url('booking') }}/${bookingId}/upload-proof`;
            uploadModal.classList.remove('hidden');
            uploadModal.classList.add('flex');
        });
    });

    function closeUploadModal() {
        uploadModal.classList.add('hidden');
        uploadModal.classList.remove('flex');
    }

    uploadClose?.addEventListener('click', closeUploadModal);
    uploadCancelBtn?.addEventListener('click', closeUploadModal);

    // Preview selected image
    paymentInput?.addEventListener('change', function () {
        const f = this.files && this.files[0];
        if (!f) {
            paymentPreview.style.display = 'none';
            paymentPreviewImg.src = '#';
            return;
        }
        if (!f.type.startsWith('image/')) {
            alert('Harap pilih file gambar.');
            this.value = '';
            return;
        }
        const url = URL.createObjectURL(f);
        paymentPreviewImg.src = url;
        paymentPreview.style.display = 'block';
    });

    // Submit upload form (regular POST)
    uploadForm?.addEventListener('submit', function (e) {
        const btn = document.getElementById('uploadSubmitBtn');
        if (btn) { btn.disabled = true; btn.textContent = 'Mengunggah...'; }
    });

    // Open cancel modal
    document.querySelectorAll('.request-cancel-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const bookingId = btn.dataset.booking;
            cancelBookingInput.value = bookingId;
            document.getElementById('cancel_reason').value = '';
            document.getElementById('cancel_agree').checked = false;
            cancelForm.action = `{{ url('booking') }}/${bookingId}/request-cancellation`;
            cancelModal.classList.remove('hidden');
            cancelModal.classList.add('flex');
        });
    });

    function closeCancelModal() {
        cancelModal.classList.add('hidden');
        cancelModal.classList.remove('flex');
    }

    cancelClose?.addEventListener('click', closeCancelModal);
    cancelCancelBtn?.addEventListener('click', closeCancelModal);

    // Submit cancellation (regular POST)
    cancelForm?.addEventListener('submit', function (e) {
        const btn = document.getElementById('cancelSubmitBtn');
        if (btn) { btn.disabled = true; btn.textContent = 'Memproses...'; }
    });

    // Close modals on ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeUploadModal();
            closeCancelModal();
        }
    });

    // ====================================
    // Polling status for recent bookings
    // ====================================
    // We will poll only bookings present on the page, and only if their current status is waiting_payment or pending_verification.
    const POLL_INTERVAL_MS = 10_000; // 10s
    const bookingRows = Array.from(document.querySelectorAll('.booking-row, .booking-card'));

    function shouldPollRow(row) {
        // only poll rows that expose a status URL
        const statusUrl = row.dataset.statusUrl;
        if (!statusUrl) return false;

        // check initial badge label for "Menunggu Pembayaran" or "Menunggu Verifikasi"
        // safer: poll all rows by default but you can restrict further
        return true;
    }

    async function pollStatuses() {
        const rowsToPoll = bookingRows.filter(shouldPollRow);
        if (!rowsToPoll.length) return;

        for (const row of rowsToPoll) {
            const url = row.dataset.statusUrl;
            try {
                const resp = await fetch(url, { headers: { 'Accept': 'application/json' }});
                if (!resp.ok) continue;
                const data = await resp.json();
                updateRowFromStatus(row, data);
            } catch (err) {
                // ignore network errors (keep polling)
                console.error('Status poll error', err);
            }
        }
    }

    function updateRowFromStatus(row, data) {
        // expected data: { status, remaining_time, deadline, cancellation_requested, ... }
        const bookingId = row.dataset.bookingId || row.getAttribute('data-booking-id');
        if (!bookingId) return;

        // find badge
        const badgeEl = row.querySelector('.booking-badge[data-booking-id="' + bookingId + '"]') || row.querySelector('.inline-flex.items-center');
        const labelEl = badgeEl ? badgeEl.querySelector('.booking-badge-label') : null;
        // find remaining time element
        const remainingEl = row.querySelector('.booking-remaining[data-booking-id="' + bookingId + '"]');

        // update label & classes
        if (data.status) {
            const map = {
                'waiting_payment': { label: 'Menunggu Pembayaran', colorClass: 'bg-yellow-100 text-yellow-800', dotClass: 'bg-yellow-500' },
                'pending_verification': { label: 'Menunggu Verifikasi', colorClass: 'bg-blue-100 text-blue-800', dotClass: 'bg-blue-600' },
                'booked': { label: 'Dikonfirmasi', colorClass: 'bg-green-100 text-green-800', dotClass: 'bg-green-600' },
                'completed': { label: 'Selesai', colorClass: 'bg-gray-100 text-gray-700', dotClass: 'bg-gray-500' },
                'cancelled': { label: 'Dibatalkan', colorClass: 'bg-red-100 text-red-800', dotClass: 'bg-red-600' },
            };
            const meta = map[data.status] || { label: (data.status || '').replace(/[_-]/g,' '), colorClass: 'bg-gray-100 text-gray-700', dotClass: 'bg-gray-400' };

            if (badgeEl) {
                // remove previous bg-* text-* classes (simple approach: overwrite className partially)
                // safer to set only the color-related classes
                badgeEl.className = 'booking-badge inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold ' + meta.colorClass;
                const dot = badgeEl.querySelector('span:first-child');
                if (dot) { dot.className = 'w-2 h-2 mr-2 rounded-full ' + meta.dotClass; }
                if (labelEl) labelEl.textContent = meta.label;
                else {
                    // try to find label fallback
                    const possible = badgeEl.querySelector('span:not([class])') || badgeEl.querySelector('span:last-child');
                    if (possible) possible.textContent = meta.label;
                }
            }
        }

        // update remaining time (if returned)
        if (typeof data.remaining_time !== 'undefined' && remainingEl) {
            // data.remaining_time is seconds; convert to human-readable similar to model helper
            let rem = '';
            let secs = parseInt(data.remaining_time || 0, 10);
            if (secs <= 0) {
                remainingEl.textContent = 'Waktu habis';
                remainingEl.classList.remove('hidden');
            } else {
                const days = Math.floor(secs / (60*60*24));
                secs = secs % (60*60*24);
                const hours = Math.floor(secs / (60*60));
                secs = secs % (60*60);
                const minutes = Math.floor(secs / 60);
                const seconds = secs % 60;
                const parts = [];
                if (days) parts.push(days + ' hari');
                if (hours) parts.push(hours + ' jam');
                if (minutes && days === 0) parts.push(minutes + ' menit');
                if (!parts.length) parts.push('Kurang dari 1 menit');
                remainingEl.textContent = parts.join(' ');
                remainingEl.classList.remove('hidden');
            }
        }

        // If cancellation_requested flag present, show notice
        if (typeof data.cancellation_requested !== 'undefined' && data.cancellation_requested) {
            // insert a small note if not present
            if (!row.querySelector('.cancellation-note')) {
                const note = document.createElement('div');
                note.className = 'mt-2 text-xs text-yellow-700 font-medium cancellation-note';
                note.textContent = 'Permohonan pembatalan terkirim';
                const badgeWrapper = row.querySelector('td .booking-badge') || row.querySelector('td');
                if (badgeWrapper) badgeWrapper.parentNode.appendChild(note);
            }
        }
    }

    // start polling (initial delay + interval)
    setTimeout(() => { pollStatuses(); setInterval(pollStatuses, POLL_INTERVAL_MS); }, 800);

    // Accessibility: delegations for upload/cancel buttons already present earlier
})();
</script>
@endpush

@endsection
