<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Background;
use App\Models\ExtraItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    /**
     * Store booking (customer online / admin offline).
     */
    public function store(Request $request)
    {
        $isCustomer = Auth::guard('customer')->check();
        $isAdmin    = Auth::guard('web')->check();

        $rules = [
            'contact_name'            => 'required|string|max:100',
            'whatsapp_number'         => 'required|string|max:20',
            'booking_date'            => 'required|date|after_or_equal:today',
            'booking_time'            => 'required|string',
            'session_name'            => 'required|string|max:100',
            'package_name'            => 'required|string|max:100',
            'selected_backgrounds'    => 'nullable',
            'selected_extra_items'    => 'nullable',
            'notes'                   => 'nullable|string',
            'baby_name'               => 'nullable|string|max:255',
            'baby_age'                => 'nullable|string|max:50',
        ];

        if ($isAdmin && !$isCustomer) {
            $rules['customer_id']    = 'required|exists:customers,id';
            $rules['payment_method'] = 'required|in:cash,transfer';
            $rules['payment_proof']  = 'nullable|image|mimes:jpg,jpeg,png|max:5120';
        }

        $data = $request->validate($rules);

        // Normalize phone
        $data['whatsapp_number'] = $this->normalizePhone($data['whatsapp_number']);

        // Validate booking_time is allowed
        if (method_exists(Booking::class, 'getAllTimes')) {
            $allTimes = Booking::getAllTimes();
            if (!in_array($data['booking_time'], $allTimes, true)) {
                return $this->respondInvalid($request, 'Waktu booking tidak valid.');
            }
        }

        // -----------------------
        // Extras: normalize robustly (accept ids OR objects {id, qty, price})
        // -----------------------
        $extras = $this->normalizeExtrasInput($data['selected_extra_items'] ?? []);
        // extras is array of entries: ['id'=>..., 'name'=>..., 'price'=>int, 'qty'=>int, 'total'=>int]

        // Backgrounds: convert ids->structured array (reuse existing helper)
        $selectedBgIds = $this->normalizeSelectedIds($data['selected_backgrounds'] ?? []);
        $backgrounds = $this->fetchBackgroundsByIds($selectedBgIds);

        // Price calculation (server-side)
        $packagePrice = $this->getPackagePrice($data['package_name']);
        $extraItemsPrice = array_sum(array_map(fn($e) => (int)($e['total'] ?? 0), $extras));
        $totalPrice = $packagePrice + $extraItemsPrice;

        // Backgrounds validation per package
        $maxBackgrounds = $this->getMaxBackgrounds($data['package_name']);
        $isBabySmash = in_array(Str::lower($data['package_name']), ['baby smash cake', 'babysmash'], true);
        if (!$isBabySmash) {
            if (count($selectedBgIds) < 1) {
                return back()->withInput()->with('errorMessage', "❌ Paket {$data['package_name']} harus memilih minimal 1 background.");
            }
            if (count($selectedBgIds) > $maxBackgrounds) {
                return back()->withInput()->with('errorMessage', "❌ Paket {$data['package_name']} hanya membolehkan maksimal {$maxBackgrounds} background.");
            }
        } else {
            $backgrounds = [];
        }

        // Slot availability (same as before)
        if (method_exists(Booking::class, 'isSlotAvailable')) {
            $isAvailable = Booking::isSlotAvailable($data['booking_date'], $data['booking_time']);
            if (!$isAvailable) {
                return $this->respondInvalid($request, 'Slot sudah terisi, pilih waktu lain.', 409);
            }
        } else {
            $exists = Booking::where('booking_date', $data['booking_date'])
                ->where('booking_time', $data['booking_time'])
                ->whereIn('status', [Booking::STATUS_WAITING_PAYMENT, Booking::STATUS_PENDING_VERIFICATION, Booking::STATUS_BOOKED])
                ->exists();
            if ($exists) {
                return $this->respondInvalid($request, 'Slot sudah terisi, pilih waktu lain.', 409);
            }
        }

        // Normalize date
        try {
            $bookingDate = Carbon::parse($data['booking_date'])->toDateString();
        } catch (\Throwable $e) {
            $bookingDate = $data['booking_date'];
        }

        // Build payload: store structured selected_extra_items (with price & qty)
        $payload = [
            'contact_name' => trim($data['contact_name']),
            'whatsapp_number' => $data['whatsapp_number'],
            'booking_date' => $bookingDate,
            'booking_time' => $data['booking_time'],
            'session_name' => $data['session_name'],
            'package_name' => $data['package_name'],
            'selected_backgrounds' => $backgrounds,
            'selected_extra_items' => $extras,
            'total_price' => $totalPrice,
            'notes' => $data['notes'] ?? null,
            'baby_name' => $data['baby_name'] ?? null,
            'baby_age' => $data['baby_age'] ?? null,
        ];

        // status/payment logic (same as previous behavior)
        if ($isAdmin && !$isCustomer) {
            $payload['user_id'] = auth('web')->id();
            $payload['customer_id'] = $data['customer_id'];
            $payload['payment_method'] = $data['payment_method'] ?? 'transfer';

            if ($request->hasFile('payment_proof')) {
                try {
                    $path = $request->file('payment_proof')->store('payment_proofs', 'public');
                    $payload['payment_proof'] = $path;

                    if (($data['payment_method'] ?? '') === 'transfer') {
                        $payload['status'] = Booking::STATUS_PENDING_VERIFICATION;
                        $payload['payment_deadline'] = null;
                    } else {
                        $payload['status'] = Booking::STATUS_BOOKED;
                        $payload['payment_deadline'] = null;
                    }
                } catch (\Throwable $e) {
                    Log::warning('Gagal menyimpan payment_proof (admin create): ' . $e->getMessage());
                    $payload['status'] = ($data['payment_method'] ?? 'transfer') === 'cash' ? Booking::STATUS_BOOKED : Booking::STATUS_WAITING_PAYMENT;
                    $payload['payment_deadline'] = $payload['status'] === Booking::STATUS_WAITING_PAYMENT ? now()->addMinutes(10) : null;
                }
            } else {
                if (($data['payment_method'] ?? 'transfer') === 'cash') {
                    $payload['status'] = Booking::STATUS_BOOKED;
                    $payload['payment_deadline'] = null;
                } else {
                    $payload['status'] = Booking::STATUS_WAITING_PAYMENT;
                    $payload['payment_deadline'] = now()->addMinutes(10);
                }
            }
        } else {
            $payload['payment_method'] = 'transfer';
            $payload['status'] = Booking::STATUS_WAITING_PAYMENT;
            $payload['payment_deadline'] = now()->addMinutes(10);
            if ($isCustomer) {
                $payload['customer_id'] = Auth::guard('customer')->id();
            }
        }

        try {
            $booking = Booking::create($payload);

            Log::info('BookingController@store - booking created', ['booking_id' => $booking->id, 'status' => $booking->status ?? null]);

            if ($isCustomer && $request->expectsJson()) {
                return response()->json([
                    'message' => 'Pesanan berhasil dibuat, silakan lakukan pembayaran.',
                    'redirect_url' => route('booking.payment', ['booking' => $booking->id])
                ], 201);
            }

            if ($isCustomer) {
                return redirect()->route('booking.payment', ['booking' => $booking->id])
                    ->with('successMessage', 'Pesanan berhasil dibuat, silakan lakukan pembayaran.');
            }

            if ($isAdmin && !$isCustomer) {
                return redirect()->route('bookings.index')
                    ->with('successMessage', 'Booking manual berhasil ditambahkan.');
            }

            abort(403, 'Unauthorized');
        } catch (\Throwable $e) {
            Log::error('Booking gagal: ' . $e->getMessage(), ['payload' => $payload]);
            return $this->respondServerError($request, 'Terjadi kesalahan, coba lagi.');
        }
    }

    /**
     * Normalize extras input. Accept:
     *  - array of ids: [1,2,3]
     *  - array of objects: [{id:1, qty:2, price:50000}, {...}]
     *  - json string of above
     *
     * Returns array of structured extras:
     *  [
     *    ['id'=>1,'name'=>'Cetak 4R','price'=>50000,'qty'=>2,'total'=>100000],
     *    ...
     *  ]
     */
    private function normalizeExtrasInput($value): array
    {
        if (is_null($value) || $value === '') return [];

        // decode JSON string if needed
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $value = $decoded;
            } elseif (Str::contains($value, ',')) {
                $value = array_map('trim', explode(',', $value));
            } else {
                $value = [$value];
            }
        }

        if (!is_array($value)) {
            return [];
        }

        $normalized = [];
        $idsToFetch = [];

        foreach ($value as $it) {
            if (is_array($it)) {
                $id = $it['id'] ?? ($it[0] ?? null);
                $qty = isset($it['qty']) ? (int)$it['qty'] : (isset($it['quantity']) ? (int)$it['quantity'] : 1);
                $price = isset($it['price']) ? (int)$it['price'] : null;
                $normalized[] = ['id' => $id, 'qty' => max(1, $qty), 'price' => $price, 'name' => $it['name'] ?? null];
                if (is_numeric($id)) $idsToFetch[] = (int)$id;
            } else {
                // scalar id
                $id = $it;
                $normalized[] = ['id' => $id, 'qty' => 1, 'price' => null, 'name' => null];
                if (is_numeric($id)) $idsToFetch[] = (int)$id;
            }
        }

        $idsToFetch = array_values(array_unique(array_filter($idsToFetch, fn($v) => $v > 0)));

        $dbItems = [];
        if (!empty($idsToFetch)) {
            $rows = ExtraItem::whereIn('id', $idsToFetch)->get(['id', 'name', 'price']);
            foreach ($rows as $r) $dbItems[$r->id] = $r;
        }

        // finalize: fill missing price/name from DB and compute total
        // finalize: fill missing price/name from DB and compute total
        foreach ($normalized as &$entry) {
            $id = $entry['id'];
            if (is_numeric($id) && isset($dbItems[(int)$id])) {
                $db = $dbItems[(int)$id];
                // always use DB price for items that exist in DB
                $entry['price'] = (int)$db->price;
                if (empty($entry['name'])) $entry['name'] = $db->name;
            } else {
                // fallback to provided price or 0
                $entry['price'] = (int) ($entry['price'] ?? 0);
                $entry['name'] = $entry['name'] ?? 'Extra Item';
            }
            $entry['qty'] = max(1, (int)($entry['qty'] ?? 1));
            $entry['total'] = (int)$entry['price'] * $entry['qty'];
        }
        unset($entry);


        return $normalized;
    }


    /**
     * Show payment page (customer).
     */
    public function payment(Booking $booking)
    {
        if (Auth::guard('customer')->id() !== $booking->customer_id) {
            abort(403);
        }

        if (method_exists($booking, 'needsAutoCancellation') && $booking->needsAutoCancellation()) {
            $booking->autoCancel();
        }

        // Allow only appropriate statuses to see the payment page
        if ($booking->status === Booking::STATUS_WAITING_PAYMENT) {
            return view('customer.payments.manual', compact('booking'));
        }

        if ($booking->status === Booking::STATUS_PENDING_VERIFICATION) {
            return redirect()->route('customer.bookings')->with('errorMessage', 'Bukti pembayaran sudah diterima dan sedang menunggu verifikasi oleh admin.');
        }

        if ($booking->status === Booking::STATUS_BOOKED || $booking->status === Booking::STATUS_COMPLETED) {
            return redirect()->route('customer.bookings')->with('errorMessage', 'Pembayaran sudah dikonfirmasi atau jadwal sudah dikonfirmasi.');
        }

        if ($booking->status === Booking::STATUS_CANCELLED) {
            return redirect()->route('customer.bookings')->with('errorMessage', 'Pesanan telah dibatalkan.');
        }

        return redirect()->route('customer.bookings')->with('errorMessage', 'Status pesanan tidak valid untuk halaman pembayaran.');
    }

    /**
     * Upload payment proof (customer).
     *
     * Handles both AJAX (JSON) and classic form POST.
     */
    public function uploadProof(Request $request, Booking $booking)
    {
        if (Auth::guard('customer')->id() !== $booking->customer_id) {
            abort(403);
        }

        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        try {
            $path = $request->file('payment_proof')->store('payment_proofs', 'public');

            $booking->update([
                'payment_proof' => $path,
                'status'        => Booking::STATUS_PENDING_VERIFICATION,
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Bukti pembayaran berhasil diunggah. Menunggu verifikasi.'], 200);
            }

            return redirect()->route('customer.bookings')->with('successMessage', 'Bukti pembayaran berhasil diunggah.');
        } catch (\Throwable $e) {
            Log::error('Upload bukti gagal: ' . $e->getMessage());
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Upload gagal, coba lagi.'], 500);
            }
            return back()->with('errorMessage', 'Upload gagal, coba lagi.');
        }
    }

    /**
     * Admin manually verifies the payment (sets booked).
     */
    public function adminVerifyPayment(Request $request, Booking $booking)
    {
        if (!Auth::guard('web')->check()) {
            abort(403);
        }

        try {
            $booking->update([
                'status' => Booking::STATUS_BOOKED,
                'payment_deadline' => null,
            ]);

            Log::info("Admin verified payment for booking {$booking->id} by user " . auth('web')->id());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Pembayaran diverifikasi. Booking dikonfirmasi.'], 200);
            }
            return redirect()->back()->with('successMessage', 'Pembayaran diverifikasi dan booking dikonfirmasi.');
        } catch (\Throwable $e) {
            Log::error('Verify payment failed: ' . $e->getMessage(), ['booking_id' => $booking->id]);
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Gagal memverifikasi pembayaran.'], 500);
            }
            return redirect()->back()->with('errorMessage', 'Gagal memverifikasi pembayaran.');
        }
    }

    /**
     * Customer requests cancellation.
     */
    public function requestCancellation(Request $request, Booking $booking)
    {
        // Ownership
        if (Auth::guard('customer')->id() !== $booking->customer_id) {
            return $this->respondUnauthorized($request);
        }

        // Validation
        $payload = $request->validate([
            'reason' => 'required|string|max:1000',
            'agree'  => 'required|accepted'
        ]);

        // Disallowed statuses
        if (in_array($booking->status, [Booking::STATUS_CANCELLED, Booking::STATUS_COMPLETED], true)) {
            return $this->respondInvalid($request, 'Pesanan sudah tidak dapat dibatalkan.');
        }

        // Compute booking datetime robustly
        try {
            $bookingDateTime = Carbon::createFromFormat('Y-m-d H:i', $booking->booking_date . ' ' . $booking->booking_time);
        } catch (\Throwable $e) {
            $bookingDateTime = Carbon::parse($booking->booking_date . ' ' . $booking->booking_time);
        }

        // Require at least 24 hours notice
        $cutoff = $bookingDateTime->copy()->subHours(24);
        if (now()->greaterThanOrEqualTo($cutoff)) {
            return $this->respondInvalid($request, 'Pembatalan hanya dapat diajukan minimal 24 jam sebelum jadwal sesi.');
        }

        // If still unpaid -> cancel immediately (no refund)
        if ($booking->status === Booking::STATUS_WAITING_PAYMENT) {
            $booking->update([
                'status' => Booking::STATUS_CANCELLED,
                'cancellation_requested' => true,
                'cancellation_requested_at' => now(),
                'cancellation_reason' => $payload['reason'],
                'refund_amount' => 0,
            ]);

            $msg = 'Booking dibatalkan. Karena belum melakukan pembayaran, tidak ada refund yang diperlukan.';
            if ($request->expectsJson()) return response()->json(['message' => $msg], 200);
            return redirect()->route('customer.bookings')->with('successMessage', $msg);
        }

        // For paid statuses (pending_verification, booked) -> flag request for admin review
        $suggestedRefund = (int) round(($booking->total_price ?? 0) * 0.9);

        try {
            $booking->update([
                'cancellation_requested' => true,
                'cancellation_requested_at' => now(),
                'cancellation_reason' => $payload['reason'],
                // final refund_amount to be set by admin after review
            ]);

            Log::info("Cancellation requested for booking {$booking->id} by customer {$booking->customer_id}");

            $msg = 'Permohonan pembatalan berhasil dikirim. Admin akan meninjau dan memproses refund sesuai kebijakan.';
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $msg,
                    'suggested_refund' => $suggestedRefund,
                ], 200);
            }

            $flash = $msg . ' (Refund : Rp ' . number_format($suggestedRefund, 0, ',', '.') . ')';
            return redirect()->route('customer.bookings')->with('successMessage', $flash);
        } catch (\Throwable $e) {
            Log::error('Gagal menyimpan permohonan pembatalan: ' . $e->getMessage(), ['booking_id' => $booking->id]);
            return $this->respondServerError($request, 'Terjadi kesalahan, coba lagi.');
        }
    }

    /**
     * API check status booking (countdown).
     */
    public function checkStatus(Booking $booking)
    {
        if (method_exists($booking, 'needsAutoCancellation') && $booking->needsAutoCancellation()) {
            $booking->autoCancel();
        }

        return response()->json([
            'status' => $booking->status,
            'remaining_time' => $booking->getRemainingPaymentTime(),
            'deadline' => $booking->payment_deadline?->timestamp,
            'cancellation_requested' => (bool) $booking->cancellation_requested,
            'cancellation_requested_at' => $booking->cancellation_requested_at?->toDateTimeString(),
        ]);
    }

    /**
     * Serve payment/refund proof file securely.
     * Route: GET /bookings/{booking}/proof/{type} where {type} = payment|refund
     */
    public function serveProof(Request $request, Booking $booking, string $type)
    {
        if (!in_array($type, ['payment', 'refund'], true)) abort(404);

        // authorization
        $isCustomer = Auth::guard('customer')->check();
        $isAdmin = Auth::guard('web')->check();

        if ($isCustomer && Auth::guard('customer')->id() !== $booking->customer_id) {
            abort(403);
        }
        if (!$isCustomer && !$isAdmin) abort(403);

        $path = $type === 'payment' ? $booking->payment_proof : $booking->refund_proof;
        if (empty($path)) abort(404);

        $disk = Storage::disk('public');
        if ($disk->exists($path)) {
            $full = storage_path('app/public/' . ltrim($path, '/'));
            if (!file_exists($full)) abort(404);

            // Prevent path traversal
            if (!Str::startsWith(realpath($full), realpath(storage_path('app/public')))) {
                abort(403);
            }

            $mime = mime_content_type($full) ?: 'application/octet-stream';
            $inline = Str::startsWith($mime, 'image/') || Str::startsWith($mime, 'video/') || Str::startsWith($mime, 'audio/');

            return response()->file($full, [
                'Content-Type' => $mime,
                'Content-Disposition' => ($inline ? 'inline' : 'attachment') . '; filename="' . basename($full) . '"'
            ]);
        }

        abort(404);
    }

    /* -----------------------
     * Helper methods
     * ----------------------*/

    private function normalizePhone(string $raw): string
    {
        $s = preg_replace('/[^\d+]/', '', trim($raw));
        if ($s === '') return $s;

        if (Str::startsWith($s, '+')) return $s;
        if (Str::startsWith($s, '62')) return '+' . $s;
        if (Str::startsWith($s, '0')) return '+62' . substr($s, 1);
        return '+62' . $s;
    }

    private function normalizeSelectedIds($value): array
    {
        if (is_null($value)) return [];
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $arr = $decoded;
            } elseif (Str::contains($value, ',')) {
                $arr = array_filter(array_map('trim', explode(',', $value)));
            } else {
                $arr = [$value];
            }
        } elseif (is_array($value)) {
            $arr = $value;
        } else {
            $arr = [$value];
        }

        $arr = array_values(array_filter($arr, fn($v) => $v !== null && $v !== ''));
        $arr = array_map(fn($v) => is_numeric($v) ? (int)$v : $v, $arr);
        $arr = array_values(array_unique($arr, SORT_REGULAR));
        return $arr;
    }

    private function fetchExtrasByIds($idsInput = []): array
    {
        $ids = $this->normalizeSelectedIds($idsInput);
        if (empty($ids)) return [];

        $items = ExtraItem::whereIn('id', $ids)->get(['id', 'name', 'price']);
        return $items->map(fn($it) => [
            'id' => $it->id,
            'name' => $it->name,
            'price' => (int)$it->price,
        ])->toArray();
    }

    private function fetchBackgroundsByIds($idsInput = []): array
    {
        $ids = $this->normalizeSelectedIds($idsInput);
        if (empty($ids)) return [];

        $bgs = Background::whereIn('id', $ids)->get(['id', 'name', 'image']);
        return $bgs->map(fn($bg) => [
            'id' => $bg->id,
            'name' => $bg->name,
            'image' => $bg->image,
        ])->toArray();
    }

    private function getPackagePrice($packageName): int
    {
        switch (Str::lower((string)$packageName)) {
            case 'baby smash cake':
            case 'babysmash':
                return 550000;
            case 'plain':
                return 300000;
            case 'grande':
                return 500000;
            case 'royal':
                return 700000;
            case 'prewed i':
            case 'prewed1':
                return 700000;
            case 'prewed ii':
            case 'prewed2':
                return 1000000;
            case 'family':
                return 800000;
            case 'graduation':
                return 500000;
            default:
                return 0;
        }
    }

    private function getMaxBackgrounds($packageName): int
    {
        switch (Str::lower((string)$packageName)) {
            case 'baby smash cake':
            case 'babysmash':
                return 0;
            case 'plain':
                return 1;
            case 'grande':
                return 2;
            case 'prewed i':
            case 'prewed1':
                return 2;
            case 'royal':
                return 4;
            case 'prewed ii':
            case 'prewed2':
                return 3;
            case 'family':
                return 2;
            case 'graduation':
                return 2;
            default:
                return 1;
        }
    }

    /* -----------------------
     * Response helpers
     * ----------------------*/

    private function respondInvalid(Request $request, string $message, int $status = 422)
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['message' => $message], $status);
        }
        return back()->withInput()->with('errorMessage', $message);
    }

    private function respondServerError(Request $request, string $message = 'Terjadi kesalahan', int $status = 500)
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['message' => $message], $status);
        }
        return back()->with('errorMessage', $message);
    }

    private function respondUnauthorized(Request $request)
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        abort(403);
    }
}
