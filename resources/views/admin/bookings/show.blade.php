<x-layouts.app :title="'Detail Pesanan #' . $booking->id">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="max-w-7xl mx-auto px-4 py-8">
        {{-- Hero (NOTE: tombol back di header biru sudah dihapus) --}}
        <div class="rounded-2xl overflow-hidden mb-6 shadow-sm">
            <div class="relative bg-gradient-to-r from-indigo-600 to-blue-500 px-6 py-6 sm:py-8">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white">Detail Pesanan #{{ $booking->id }}</h1>
                    <p class="mt-1 text-sm text-indigo-100">Rincian lengkap pesanan, status, bukti pembayaran dan aksi admin.</p>
                </div>
            </div>
        </div>

        {{-- Top actions (mobile-friendly): hanya tombol kembali di area normal (tetap ada) --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <a href="{{ route('bookings.index') }}" class="inline-flex items-center gap-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg w-full sm:w-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Daftar Pesanan</span>
                </a>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                @if($booking->status === \App\Models\Booking::STATUS_BOOKED)
                    <a href="{{ route('bookings.edit', $booking->id) }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg w-full sm:w-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5" />
                        </svg>
                        <span>Edit Pesanan</span>
                    </a>
                @endif
            </div>
        </div>

        {{-- Alerts --}}
        @if(session('successMessage'))
            <div class="mb-4 px-4 py-3 bg-green-50 text-green-800 rounded">{{ session('successMessage') }}</div>
        @endif
        @if(session('errorMessage'))
            <div class="mb-4 px-4 py-3 bg-red-50 text-red-800 rounded">{{ session('errorMessage') }}</div>
        @endif

        {{-- Layout utama --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left / main --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Informasi Pelanggan --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Pelanggan</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-500 text-sm">Nama Kontak</p>
                            <p class="font-medium">{{ $booking->contact_name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Nomor WhatsApp</p>
                            <p class="font-medium">{{ $booking->whatsapp_number }}</p>
                        </div>

                        @if($booking->customer)
                            <div>
                                <p class="text-gray-500 text-sm">Akun Customer</p>
                                <p class="font-medium">#{{ $booking->customer->id }} — {{ $booking->customer->name }}</p>
                            </div>
                        @endif

                        @if($booking->baby_name)
                            <div>
                                <p class="text-gray-500 text-sm">Nama Bayi</p>
                                <p class="font-medium">{{ $booking->baby_name }}</p>
                            </div>
                        @endif

                        @if($booking->baby_age)
                            <div>
                                <p class="text-gray-500 text-sm">Usia Bayi</p>
                                <p class="font-medium">{{ $booking->baby_age }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Detail Booking --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Detail Booking</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-500 text-sm">Sesi</p>
                            <p class="font-medium">{{ $booking->formatted_session_name ?? $booking->session_name }}</p>
                        </div>

                        <div>
                            <p class="text-gray-500 text-sm">Tanggal</p>
                            <p class="font-medium">{{ \Carbon\Carbon::parse($booking->booking_date)->translatedFormat('d F Y') }}</p>
                        </div>

                        <div>
                            <p class="text-gray-500 text-sm">Jam</p>
                            <p class="font-medium">{{ $booking->booking_time }}</p>
                        </div>

                        <div>
                            <p class="text-gray-500 text-sm">Paket</p>
                            <p class="font-medium">{{ $booking->package_name }}</p>
                        </div>

                        <div class="md:col-span-2">
                            <p class="text-gray-500 text-sm">Catatan</p>
                            <p class="font-medium whitespace-pre-line">{{ $booking->notes ?: '-' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Selected items --}}
                @if(!empty($booking->selected_backgrounds) || !empty($booking->selected_extra_items))
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Item yang Dipilih</h2>

                        @if(!empty($booking->selected_backgrounds))
                            <div class="mb-4">
                                <h3 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                    Background
                                    <span class="text-xs text-gray-400">({{ count($booking->selected_backgrounds) }})</span>
                                </h3>

                                <div class="flex flex-wrap gap-3">
                                    @foreach($booking->selected_backgrounds as $bg)
                                        @php
                                            $img = $bg['image'] ?? null;
                                            $imgUrl = $img ? asset('storage/' . ltrim($img, '/')) : null;
                                        @endphp
                                        <div class="w-36 rounded-lg overflow-hidden border bg-gray-50">
                                            @if($imgUrl)
                                                <img loading="lazy" src="{{ $imgUrl }}" alt="{{ $bg['name'] ?? 'Background' }}" class="w-full h-20 object-cover">
                                            @else
                                                <div class="h-20 flex items-center justify-center text-xs text-gray-500 px-2">{{ $bg['name'] ?? '-' }}</div>
                                            @endif
                                            <div class="p-2 text-xs text-gray-700">{{ $bg['name'] ?? '-' }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if(!empty($booking->selected_extra_items))
                            <div>
                                <h3 class="font-semibold text-gray-700 mb-3">Extra Items</h3>
                                <div class="space-y-2">
                                    @foreach($booking->selected_extra_items as $it)
                                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded border">
                                            <div class="text-sm text-gray-700">{{ $it['name'] ?? '-' }}</div>
                                            <div class="text-sm text-gray-600">Rp {{ number_format($it['price'] ?? 0,0,',','.') }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Right column --}}
            <div class="space-y-6">
                {{-- PRIORITAS: Panel Permohonan Pembatalan (Hanya tampil jika ada permohonan dari customer) --}}
                @if($booking->cancellation_requested)
                    <div id="cancellationPanel" class="bg-white rounded-2xl shadow-lg border border-yellow-200 p-6">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 110 20 10 10 0 010-20z"/>
                                    </svg>
                                </div>
                            </div>

                            <div class="min-w-0">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <div class="text-sm font-semibold text-yellow-800">Permohonan Pembatalan Aktif</div>
                                        <div class="text-xs text-gray-600 mt-1">{{ \Illuminate\Support\Str::limit($booking->cancellation_reason ?: '-', 200) }}</div>
                                        <div class="text-xs text-gray-500 mt-2">Diajukan: {{ optional($booking->cancellation_requested_at)->format('Y-m-d H:i') }}</div>
                                    </div>
                                </div>

                                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <button id="btnOpenApprove" type="button" class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-md">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        Proses Refund
                                    </button>

                                    <button id="btnOpenReject" type="button" class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow-md">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Tolak Permohonan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Jika tidak ada permohonan, tampilkan pesan singkat (TIDAK ADA tombol 'Tandai Permohonan') --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 110 20 10 10 0 010-20z"/>
                                    </svg>
                                </div>
                            </div>

                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-gray-800">Tidak ada permohonan pembatalan</div>
                                <div class="text-xs text-gray-500 mt-1">Semua permohonan pembatalan hanya bisa diajukan oleh customer. Admin hanya bisa menerima/proses/menolak.</div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Status, Payment, Admin actions tetap sama --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Status Pesanan</h2>

                    @php
                        $map = [
                            'waiting_payment' => ['label'=>'Menunggu Pembayaran','color'=>'bg-yellow-50 text-yellow-800 border-yellow-100','dot'=>'bg-yellow-500'],
                            'pending_verification' => ['label'=>'Menunggu Verifikasi','color'=>'bg-blue-50 text-blue-800 border-blue-100','dot'=>'bg-blue-600'],
                            'booked' => ['label'=>'Dikonfirmasi','color'=>'bg-green-50 text-green-800 border-green-100','dot'=>'bg-green-600'],
                            'completed' => ['label'=>'Selesai','color'=>'bg-gray-50 text-gray-700 border-gray-100','dot'=>'bg-gray-500'],
                            'cancelled' => ['label'=>'Dibatalkan','color'=>'bg-red-50 text-red-800 border-red-100','dot'=>'bg-red-600'],
                        ];
                        $badge = $map[$booking->status] ?? ['label'=>ucfirst($booking->status),'color'=>'bg-gray-50 text-gray-700 border-gray-100','dot'=>'bg-gray-400'];
                    @endphp

                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <span id="statusBadge" class="inline-flex items-center gap-3 px-3 py-1 rounded-full text-sm font-semibold border {{ $badge['color'] }}">
                                <span class="w-2 h-2 rounded-full {{ $badge['dot'] }}"></span>
                                {{ $badge['label'] }}
                            </span>
                        </div>

                        @if($booking->refund_amount && $booking->status === \App\Models\Booking::STATUS_CANCELLED)
                            <div class="text-sm text-gray-700">Refund: <strong id="refundAmountDisplay">{{ $booking->formattedRefund() }}</strong></div>
                        @endif
                    </div>

                    @if($booking->status === \App\Models\Booking::STATUS_WAITING_PAYMENT)
                        <div class="mt-4 p-3 bg-amber-50 rounded-lg border border-amber-100 text-sm text-amber-700">
                            Customer memiliki <strong id="remainingTime">{{ $booking->getRemainingTimeFormatted() }}</strong> untuk menyelesaikan pembayaran.
                        </div>
                    @endif

                    @if($booking->status === \App\Models\Booking::STATUS_CANCELLED && $booking->cancellation_reason)
                        <div class="mt-4 p-3 bg-red-50 rounded-lg border border-red-100 text-sm text-red-700">
                            <strong>Dibatalkan:</strong> <span id="cancelReasonDisplay">{{ $booking->cancellation_reason }}</span>
                        </div>
                    @endif
                </div>

                {{-- Payment card --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Detail Pembayaran</h2>

                    <div class="space-y-3">
                        <div>
                            <p class="text-gray-500 text-sm">Metode</p>
                            <p class="font-medium capitalize">{{ $booking->payment_method }}</p>
                        </div>

                        <div>
                            <p class="text-gray-500 text-sm">Total</p>
                            <p class="font-bold text-xl">Rp {{ number_format($booking->total_price ?? 0,0,',','.') }}</p>
                        </div>

                        @if($booking->payment_method === 'transfer')
                            @if($booking->payment_proof && $booking->paymentProofExists())
                                <div>
                                    <p class="text-gray-500 text-sm mb-2">Bukti Pembayaran</p>
                                    <a href="{{ $booking->payment_proof_url }}" target="_blank" class="block rounded overflow-hidden border">
                                        <img src="{{ $booking->payment_proof_url }}" alt="Bukti Pembayaran" class="w-full h-48 object-contain bg-white">
                                    </a>
                                    <div class="mt-2 flex gap-2">
                                        <a href="{{ $booking->payment_proof_url }}" target="_blank" class="inline-flex items-center px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm">Lihat / Download</a>
                                    </div>
                                </div>
                            @else
                                <div class="p-3 bg-yellow-50 rounded-lg border border-yellow-100 text-sm text-yellow-700">Transfer belum diunggah oleh customer.</div>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- Admin actions (single place for actions; no 'Tandai Permohonan' here) --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Aksi Admin</h2>

                    <div class="space-y-3">
                        @if($booking->status === \App\Models\Booking::STATUS_WAITING_PAYMENT)
                            <form method="POST" action="{{ route('bookings.forceCancel', $booking->id) }}" class="js-prompt-reason" novalidate>@csrf
                                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded-lg">Batalkan Sekarang</button>
                            </form>
                        @endif

                        @if($booking->status === \App\Models\Booking::STATUS_PENDING_VERIFICATION)
                            <form method="POST" action="{{ route('bookings.verifyPayment', $booking->id) }}" novalidate>@csrf
                                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 rounded-lg">Verifikasi & Konfirmasi</button>
                            </form>

                            <form method="POST" action="{{ route('bookings.cancelBooking', $booking->id) }}" class="js-prompt-reason" novalidate>@csrf
                                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded-lg">Batalkan Pesanan</button>
                            </form>
                        @endif

                        @if($booking->status === \App\Models\Booking::STATUS_BOOKED)
                            <form method="POST" action="{{ route('bookings.completeBooking', $booking->id) }}" novalidate>@csrf
                                <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 rounded-lg">Tandai Selesai</button>
                            </form>

                            <form method="POST" action="{{ route('bookings.cancelBooking', $booking->id) }}" class="js-prompt-reason" novalidate>@csrf
                                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded-lg">Batalkan Pesanan</button>
                            </form>
                        @endif

                        @if(in_array($booking->status, [\App\Models\Booking::STATUS_COMPLETED, \App\Models\Booking::STATUS_CANCELLED]))
                            <form method="POST" action="{{ route('bookings.destroy', $booking->id) }}" class="js-confirm-delete" novalidate>
                                @csrf @method('DELETE')
                                <button type="submit" class="w-full bg-red-700 hover:bg-red-800 text-white font-semibold py-2 rounded-lg">Hapus Pesanan</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- APPROVE Modal (sama seperti sebelumnya) --}}
        <div id="approveModal" style="display:none" class="fixed inset-0 z-50 items-center justify-center bg-black bg-opacity-40 px-4">
            <div class="bg-white rounded-lg w-full max-w-lg shadow-lg overflow-hidden">
                <form id="approveForm" method="POST" enctype="multipart/form-data" action="{{ route('bookings.processCancellation', $booking->id) }}">
                    @csrf
                    <div class="p-4 border-b flex items-center justify-between">
                        <h3 class="text-lg font-medium">Setujui Pembatalan & Proses Refund</h3>
                        <button type="button" id="approveClose" class="text-gray-500 hover:text-gray-700" aria-label="Tutup">&times;</button>
                    </div>

                    <div class="p-4 space-y-3">
                        <div>
                            <div class="text-sm text-gray-600">Booking</div>
                            <div class="font-medium">#{{ $booking->id }} — {{ $booking->contact_name }}</div>
                            <div class="text-xs text-gray-500">{{ $booking->package_name }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Total Booking</label>
                            <div id="approveTotalDisplay" class="mt-1 text-lg font-semibold text-gray-800">Rp {{ number_format($booking->total_price ?? 0,0,',','.') }}</div>
                        </div>

                        <div>
                            <label for="refund_amount" class="block text-sm font-medium text-gray-700">Jumlah Refund (Rp)</label>
                            <input id="refund_amount" name="refund_amount" type="number" min="0" step="1"
                                value="{{ $booking->getSuggestedRefundAmount(0.9) }}"
                                class="mt-1 block w-full border rounded px-3 py-2">
                            <p class="text-xs text-gray-400 mt-1">Kosongkan untuk default 90% dari total.</p>
                            <div id="approveError" class="text-xs text-red-600 mt-1 hidden"></div>
                        </div>

                        <div>
                            <label for="refund_proof" class="block text-sm font-medium text-gray-700">Bukti Refund (opsional, max 2MB)</label>
                            <input id="refund_proof" name="refund_proof" type="file" accept="image/*" class="mt-1 block w-full">
                            <div id="refundPreview" class="mt-2 hidden">
                                <div class="text-xs text-gray-500 mb-1">Preview:</div>
                                <img id="refundPreviewImg" src="#" alt="Preview" class="max-h-40 rounded shadow object-contain">
                            </div>
                        </div>

                        <div>
                            <label for="approve_note" class="block text-sm font-medium text-gray-700">Catatan (opsional)</label>
                            <textarea id="approve_note" name="cancellation_reason" rows="3" class="mt-1 block w-full border rounded px-3 py-2" placeholder="Catatan yang akan disimpan..."></textarea>
                        </div>
                    </div>

                    <div class="p-4 border-t flex justify-end gap-2">
                        <button type="button" id="approveCancelBtn" class="px-4 py-2 bg-gray-100 rounded hover:bg-gray-200">Batal</button>
                        <button type="submit" id="approveSubmitBtn" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Setujui & Refund</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- REJECT Modal --}}
        <div id="rejectModal" style="display:none" class="fixed inset-0 z-50 items-center justify-center bg-black bg-opacity-40 px-4">
            <div class="bg-white rounded-lg w-full max-w-md shadow-lg overflow-hidden">
                <form id="rejectForm" method="POST" action="{{ route('bookings.cancellations.reject', $booking->id) }}">
                    @csrf
                    <div class="p-4 border-b flex items-center justify-between">
                        <h3 class="text-lg font-medium">Tolak Permohonan Pembatalan</h3>
                        <button type="button" id="rejectClose" class="text-gray-500 hover:text-gray-700" aria-label="Tutup">&times;</button>
                    </div>

                    <div class="p-4 space-y-3">
                        <div>
                            <label for="rejection_reason" class="block text-sm font-medium text-gray-700">Alasan Penolakan</label>
                            <textarea id="rejection_reason" name="rejection_reason" rows="4" required class="mt-1 block w-full border rounded px-3 py-2" placeholder="Tulis alasan penolakan untuk customer..."></textarea>
                            <div id="rejectError" class="text-xs text-red-600 mt-1 hidden"></div>
                        </div>
                    </div>

                    <div class="p-4 border-t flex justify-end gap-2">
                        <button type="button" id="rejectCancelBtn" class="px-4 py-2 bg-gray-100 rounded hover:bg-gray-200">Batal</button>
                        <button type="submit" id="rejectSubmitBtn" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Tolak</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Toast container --}}
        <div id="toastContainer" class="fixed z-50 right-4 top-4 flex flex-col gap-2" aria-live="polite"></div>

        {{-- Inline JS (disesuaikan: HAPUS injeksi tombol 'Tandai Permohonan') --}}
        <script>
        (function () {
            document.addEventListener('DOMContentLoaded', function () {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content') || '';
                const approveModal = document.getElementById('approveModal');
                const rejectModal = document.getElementById('rejectModal');

                function openModal(el){ if(!el) return; el.style.display = 'flex'; el.setAttribute('aria-hidden','false'); }
                function closeModal(el){ if(!el) return; el.style.display = 'none'; el.setAttribute('aria-hidden','true'); }

                function showToast(msg, type='info') {
                    const colors = { info: 'bg-blue-600 text-white', success: 'bg-green-600 text-white', error: 'bg-red-600 text-white' };
                    const box = document.createElement('div');
                    box.className = `px-4 py-2 rounded shadow ${colors[type] || colors.info}`;
                    box.innerText = msg;
                    document.getElementById('toastContainer').appendChild(box);
                    setTimeout(()=> { box.style.opacity = 0; setTimeout(()=> box.remove(), 300); }, 3500);
                }

                // handlers open / close
                document.getElementById('btnOpenApprove')?.addEventListener('click', ()=> openModal(approveModal));
                document.getElementById('btnOpenReject')?.addEventListener('click', ()=> openModal(rejectModal));
                ['approveClose','approveCancelBtn'].forEach(id => document.getElementById(id)?.addEventListener('click', ()=> closeModal(approveModal)));
                ['rejectClose','rejectCancelBtn'].forEach(id => document.getElementById(id)?.addEventListener('click', ()=> closeModal(rejectModal)));

                // preview & validation for refund image
                const refundInput = document.getElementById('refund_proof');
                const refundPreview = document.getElementById('refundPreview');
                const refundPreviewImg = document.getElementById('refundPreviewImg');
                refundInput?.addEventListener('change', function () {
                    const f = this.files && this.files[0];
                    const errEl = document.getElementById('approveError');
                    if (errEl) { errEl.classList.add('hidden'); errEl.innerText = ''; }
                    if (!f) { refundPreview.classList.add('hidden'); refundPreviewImg.src = '#'; return; }
                    if (!f.type.startsWith('image/')) {
                        if (errEl) { errEl.innerText = 'File harus berupa gambar.'; errEl.classList.remove('hidden'); }
                        this.value = '';
                        return;
                    }
                    if (f.size > 2 * 1024 * 1024) {
                        if (errEl) { errEl.innerText = 'Ukuran file maksimal 2MB.'; errEl.classList.remove('hidden'); }
                        this.value = ''; refundPreview.classList.add('hidden'); return;
                    }
                    refundPreviewImg.src = URL.createObjectURL(f);
                    refundPreview.classList.remove('hidden');
                });

                // universal submit helper using fetch + CSRF
                async function submitFormWithFetch(form, onSuccessMsg = '') {
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) { submitBtn.disabled = true; submitBtn.__origText = submitBtn.textContent; submitBtn.textContent = 'Memproses...'; }
                    const fd = new FormData(form);
                    if (!fd.has('_token')) fd.append('_token', csrfToken);

                    try {
                        const res = await fetch(form.action, {
                            method: form.method || 'POST',
                            body: fd,
                            credentials: 'same-origin',
                            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                        });

                        const ct = res.headers.get('content-type') || '';
                        let payload = null;
                        if (ct.includes('application/json')) payload = await res.json();
                        else payload = await res.text();

                        if (res.ok) {
                            showToast((payload && payload.message) ? payload.message : onSuccessMsg || 'Berhasil', 'success');
                            setTimeout(()=> location.reload(), 700);
                            return { ok: true, payload };
                        }

                        if (payload && payload.errors) {
                            const first = Object.values(payload.errors)[0];
                            const msg = Array.isArray(first) ? first[0] : first;
                            return { ok: false, message: msg };
                        }

                        return { ok: false, message: (payload && payload.message) ? payload.message : 'Terjadi kesalahan server' };
                    } catch (err) {
                        console.error(err);
                        return { ok: false, message: 'Gagal menghubungi server' };
                    } finally {
                        if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = submitBtn.__origText || 'Kirim'; }
                    }
                }

                // approve form submit (reload on success to sync state)
                const approveForm = document.getElementById('approveForm');
                approveForm?.addEventListener('submit', async function (e) {
                    e.preventDefault();
                    const errEl = document.getElementById('approveError');
                    if (errEl) { errEl.classList.add('hidden'); errEl.innerText = ''; }

                    const refundVal = Number(approveForm.querySelector('#refund_amount')?.value || 0);
                    const total = Number({{ (int)($booking->total_price ?? 0) }});
                    if (refundVal < 0 || refundVal > total) {
                        if (errEl) { errEl.innerText = 'Nilai refund tidak valid (harus antara 0 dan total).'; errEl.classList.remove('hidden'); }
                        showToast('Nilai refund tidak valid', 'error');
                        return;
                    }

                    const result = await submitFormWithFetch(approveForm, 'Pembatalan diproses. Refund disimpan.');
                    if (!result.ok) {
                        if (errEl) { errEl.innerText = result.message || 'Terjadi kesalahan'; errEl.classList.remove('hidden'); }
                        showToast(result.message || 'Gagal memproses', 'error');
                        return;
                    }
                    closeModal(approveModal);
                });

                // reject form submit (AJAX but no injection of "Tandai Permohonan")
                const rejectForm = document.getElementById('rejectForm');
                rejectForm?.addEventListener('submit', async function (e) {
                    e.preventDefault();
                    const errEl = document.getElementById('rejectError');
                    if (errEl) { errEl.classList.add('hidden'); errEl.innerText = ''; }

                    if (!rejectForm.querySelector('#rejection_reason')?.value.trim()) {
                        if (errEl) { errEl.innerText = 'Alasan penolakan diperlukan.'; errEl.classList.remove('hidden'); }
                        showToast('Alasan penolakan diperlukan', 'error');
                        return;
                    }

                    const result = await submitFormWithFetch(rejectForm, 'Permohonan pembatalan ditolak.');
                    if (!result.ok) {
                        if (errEl) { errEl.innerText = result.message || 'Terjadi kesalahan'; errEl.classList.remove('hidden'); }
                        showToast(result.message || 'Gagal menolak', 'error');
                        return;
                    }

                    // After success, remove the cancellation panel from DOM (no new button inserted)
                    document.getElementById('cancellationPanel')?.remove();
                    closeModal(rejectModal);
                });

                // prompt for reason on immediate cancel/force-cancel forms
                document.querySelectorAll('form.js-prompt-reason').forEach(form => {
                    form.addEventListener('submit', function (ev) {
                        ev.preventDefault();
                        const reason = prompt('Masukkan alasan pembatalan (opsional). Tekan Cancel untuk membatalkan aksi.');
                        if (reason === null) return;
                        if (reason.trim()) {
                            const input = document.createElement('input'); input.type = 'hidden'; input.name = 'cancellation_reason'; input.value = reason.trim();
                            form.appendChild(input);
                        }
                        form.submit();
                    });
                });

                // confirm delete
                document.querySelectorAll('form.js-confirm-delete').forEach(form=>{
                    form.addEventListener('submit', function (e) {
                        if (!confirm('Yakin ingin menghapus pesanan ini? Tindakan ini tidak dapat dikembalikan.')) e.preventDefault();
                    });
                });

                // ESC to close modals
                document.addEventListener('keydown', (ev) => {
                    if (ev.key === 'Escape') { closeModal(approveModal); closeModal(rejectModal); }
                });
            });
        })();
        </script>
</x-layouts.app>
