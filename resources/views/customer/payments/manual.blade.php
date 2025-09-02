{{-- resources/views/bookings/payment_manual.blade.php --}}
@extends('layouts.app')

@section('title', 'Pembayaran Manual')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="md:flex">
            {{-- LEFT: instruksi (no logo, clean) --}}
            <div class="md:w-1/2 bg-gradient-to-b from-white via-gray-50 to-gray-50 p-8">
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Instruksi Pembayaran</h1>
                    <p class="text-sm text-gray-500">Pembayaran manual via transfer bank — unggah bukti setelah transfer.</p>
                </div>

                {{-- Important amount card --}}
                <div class="rounded-xl border border-gray-100 p-4 bg-white shadow-sm mb-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-xs text-gray-400">Nama Kontak</div>
                            <div class="font-medium text-gray-800">{{ $booking->contact_name }}</div>
                        </div>

                        <div class="text-right">
                            <div class="text-xs text-gray-400">Total Bayar</div>
                            <div id="amountDisplay" class="text-2xl font-extrabold text-indigo-600">Rp {{ number_format($booking->total_price ?? 0,0,',','.') }}</div>
                            <div class="text-xs text-gray-400 mt-1">Tagihan #{{ $booking->id }}</div>
                        </div>
                    </div>

                    <div class="mt-3 flex gap-2">
                        <button id="btnCopyAmount" type="button" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-indigo-600 text-white text-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16h8M8 12h8M8 8h8"/></svg>
                            Salin Jumlah
                        </button>

                        <button id="btnPrintReceipt" type="button" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-100 text-gray-800 text-sm hover:bg-gray-200 focus:outline-none">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V2h12v7M6 18h12v4H6z" /></svg>
                            Cetak Nota
                        </button>
                    </div>
                </div>

                {{-- Countdown --}}
                <div class="rounded-xl border border-gray-100 p-4 bg-white shadow-sm mb-4">
                    <div class="text-xs text-gray-400">Batas Waktu Pembayaran</div>
                    <div id="countdown" class="mt-2 text-3xl font-bold text-red-600">--:--:--</div>
                    <div id="countdownSub" class="text-sm text-gray-500 mt-1">Segera selesaikan pembayaran sebelum waktu habis.</div>
                </div>

                {{-- Bank details --}}
                <div class="rounded-xl border border-gray-100 p-4 bg-white shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-xs text-gray-400">Transfer ke</div>
                            <div class="text-sm font-medium text-gray-800">BANK BCA</div>
                            <div class="text-sm text-gray-700 font-semibold mt-1" id="accountNumber">1234567890</div>
                            <div class="text-xs text-gray-500">a.n Peace Picture Studio</div>
                        </div>

                        <div class="flex flex-col items-end gap-2">
                            <button id="btnCopyAccount" type="button" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-100 text-gray-800 text-sm hover:bg-gray-200 focus:outline-none">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16h8M8 12h8M8 8h8"/></svg>
                                Salin Rekening
                            </button>

                            <a href="https://wa.me/6285782086279" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-green-600 text-white text-sm hover:bg-green-700">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15a2 2 0 01-2 2h-1l-2 2v-2H8a2 2 0 01-2-2V7a2 2 0 012-2h8l2 2h1a2 2 0 012 2z"/></svg>
                                Hubungi Admin
                            </a>
                        </div>
                    </div>

                    <div class="mt-3 text-xs text-gray-500">
                        <strong>Catatan:</strong> Setelah transfer, unggah bukti pembayaran di sebelah kanan. File gambar maksimal 5 MB. Tim admin akan verifikasi.
                    </div>
                </div>

                {{-- small footer --}}
                <div class="mt-6 text-xs text-gray-400">
                    <div><strong>Booking ID:</strong> #{{ $booking->id }}</div>
                    <div>Nomor WA Customer: <a href="https://wa.me/{{ preg_replace('/[^0-9]/','',$booking->whatsapp_number) }}" class="text-indigo-600 hover:underline">WA {{ $booking->whatsapp_number }}</a></div>
                </div>
            </div>

            {{-- RIGHT: upload & preview --}}
            <div class="md:w-1/2 p-8 bg-gray-50">
                <div class="max-w-xl mx-auto">
                    <h2 class="text-lg font-semibold text-gray-800 mb-3">Upload Bukti Pembayaran</h2>

                    <form id="uploadFallbackForm" action="{{ route('booking.uploadProof', $booking->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4" novalidate>
                        @csrf

                        {{-- drag & drop area --}}
                        <div id="dropZone" class="border-2 border-dashed border-gray-200 rounded-xl p-4 bg-white hover:border-indigo-300 focus-within:ring-2 focus-within:ring-indigo-200" tabindex="0" role="button" aria-label="Area upload bukti pembayaran">
                            <input id="payment_proof" name="payment_proof" type="file" accept="image/*" class="hidden" required>
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 014-4h10a4 4 0 014 4M12 7v10" /></svg>
                                </div>

                                <div>
                                    <div class="text-sm text-gray-700 font-medium">Seret & letakkan gambar di sini, atau klik untuk pilih file</div>
                                    <div class="text-xs text-gray-400 mt-1">JPEG, PNG. Disarankan foto bukti yang jelas dan lengkap.</div>
                                </div>
                            </div>

                            <div id="dropHints" class="mt-3 text-xs text-gray-500">
                                Ukuran maksimal <strong>5 MB</strong>. Sistem akan mencoba mengompres otomatis jika perlu.
                            </div>
                        </div>

                        {{-- preview area --}}
                        <div id="previewArea" class="hidden mt-2">
                            <label class="text-xs text-gray-400">Preview Bukti</label>
                            <div class="mt-2 rounded-lg border border-gray-200 overflow-hidden bg-white">
                                <img id="previewImg" src="#" alt="Preview Bukti" class="w-full object-contain max-h-72">
                            </div>

                            <div class="mt-2 flex items-center justify-between gap-2">
                                <div class="text-sm text-gray-600" id="fileMeta">—</div>
                                <div class="flex gap-2">
                                    <button id="btnRemoveFile" type="button" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md bg-gray-100 hover:bg-gray-200 text-sm">Hapus</button>
                                </div>
                            </div>
                        </div>

                        {{-- upload controls --}}
                        <div class="flex items-center gap-3">
                            <button id="btnUpload" type="button" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white font-medium focus:outline-none">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12l7-7 7 7"/></svg>
                                Unggah & Kirim
                            </button>

                            <div class="flex-1">
                                <div id="uploadProgressWrap" class="hidden bg-gray-100 rounded-full h-3 overflow-hidden">
                                    <div id="uploadProgressBar" class="h-3 bg-green-500 w-0 transition-all"></div>
                                </div>
                                <div id="uploadStatus" class="mt-1 text-xs text-gray-500 hidden">Mengunggah...</div>
                            </div>
                        </div>

                        @error('payment_proof')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                        @enderror

                        <noscript class="block">
                            <div class="text-sm text-gray-500">Jika JavaScript dimatikan: gunakan tombol di bawah untuk mengunggah secara konvensional.</div>
                            <div class="mt-3">
                                <button type="submit" class="w-full px-4 py-2 rounded-lg bg-indigo-600 text-white">Unggah (Tanpa JavaScript)</button>
                            </div>
                        </noscript>
                    </form>

                    <div class="mt-6 text-xs text-gray-400">
                        <div>Setelah upload, tim admin akan verifikasi. Anda akan menerima notifikasi via halaman riwayat / WA.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Toast container --}}
<div id="toast" aria-live="polite" class="fixed right-6 top-6 z-50"></div>

{{-- Inline Script --}}
<script>
(function () {
    // Helpers
    function showToast(message, type = 'info') {
        const color = type === 'success' ? 'bg-green-600' : type === 'error' ? 'bg-red-600' : 'bg-indigo-600';
        const el = document.createElement('div');
        el.className = `${color} text-white px-4 py-2 rounded shadow mb-2`;
        el.textContent = message;
        document.getElementById('toast').appendChild(el);
        setTimeout(() => { el.style.opacity = 0; setTimeout(()=>el.remove(), 300); }, 3500);
    }

    // Countdown
    const deadline = {!! json_encode(optional($booking->payment_deadline)->timestamp * 1000 ?? null) !!};
    const countdownEl = document.getElementById('countdown');
    const countdownSub = document.getElementById('countdownSub');
    let countdownInterval = null;
    function updateCountdown() {
        if (!deadline) { countdownEl.textContent = '-'; return; }
        const now = Date.now();
        const dist = deadline - now;
        if (dist <= 0) {
            countdownEl.textContent = 'Waktu pembayaran telah habis';
            countdownEl.classList.remove('text-red-600');
            countdownEl.classList.add('text-gray-500');
            countdownSub.textContent = 'Harap hubungi admin untuk opsi selanjutnya.';
            document.getElementById('btnUpload').disabled = true;
            document.getElementById('dropZone')?.classList.add('opacity-60', 'pointer-events-none');
            if (countdownInterval) clearInterval(countdownInterval);
            return;
        }
        const days = Math.floor(dist / (1000*60*60*24));
        const hours = Math.floor((dist % (1000*60*60*24)) / (1000*60*60));
        const minutes = Math.floor((dist % (1000*60*60)) / (1000*60));
        const seconds = Math.floor((dist % (1000*60)) / 1000);
        let out = '';
        if (days) out += days + 'd ';
        out += `${String(hours).padStart(2,'0')}:${String(minutes).padStart(2,'0')}:${String(seconds).padStart(2,'0')}`;
        countdownEl.textContent = out;
    }
    if (deadline) { updateCountdown(); countdownInterval = setInterval(updateCountdown, 1000); }

    // Copy actions
    document.getElementById('btnCopyAccount')?.addEventListener('click', function () {
        const acc = document.getElementById('accountNumber').textContent.trim();
        navigator.clipboard?.writeText(acc).then(()=> showToast('Nomor rekening disalin', 'success')).catch(()=> showToast('Gagal menyalin', 'error'));
    });
    document.getElementById('btnCopyAmount')?.addEventListener('click', function () {
        const amount = {!! json_encode((int) ($booking->total_price ?? 0)) !!};
        navigator.clipboard?.writeText(String(amount)).then(()=> showToast('Jumlah disalin (angka)', 'success')).catch(()=> showToast('Gagal menyalin', 'error'));
    });

    // Print receipt (nota) — minimal, clean
    document.getElementById('btnPrintReceipt')?.addEventListener('click', function () {
        const receiptHtml = `
            <html>
            <head>
                <meta charset="utf-8">
                <title>Nota Pembayaran - #${{{ $booking->id }}}</title>
                <style>
                    body{font-family: system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial; padding:20px; color:#111}
                    .wrap{max-width:400px; margin:0 auto}
                    h2{text-align:center; font-size:18px; margin-bottom:6px}
                    .muted{color:#666; font-size:12px}
                    .line{border-top:1px dashed #ddd; margin:12px 0}
                    .row{display:flex; justify-content:space-between; margin:6px 0}
                    .total{font-weight:700; font-size:18px}
                </style>
            </head>
            <body>
                <div class="wrap">
                    <h2>Nota Pembayaran</h2>
                    <div class="muted">Peace Picture Studio</div>
                    <div class="line"></div>

                    <div class="row"><div>Booking</div><div>#${{ $booking->id }}</div></div>
                    <div class="row"><div>Nama</div><div>{{ $booking->contact_name }}</div></div>
                    <div class="row"><div>Paket</div><div>{{ $booking->package_name }}</div></div>
                    <div class="row"><div>Tanggal</div><div>{{ \Carbon\Carbon::parse($booking->booking_date)->translatedFormat('d F Y') }}</div></div>

                    <div class="line"></div>
                    <div class="row total"><div>Total Bayar</div><div>Rp {{ number_format($booking->total_price ?? 0,0,',','.') }}</div></div>
                    <div class="line"></div>

                    <div class="muted" style="margin-top:10px; font-size:12px">
                        Transfer ke: BANK BCA — 1234567890 (a.n Peace Picture Studio)<br>
                        Setelah transfer, unggah bukti pembayaran melalui halaman ini.<br>
                        Admin: +62 857-8208-6279
                    </div>
                </div>
            </body>
            </html>
        `;
        const w = window.open('', '_blank', 'width=440,height=640,scrollbars=yes');
        if (!w) { showToast('Pop-up diblokir. Izinkan pop-up untuk mencetak.', 'error'); return; }
        w.document.write(receiptHtml);
        w.document.close();
        setTimeout(()=> w.print(), 400);
    });

    // Drag & Drop + Preview + Compress + Upload (same logic as earlier)
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('payment_proof');
    const previewArea = document.getElementById('previewArea');
    const previewImg = document.getElementById('previewImg');
    const fileMeta = document.getElementById('fileMeta');
    const btnRemoveFile = document.getElementById('btnRemoveFile');
    const btnUpload = document.getElementById('btnUpload');
    const uploadProgressWrap = document.getElementById('uploadProgressWrap');
    const uploadProgressBar = document.getElementById('uploadProgressBar');
    const uploadStatus = document.getElementById('uploadStatus');

    let selectedFile = null;
    const MAX_FILE_MB = 5;
    const MAX_FILE_BYTES = MAX_FILE_MB * 1024 * 1024;

    function humanFileSize(bytes) {
        if (bytes === 0) return '0 B';
        const i = Math.floor(Math.log(bytes) / Math.log(1024));
        const sizes = ['B','KB','MB','GB','TB'];
        return (bytes / Math.pow(1024, i)).toFixed(i ? 1 : 0) + ' ' + sizes[i];
    }

    function clearSelection() {
        selectedFile = null;
        if (fileInput) fileInput.value = '';
        previewArea.classList.add('hidden');
        previewImg.src = '#';
        fileMeta.textContent = '—';
    }

    function showFilePreview(file, dataUrl) {
        selectedFile = file;
        previewImg.src = dataUrl;
        previewArea.classList.remove('hidden');
        fileMeta.textContent = `${file.name} • ${humanFileSize(file.size)} • ${file.type}`;
    }

    if (dropZone) {
        ['dragenter','dragover'].forEach(evt => {
            dropZone.addEventListener(evt, function (e) {
                e.preventDefault(); e.stopPropagation();
                dropZone.classList.add('border-indigo-300', 'bg-indigo-50');
            });
        });
        ['dragleave','drop'].forEach(evt => {
            dropZone.addEventListener(evt, function (e) {
                e.preventDefault(); e.stopPropagation();
                dropZone.classList.remove('border-indigo-300', 'bg-indigo-50');
            });
        });

        dropZone.addEventListener('click', function () { fileInput?.click(); });
        dropZone.addEventListener('drop', function (e) {
            const f = (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0]) ? e.dataTransfer.files[0] : null;
            if (f) handleSelectedFile(f);
        });
    }

    if (fileInput) {
        fileInput.addEventListener('change', function (e) {
            const f = this.files && this.files[0];
            if (f) handleSelectedFile(f);
        });
    }

    btnRemoveFile?.addEventListener('click', function () {
        clearSelection();
        showToast('File dihapus', 'info');
    });

    // client-side resize/compress
    function resizeImage(file, maxWidth = 1600, quality = 0.85) {
        return new Promise((resolve, reject) => {
            if (!file.type.startsWith('image/')) return reject(new Error('Not an image'));
            const img = new Image();
            const reader = new FileReader();
            reader.onload = function (ev) { img.src = ev.target.result; };
            reader.onerror = reject;
            img.onerror = reject;
            img.onload = function () {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                let { width, height } = img;
                if (width > maxWidth) {
                    height = Math.round(height * (maxWidth / width));
                    width = maxWidth;
                }
                canvas.width = width;
                canvas.height = height;
                ctx.drawImage(img, 0, 0, width, height);
                canvas.toBlob(function (blob) {
                    if (!blob) return reject(new Error('Compression failed'));
                    const newFile = new File([blob], (file.name.split('.').slice(0,-1).join('.') || 'proof') + '.jpg', { type: 'image/jpeg' });
                    resolve(newFile);
                }, 'image/jpeg', quality);
            };
            reader.readAsDataURL(file);
        });
    }

    async function handleSelectedFile(file) {
        if (!file.type.startsWith('image/')) { showToast('File harus berupa gambar (jpg/png).', 'error'); return; }
        if (file.size > MAX_FILE_BYTES) {
            showToast('File besar — mencoba kompres otomatis...', 'info');
            try {
                const compressed = await resizeImage(file, 1200, 0.8);
                if (compressed.size > MAX_FILE_BYTES) { showToast('Masih terlalu besar setelah kompres, mohon gunakan gambar lebih kecil.', 'error'); return; }
                showFilePreview(compressed, URL.createObjectURL(compressed));
            } catch (err) { console.error(err); showToast('Gagal kompres gambar. Gunakan file <5MB.', 'error'); }
        } else {
            showFilePreview(file, URL.createObjectURL(file));
        }
    }

    // upload via XHR
    btnUpload?.addEventListener('click', function () {
        if (!selectedFile) { showToast('Pilih bukti pembayaran terlebih dahulu.', 'error'); return; }
        const fd = new FormData();
        fd.append('payment_proof', selectedFile);
        fd.append('_token', '{{ csrf_token() }}');

        const url = document.getElementById('uploadFallbackForm').action;
        uploadProgressWrap.classList.remove('hidden');
        uploadProgressBar.style.width = '0%';
        uploadStatus.classList.remove('hidden');
        uploadStatus.textContent = 'Mengunggah...';

        const xhr = new XMLHttpRequest();
        xhr.open('POST', url, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        xhr.upload.onprogress = function (e) {
            if (e.lengthComputable) {
                const pct = Math.round((e.loaded / e.total) * 100);
                uploadProgressBar.style.width = pct + '%';
            }
        };

        xhr.onload = function () {
            try {
                if (xhr.status >= 200 && xhr.status < 300) {
                    let res;
                    try { res = JSON.parse(xhr.responseText); } catch (e) { res = null; }
                    showToast((res && res.message) ? res.message : 'Berhasil mengunggah bukti. Menunggu verifikasi.', 'success');
                    uploadStatus.textContent = 'Selesai';
                    setTimeout(()=> location.reload(), 900);
                    return;
                } else {
                    let errMsg = 'Gagal mengunggah. Periksa koneksi atau coba lagi.';
                    try { const j = JSON.parse(xhr.responseText); if (j && j.message) errMsg = j.message; } catch {}
                    showToast(errMsg, 'error');
                    uploadStatus.textContent = 'Gagal';
                }
            } catch (err) {
                console.error(err);
                showToast('Respons server tidak valid', 'error');
            } finally {
                setTimeout(()=> { uploadProgressWrap.classList.add('hidden'); uploadStatus.classList.add('hidden'); uploadProgressBar.style.width = '0%'; }, 1500);
            }
        };

        xhr.onerror = function () {
            showToast('Gagal menghubungi server saat upload', 'error');
            uploadStatus.textContent = 'Gagal';
            setTimeout(()=> { uploadProgressWrap.classList.add('hidden'); uploadStatus.classList.add('hidden'); uploadProgressBar.style.width = '0%'; }, 1500);
        };

        xhr.send(fd);
    });

    // keyboard accessibility
    dropZone?.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); fileInput?.click(); }
    });
})();
</script>
@endsection
