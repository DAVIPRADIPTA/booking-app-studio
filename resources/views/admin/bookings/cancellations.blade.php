<x-layouts.app :title="__('Permohonan Pembatalan - Admin')">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="max-w-7xl mx-auto py-8 px-4">
        <!-- Header (compact & single line) -->
        <div class="flex items-center justify-between mb-6 gap-4">
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-semibold text-gray-800">Permohonan Pembatalan</h1>
                <span class="text-sm text-gray-500">Kelola permohonan pembatalan pelanggan</span>
            </div>

            <div class="flex items-center gap-3">
                <div class="text-sm text-gray-600">Permohonan aktif:</div>
                <div id="badgePending" class="inline-flex items-center gap-2 px-3 py-1 bg-yellow-50 border border-yellow-100 text-yellow-800 rounded-full text-sm font-medium">
                    <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10 2a8 8 0 100 16 8 8 0 000-16zM9 8h2v5H9V8zm0 6h2v2H9v-2z"/></svg>
                    <span id="pendingCount" class="leading-none">{{ $pendingCount ?? 0 }}</span>
                </div>
            </div>
        </div>

        @if(session('successMessage'))
            <div class="mb-4 p-3 rounded bg-green-50 border border-green-100 text-green-800 text-sm">{{ session('successMessage') }}</div>
        @endif
        @if(session('errorMessage'))
            <div class="mb-4 p-3 rounded bg-red-50 border border-red-100 text-red-800 text-sm">{{ session('errorMessage') }}</div>
        @endif

        <!-- Card wrapper -->
        <div class="bg-white shadow sm:rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 w-14">#</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Booking</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Customer</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Tanggal / Waktu</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Paket / Total</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Alasan</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Diminta</th>
                            <th class="px-4 py-3 text-center text-sm font-medium text-gray-700 w-48">Aksi</th>
                        </tr>
                    </thead>

                    <tbody id="requestsTbody" class="bg-white divide-y divide-gray-200">
                        @forelse($requests as $req)
                            <tr id="row-{{ $req->id }}" class="hover:bg-gray-50">
                                <td class="px-4 py-4 text-sm text-gray-700 font-medium">#{{ $req->id }}</td>

                                <td class="px-4 py-4 text-sm text-gray-700">
                                    <div class="font-medium text-gray-800">#{{ $req->id }}</div>
                                    <a href="{{ route('bookings.show', $req->id) }}" class="text-xs text-blue-600 hover:underline">Lihat detail</a>
                                </td>

                                <td class="px-4 py-4 text-sm text-gray-700">
                                    <div class="font-medium">{{ $req->contact_name }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">{{ $req->whatsapp_number }}</div>
                                    @if($req->customer)
                                        <div class="text-xs text-gray-400 mt-1">Customer #{{ $req->customer->id ?? '-' }}</div>
                                    @endif
                                </td>

                                <td class="px-4 py-4 text-sm text-gray-700">
                                    <div>{{ \Carbon\Carbon::parse($req->booking_date)->translatedFormat('d M Y') }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">{{ $req->booking_time }}</div>
                                </td>

                                <td class="px-4 py-4 text-sm text-gray-700">
                                    <div class="font-medium">{{ $req->package_name }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">Rp {{ number_format($req->total_price ?? 0,0,',','.') }}</div>
                                </td>

                                <td class="px-4 py-4 text-sm text-gray-700 max-w-md">
                                    <div class="text-sm text-gray-600 whitespace-pre-line">{{ \Illuminate\Support\Str::limit($req->cancellation_reason ?: '-', 220) }}</div>
                                </td>

                                <td class="px-4 py-4 text-sm text-gray-700">
                                    <div class="text-sm text-gray-600">{{ $req->cancellation_requested_at ? $req->cancellation_requested_at->format('Y-m-d H:i') : '-' }}</div>
                                </td>

                                <td class="px-4 py-4 text-sm text-gray-700 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button
                                            type="button"
                                            class="btn-approve inline-flex items-center gap-2 px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-green-300"
                                            data-id="{{ $req->id }}"
                                            data-total="{{ $req->total_price ?? 0 }}"
                                            data-booking="{{ $req->id }}"
                                            data-package="{{ $req->package_name }}"
                                            title="Setujui pembatalan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            <span>Setujui</span>
                                        </button>

                                        <button
                                            type="button"
                                            class="btn-reject inline-flex items-center gap-2 px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-300"
                                            data-id="{{ $req->id }}"
                                            data-booking="{{ $req->id }}"
                                            title="Tolak pembatalan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            <span>Tolak</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-500">Belum ada permohonan pembatalan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t bg-white">
                {{ $requests->links() }}
            </div>
        </div>
    </div>

    {{-- APPROVE MODAL --}}
    <div id="approveModal" class="fixed inset-0 z-50 hidden items-center justify-center">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" aria-hidden="true"></div>

        <div class="relative w-full max-w-2xl mx-4">
            <form id="approveForm" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl overflow-hidden shadow-xl ring-1 ring-black/5">
                @csrf
                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Setujui Pembatalan & Proses Refund</h3>
                        <p class="text-sm text-gray-500 mt-0.5">Proses refund dan simpan bukti jika ada</p>
                    </div>
                    <button type="button" id="approveClose" class="text-gray-400 hover:text-gray-600" aria-label="Tutup">&times;</button>
                </div>

                <div class="px-6 py-5 space-y-4">
                    <input type="hidden" name="booking_id" id="approve_booking_id" value="">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs text-gray-400">Booking</div>
                            <div id="approveBookingLabel" class="font-medium text-gray-800 mt-1">#</div>
                            <div id="approvePackageLabel" class="text-xs text-gray-500 mt-1"></div>
                        </div>

                        <div>
                            <div class="text-xs text-gray-400">Total Booking</div>
                            <div id="approveTotalDisplay" class="text-lg font-semibold text-gray-900 mt-1">Rp 0</div>
                        </div>
                    </div>

                    <div>
                        <label for="refund_amount" class="block text-sm font-medium text-gray-700">Jumlah Refund (Rp)</label>
                        <input id="refund_amount" name="refund_amount" type="number" min="0" step="1" class="mt-1 block w-full border border-gray-200 rounded-md px-3 py-2 focus:ring-2 focus:ring-green-300" placeholder="Kosongkan = default 90%">
                        <p class="text-xs text-gray-400 mt-1">Biarkan kosong untuk default 90% dari total.</p>
                        <div id="approveError" class="text-xs text-red-600 mt-1 hidden"></div>
                    </div>

                    <div>
                        <label for="refund_proof" class="block text-sm font-medium text-gray-700">Bukti Refund (opsional, max 2MB)</label>
                        <div class="mt-2 flex items-center gap-3">
                            <label for="refund_proof" class="inline-flex items-center gap-2 cursor-pointer bg-gray-50 border border-gray-200 rounded-md px-3 py-2 text-sm hover:bg-gray-100">
                                <svg class="w-4 h-4 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 3v12"/></svg>
                                <span id="refundFileLabel" class="text-sm text-gray-700">Pilih file</span>
                            </label>
                            <input id="refund_proof" name="refund_proof" type="file" accept="image/*" class="sr-only">
                            <div id="refundFileInfo" class="text-sm text-gray-500"></div>
                        </div>

                        <div id="refundPreview" class="mt-3 hidden">
                            <div class="text-xs text-gray-500 mb-2">Preview:</div>
                            <img id="refundPreviewImg" src="#" alt="Preview Bukti Refund" class="max-h-40 rounded-md border object-contain shadow-sm">
                        </div>
                    </div>

                    <div>
                        <label for="approve_reason" class="block text-sm font-medium text-gray-700">Catatan / Alasan (opsional)</label>
                        <textarea id="approve_reason" name="cancellation_reason" rows="3" class="mt-1 block w-full border border-gray-200 rounded-md px-3 py-2"></textarea>
                    </div>
                </div>

                <div class="px-6 py-4 border-t flex justify-end gap-3">
                    <button type="button" id="approveCancelBtn" class="px-4 py-2 bg-white border rounded-md hover:bg-gray-50">Batal</button>
                    <button type="submit" id="approveSubmitBtn" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Setujui & Refund</button>
                </div>
            </form>
        </div>
    </div>

    {{-- REJECT MODAL --}}
    <div id="rejectModal" class="fixed inset-0 z-50 hidden items-center justify-center">
        <div class="absolute inset-0 bg-black/40" aria-hidden="true"></div>

        <div class="relative w-full max-w-lg mx-4">
            <form id="rejectForm" method="POST" class="bg-white rounded-2xl overflow-hidden shadow-xl ring-1 ring-black/5">
                @csrf
                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Tolak Permohonan Pembatalan</h3>
                        <p class="text-sm text-gray-500 mt-0.5">Kirim alasan penolakan ke catatan booking</p>
                    </div>
                    <button type="button" id="rejectClose" class="text-gray-400 hover:text-gray-600" aria-label="Tutup">&times;</button>
                </div>

                <div class="px-6 py-5 space-y-4">
                    <input type="hidden" name="booking_id" id="reject_booking_id" value="">
                    <div>
                        <div class="text-xs text-gray-400">Booking</div>
                        <div id="rejectBookingLabel" class="font-medium text-gray-800 mt-1">#</div>
                    </div>

                    <div>
                        <label for="rejection_reason" class="block text-sm font-medium text-gray-700">Alasan Penolakan</label>
                        <textarea id="rejection_reason" name="rejection_reason" rows="4" required class="mt-1 block w-full border border-gray-200 rounded-md px-3 py-2" placeholder="Tulis alasan penolakan untuk customer..."></textarea>
                        <div id="rejectError" class="text-xs text-red-600 mt-1 hidden"></div>
                    </div>
                </div>

                <div class="px-6 py-4 border-t flex justify-end gap-3">
                    <button type="button" id="rejectCancelBtn" class="px-4 py-2 bg-white border rounded-md hover:bg-gray-50">Batal</button>
                    <button type="submit" id="rejectSubmitBtn" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Tolak</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Toast container --}}
    <div id="toastContainer" class="fixed z-50 right-4 top-4 flex flex-col gap-2" aria-live="polite"></div>

    @push('scripts')
    <script>
    (function () {
        const baseUrl = "{{ url('bookings') }}";
        const countUrl = "{{ route('bookings.cancellations.count') }}";
        const metaCsrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

        function showToast(message, type = 'info') {
            const colors = { info: 'bg-blue-600 text-white', success: 'bg-green-600 text-white', error: 'bg-red-600 text-white', warn: 'bg-yellow-600 text-white' };
            const el = document.createElement('div');
            el.className = `px-4 py-2 rounded shadow ${colors[type] || colors.info}`;
            el.textContent = message;
            document.getElementById('toastContainer').appendChild(el);
            setTimeout(()=> { el.style.opacity = '0'; setTimeout(()=> el.remove(), 300); }, 3500);
        }

        function toggleModal(modalEl, show = true) {
            if (!modalEl) return;
            if (show) { modalEl.classList.remove('hidden'); modalEl.classList.add('flex'); } 
            else { modalEl.classList.remove('flex'); modalEl.classList.add('hidden'); }
        }

        // Prepare approve buttons
        document.querySelectorAll('.btn-approve').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                const total = Number(btn.dataset.total || 0);
                const pkg = btn.dataset.package || '';
                const booking = btn.dataset.booking || id;

                document.getElementById('approve_booking_id').value = id;
                document.getElementById('approveBookingLabel').textContent = '#' + booking;
                document.getElementById('approvePackageLabel').textContent = pkg;
                document.getElementById('approveTotalDisplay').textContent = 'Rp ' + total.toLocaleString('id-ID');

                // reset inputs
                document.getElementById('refund_amount').value = '';
                document.getElementById('approve_reason').value = '';
                document.getElementById('refund_proof').value = '';
                document.getElementById('refundFileLabel').textContent = 'Pilih file';
                document.getElementById('refundFileInfo').textContent = '';
                document.getElementById('refundPreview').classList.add('hidden');

                // set form action
                const f = document.getElementById('approveForm');
                f.action = `${baseUrl}/${id}/process-cancellation`;

                toggleModal(document.getElementById('approveModal'), true);
            });
        });

        // Prepare reject buttons
        document.querySelectorAll('.btn-reject').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                const booking = btn.dataset.booking || id;

                document.getElementById('reject_booking_id').value = id;
                document.getElementById('rejectBookingLabel').textContent = '#' + booking;
                document.getElementById('rejection_reason').value = '';

                const f = document.getElementById('rejectForm');
                f.action = `${baseUrl}/${id}/reject-cancellation`;

                toggleModal(document.getElementById('rejectModal'), true);
            });
        });

        // Close modal handlers
        ['approveClose','approveCancelBtn'].forEach(id => { const el = document.getElementById(id); if (el) el.addEventListener('click', ()=> toggleModal(document.getElementById('approveModal'), false)); });
        ['rejectClose','rejectCancelBtn'].forEach(id => { const el = document.getElementById(id); if (el) el.addEventListener('click', ()=> toggleModal(document.getElementById('rejectModal'), false)); });

        // Refund file preview & size validation (2MB)
        const refundInput = document.getElementById('refund_proof');
        refundInput?.addEventListener('change', function () {
            const f = this.files && this.files[0];
            const label = document.getElementById('refundFileLabel');
            const info = document.getElementById('refundFileInfo');
            const preview = document.getElementById('refundPreview');
            const previewImg = document.getElementById('refundPreviewImg');
            const errEl = document.getElementById('approveError');

            if (errEl) { errEl.classList.add('hidden'); errEl.textContent = ''; }
            if (!f) {
                label.textContent = 'Pilih file';
                info.textContent = '';
                preview.classList.add('hidden');
                previewImg.src = '#';
                return;
            }

            if (!f.type.startsWith('image/')) {
                if (errEl) { errEl.textContent = 'File harus berupa gambar.'; errEl.classList.remove('hidden'); }
                this.value = '';
                return;
            }
            if (f.size > 2 * 1024 * 1024) {
                if (errEl) { errEl.textContent = 'Ukuran file maksimal 2MB.'; errEl.classList.remove('hidden'); }
                this.value = '';
                return;
            }

            label.textContent = f.name;
            info.textContent = Math.round(f.size / 1024) + ' KB';
            previewImg.src = URL.createObjectURL(f);
            preview.classList.remove('hidden');
        });

        // Approve form (AJAX)
        const approveFormEl = document.getElementById('approveForm');
        if (approveFormEl) {
            approveFormEl.addEventListener('submit', async (e) => {
                e.preventDefault();
                const form = e.target;
                const action = form.action;
                if (!action) return showToast('Endpoint tidak ditemukan', 'error');

                const fd = new FormData(form);
                if (!fd.has('_token')) fd.append('_token', metaCsrf);

                // client-side refund validation if provided
                const refundVal = fd.get('refund_amount');
                const totalStr = document.getElementById('approveTotalDisplay').textContent.replace(/[^\d]/g, '');
                const total = Number(totalStr || 0);
                if (refundVal && (Number(refundVal) < 0 || Number(refundVal) > total)) {
                    const errEl = document.getElementById('approveError'); if (errEl) { errEl.textContent = 'Nilai refund tidak valid.'; errEl.classList.remove('hidden'); }
                    return showToast('Nilai refund tidak valid', 'error');
                }

                const submitBtn = document.getElementById('approveSubmitBtn');
                submitBtn.disabled = true; submitBtn.textContent = 'Memproses...';

                try {
                    const res = await fetch(action, { method: 'POST', headers: { 'Accept': 'application/json' }, body: fd });
                    if (res.ok) {
                        // success: update row UI + decrement badge
                        const bookingId = document.getElementById('approve_booking_id').value;
                        const row = document.getElementById('row-' + bookingId);
                        if (row) {
                            const notesCell = row.querySelectorAll('td')[5];
                            const refundValLocal = fd.get('refund_amount') || '';
                            let refundText = refundValLocal ? 'Refund: Rp ' + Number(refundValLocal).toLocaleString('id-ID') : 'Refund: (default 90% dari total)';
                            if (notesCell) {
                                notesCell.innerHTML = `<div class="text-sm text-green-700 font-medium">Dibatalkan (refund)</div><div class="text-xs text-gray-500 mt-1">${refundText}</div>` + (fd.get('cancellation_reason') ? `<div class="text-xs text-gray-500 mt-1">${fd.get('cancellation_reason')}</div>` : '');
                            }
                            row.querySelectorAll('.btn-approve, .btn-reject').forEach(x => x.remove());
                        }
                        const badge = document.getElementById('pendingCount');
                        if (badge) badge.textContent = Math.max(0, Number(badge.textContent||0) - 1);

                        showToast('Permohonan disetujui. Refund diproses.', 'success');
                        toggleModal(document.getElementById('approveModal'), false);
                        return;
                    }

                    const txt = await res.text();
                    try { const j = JSON.parse(txt); showToast(j.message || 'Gagal memproses', 'error'); } 
                    catch { showToast('Server error saat memproses', 'error'); }
                } catch (err) {
                    console.error(err);
                    showToast('Gagal menghubungi server', 'error');
                } finally {
                    submitBtn.disabled = false; submitBtn.textContent = 'Setujui & Refund';
                }
            });
        }

        // Reject form (AJAX)
        const rejectFormEl = document.getElementById('rejectForm');
        if (rejectFormEl) {
            rejectFormEl.addEventListener('submit', async (e) => {
                e.preventDefault();
                const form = e.target;
                const action = form.action;
                if (!action) return showToast('Endpoint tidak ditemukan', 'error');

                const fd = new FormData(form);
                if (!fd.has('_token')) fd.append('_token', metaCsrf);

                if (!fd.get('rejection_reason') || !fd.get('rejection_reason').toString().trim()) {
                    const errEl = document.getElementById('rejectError'); if (errEl) { errEl.textContent = 'Alasan penolakan diperlukan.'; errEl.classList.remove('hidden'); }
                    return showToast('Alasan penolakan diperlukan', 'error');
                }

                const submitBtn = document.getElementById('rejectSubmitBtn');
                submitBtn.disabled = true; submitBtn.textContent = 'Memproses...';

                try {
                    const res = await fetch(action, { method: 'POST', headers: { 'Accept': 'application/json' }, body: fd });
                    if (res.ok) {
                        const bookingId = document.getElementById('reject_booking_id').value;
                        const row = document.getElementById('row-' + bookingId);
                        if (row) {
                            const notesCell = row.querySelectorAll('td')[5];
                            if (notesCell) notesCell.innerHTML = `<div class="text-sm text-red-600 font-medium">Permohonan ditolak</div><div class="text-xs text-gray-500 mt-1">${fd.get('rejection_reason')}</div>`;
                            row.querySelectorAll('.btn-approve, .btn-reject').forEach(x => x.remove());
                        }
                        const badge = document.getElementById('pendingCount');
                        if (badge) badge.textContent = Math.max(0, Number(badge.textContent||0) - 1);

                        showToast('Permohonan ditolak.', 'success');
                        toggleModal(document.getElementById('rejectModal'), false);
                        return;
                    }

                    const txt = await res.text();
                    try { const j = JSON.parse(txt); showToast(j.message || 'Gagal menolak', 'error'); } 
                    catch { showToast('Server error saat menolak', 'error'); }
                } catch (err) {
                    console.error(err);
                    showToast('Gagal menghubungi server', 'error');
                } finally {
                    submitBtn.disabled = false; submitBtn.textContent = 'Tolak';
                }
            });
        }

        // Optional: poll pending count every 30s
        async function pollCount() {
            try {
                const r = await fetch(countUrl, { headers: { 'Accept': 'application/json' }});
                if (r.ok) {
                    const j = await r.json();
                    if (typeof j.count !== 'undefined') {
                        const el = document.getElementById('pendingCount');
                        if (el) el.textContent = j.count;
                    }
                }
            } catch (e) { /* ignore */ }
        }
        setInterval(pollCount, 30000);
        pollCount();

        // Close modals with Esc
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                toggleModal(document.getElementById('approveModal'), false);
                toggleModal(document.getElementById('rejectModal'), false);
            }
        });
    })();
    </script>
    @endpush
</x-layouts.app>
