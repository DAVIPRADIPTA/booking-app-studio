<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Background;
use App\Models\ExtraItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use Carbon\Carbon;

class AdminBookingController extends Controller
{
    /**
     * List semua booking (dengan filter & search)
     */
    public function index(Request $request)
    {
        $query = Booking::query();

        // quick search on contact_name, whatsapp_number, package_name
        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($sub) use ($q) {
                $sub->where('contact_name', 'like', "%{$q}%")
                    ->orWhere('whatsapp_number', 'like', "%{$q}%")
                    ->orWhere('package_name', 'like', "%{$q}%");
            });
        }

        // filter by status (including special 'cancellation_requested' filter)
        if ($request->filled('status') && $request->status !== 'all') {
            $status = $request->status;

            // special filter to show bookings that have a cancellation request
            if ($status === 'cancellation_requested') {
                $query->where('cancellation_requested', true);
            } else {
                // normal booking status filter (guard with Booking::statuses())
                if (in_array($status, Booking::statuses())) {
                    $query->where('status', $status);
                }
            }
        }

        // filter by package name
        if ($request->filled('package_name') && $request->package_name !== 'all') {
            $query->where('package_name', $request->package_name);
        }

        // date range filter (booking_date)
        if ($request->filled('date_from')) {
            $query->whereDate('booking_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('booking_date', '<=', $request->date_to);
        }

        // **sorting** (DEFAULT: created_at desc)
        $sortBy = $request->get('sort_by', 'created_at_desc');
        switch ($sortBy) {
            case 'booking_date_asc':
                $query->orderBy('booking_date', 'asc')->orderBy('booking_time', 'asc');
                break;
            case 'booking_date_desc':
                $query->orderBy('booking_date', 'desc')->orderBy('booking_time', 'desc');
                break;
            case 'created_at_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'created_at_desc':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // per-page
        $perPage = (int) $request->get('per_page', 10);
        $perPage = max(5, min(100, $perPage));

        // stats: counts by status
        $statusCounts = Booking::selectRaw('status, count(*) as cnt')
            ->groupBy('status')
            ->pluck('cnt', 'status')
            ->toArray();

        // distinct packages for filter dropdown
        $packages = Booking::select('package_name')
            ->whereNotNull('package_name')
            ->groupBy('package_name')
            ->orderBy('package_name')
            ->pluck('package_name')
            ->toArray();

        // count permohonan pembatalan (dipakai di view sebagai badge)
        $pendingCount = Booking::where('cancellation_requested', true)->count();

        $bookings = $query->paginate($perPage)->withQueryString();

        return view('admin.bookings.index', compact('bookings', 'statusCounts', 'packages', 'pendingCount'));
    }


    /**
     * Tampilkan detail booking
     */
    public function show($id)
    {
        $booking = Booking::findOrFail($id);
        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Halaman daftar permohonan pembatalan (admin)
     */
    public function cancellationRequests(Request $request)
    {
        $query = Booking::where('cancellation_requested', true)
            ->orderBy('cancellation_requested_at', 'desc');

        // optional filter: status
        if ($request->filled('status') && $request->status !== 'all' && in_array($request->status, Booking::statuses())) {
            $query->where('status', $request->status);
        }

        $perPage = (int) $request->get('per_page', 20);
        $perPage = max(5, min(200, $perPage));

        $requests = $query->paginate($perPage)->withQueryString();

        // counts for badge/notification
        $pendingCount = Booking::where('cancellation_requested', true)->count();

        return view('admin.bookings.cancellations', compact('requests', 'pendingCount'));
    }

    /**
     * ADMIN: Mark booking as having a cancellation request (admin-triggered)
     *
     * This helps when admin wants to create a request (for testing or to start the flow)
     * so that the same UI and approval modal are shown in the show page.
     *
     * Accepts optional cancellation_reason.
     */
    public function markCancellationRequested(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        if (in_array($booking->status, [Booking::STATUS_CANCELLED, Booking::STATUS_COMPLETED])) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Booking tidak dapat diajukan pembatalan pada status saat ini.'], 422);
            }
            return back()->with('errorMessage', '❌ Booking tidak dapat diajukan pembatalan pada status saat ini.');
        }

        $request->validate([
            'cancellation_reason' => 'nullable|string|max:255',
        ]);

        try {
            $booking->markCancellationRequested($request->input('cancellation_reason'));

            Log::info("Admin membuat permohonan pembatalan untuk booking #{$booking->id}", [
                'booking_id' => $booking->id,
                'admin_id' => auth('web')->id(),
                'reason' => $request->input('cancellation_reason'),
            ]);

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Permohonan pembatalan dibuat.', 'booking_id' => $booking->id]);
            }

            return back()->with('successMessage', '✅ Permohonan pembatalan berhasil dibuat untuk booking #' . $booking->id);
        } catch (\Exception $e) {
            Log::error('Gagal membuat permohonan pembatalan (admin): ' . $e->getMessage(), [
                'booking_id' => $booking->id,
                'exception' => $e,
            ]);
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Terjadi kesalahan saat membuat permohonan pembatalan.'], 500);
            }
            return back()->with('errorMessage', 'Terjadi kesalahan saat membuat permohonan pembatalan. Silakan coba lagi.');
        }
    }

    /**
     * Form tambah booking manual
     */


    /**
     * Form edit booking - HANYA UNTUK STATUS 'booked'
     */


    /**
     * Update booking - HANYA UNTUK STATUS 'booked'
     */
    public function create()
    {
        $customers = Customer::all();

        // Ambil semua background aktif
        $backgroundItems = Background::where('is_active', true)->get();

        // Siapkan koleksi yang dipakai Blade/JS
        $babySmashBackgrounds  = $backgroundItems->where('category', 'baby-smash')->values();
        $plainBackgrounds      = $backgroundItems->where('category', 'plain')->values();
        $grandeBackgrounds     = $backgroundItems->where('category', 'grande')->values();
        $royalBackgrounds      = $backgroundItems->where('category', 'royal')->values();
        $prewedBackgrounds     = $backgroundItems->where('category', 'pre-wedding')->values();

        // sessions (shared categories)
        $familyBackgrounds     = $backgroundItems->where('category', 'family')->values();
        $graduationBackgrounds = $backgroundItems->where('category', 'graduation')->values();
        $maternityBackgrounds  = $backgroundItems->where('category', 'maternity')->values();

        // Extra items
        $extraItems = ExtraItem::where('is_active', true)->get();
        $printItems = $extraItems->where('category', 'cetak-foto')->values();
        $frameItems = $extraItems->where('category', 'frame-foto')->values();
        $serviceItems = $extraItems->where('category', 'tambahan-layanan')->values();

        // Build combination map: keys like "plain_family", "grande_graduation", ...
        // Strategy:
        // 1) If DB has a background whose category exactly equals "{package}_{session}", prefer those.
        // 2) Else fallback to backgrounds that match either package category or session category (merged, unique).
        $packages = ['plain', 'grande', 'royal'];
        $sessions = ['family', 'graduation', 'maternity'];
        $combinedBackgroundsMap = [];

        foreach ($packages as $pkg) {
            foreach ($sessions as $sess) {
                $comboKey = "{$pkg}_{$sess}";

                // try exact-match category first
                $exact = $backgroundItems->where('category', $comboKey)->values();
                if ($exact->isNotEmpty()) {
                    $combinedBackgroundsMap[$comboKey] = $exact->toArray();
                    continue;
                }

                // fallback: union of backgrounds with category == package OR category == session
                $pkgSet = $backgroundItems->where('category', $pkg)->values();
                $sessSet = $backgroundItems->where('category', $sess)->values();

                // merge unique by id
                $merged = $pkgSet->merge($sessSet)->unique('id')->values();
                $combinedBackgroundsMap[$comboKey] = $merged->toArray();
            }
        }

        // Also pass single-category maps for direct rendering
        $singleCategoryMap = [
            'baby-smash'   => $babySmashBackgrounds->toArray(),
            'plain'        => $plainBackgrounds->toArray(),
            'grande'       => $grandeBackgrounds->toArray(),
            'royal'        => $royalBackgrounds->toArray(),
            'pre-wedding'  => $prewedBackgrounds->toArray(),
            'family'       => $familyBackgrounds->toArray(),
            'graduation'   => $graduationBackgrounds->toArray(),
            'maternity'    => $maternityBackgrounds->toArray(),
            'all'          => $backgroundItems->toArray(),
        ];

        // Old inputs normalization (so blade/js can read)
        $selectedBackgrounds = old('selected_backgrounds', []);
        if (is_string($selectedBackgrounds)) {
            $tmp = json_decode($selectedBackgrounds, true);
            $selectedBackgrounds = is_array($tmp) ? $tmp : [];
        }

        $selectedExtraItems = old('selected_extra_items', []);
        if (is_string($selectedExtraItems)) {
            $tmp = json_decode($selectedExtraItems, true);
            $selectedExtraItems = is_array($tmp) ? $tmp : [];
        }

        // Available times for default date (dipakai untuk inisialisasi dropdown waktu)
        $defaultDate = old('booking_date', Carbon::now()->toDateString());
        $availableTimes = Booking::getAvailableTimes($defaultDate);

        return view('admin.bookings.create', compact(
            'customers',
            'backgroundItems',
            'babySmashBackgrounds',
            'plainBackgrounds',
            'grandeBackgrounds',
            'royalBackgrounds',
            'prewedBackgrounds',
            'familyBackgrounds',
            'graduationBackgrounds',
            'maternityBackgrounds',
            'printItems',
            'frameItems',
            'serviceItems',
            'selectedBackgrounds',
            'selectedExtraItems',
            'availableTimes',
            'combinedBackgroundsMap',
            'singleCategoryMap'
        ));
    }

    public function store(Request $request)
    {
        // Normalisasi WA awal
        $normalizedPhone = $this->normalizePhone($request->input('whatsapp_number', ''));
        $request->merge(['whatsapp_number' => $normalizedPhone]);

        $packageName = (string) $request->input('package_name', '');
        $packageKey = Str::lower($packageName);

        // Tentukan apakah paket adalah group type yang mewajibkan session
        $isGroupPackage = in_array($packageKey, ['plain', 'grande', 'royal'], true);
        $isBabySmash = in_array($packageKey, ['baby smash cake', 'babysmash'], true);

        // Rules dasar
        $rules = [
            'customer_id'     => 'required|exists:customers,id',
            'contact_name'    => 'required|string|max:100',
            'whatsapp_number' => 'required|string|max:25',
            'booking_date'    => 'required|date|after_or_equal:today',
            'booking_time'    => 'required|string',
            'package_name'    => 'required|string|max:100',
            'payment_method'  => 'required|in:cash,transfer',
            'selected_extra_items' => 'nullable',
            'payment_proof'   => $request->input('payment_method') === 'transfer' ? 'nullable|image|mimes:jpg,jpeg,png|max:2048' : 'nullable',
            'baby_name'       => 'nullable|string|max:255',
            'baby_age'        => 'nullable|string|max:50',
            'notes'           => 'nullable|string|max:2000',
        ];

        // Session wajib untuk group package
        if ($isGroupPackage) {
            $rules['session_name'] = 'required|string|in:family,graduation,maternity';
            $rules['selected_backgrounds'] = 'required';
        } else {
            // Jika bukan group (contoh: pre-wedding) session boleh optional
            $rules['session_name'] = 'nullable|string|max:100';
            // Background wajib kecuali baby-smash
            $rules['selected_backgrounds'] = $isBabySmash ? 'nullable' : 'required';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }

        // Valid waktu
        $allTimes = Booking::getAllTimes();
        if (!in_array($request->booking_time, $allTimes)) {
            return back()->withInput()->with('errorMessage', 'Waktu booking tidak valid.');
        }

        // Cek ketersediaan slot
        if (!Booking::isSlotAvailable($request->booking_date, $request->booking_time)) {
            return back()->withInput()->with('errorMessage', '❌ Slot sudah terisi, silakan pilih waktu lain.');
        }

        // Hitung harga paket dari mapping (gunakan DB mapping jika kelak berubah)
        $packagePrice = $this->getPackagePrice($request->package_name);

        // Ambil extras valid dari DB
        $extras = $this->fetchExtrasByIds($request->input('selected_extra_items', []));
        $extraItemsPrice = array_sum(array_map(fn($e) => (int)($e['price'] ?? 0), $extras));
        $totalPrice = $packagePrice + $extraItemsPrice;

        // Upload bukti transfer jika ada
        $paymentProofPath = null;
        if ($request->hasFile('payment_proof')) {
            $paymentProofPath = $this->storeUploadedFile($request->file('payment_proof'), 'payment_proofs', null);
        }

        // Background handling
        $backgrounds = [];
        $maxBackgrounds = $this->getMaxBackgrounds($request->package_name);

        if (!$isBabySmash) {
            $selectedBackgroundIds = $this->normalizeSelectedIds($request->input('selected_backgrounds', []));
            if (count($selectedBackgroundIds) < 1) {
                return back()->withInput()->with('errorMessage', "❌ Paket {$request->package_name} harus memilih minimal 1 background.");
            }
            if (count($selectedBackgroundIds) > $maxBackgrounds) {
                return back()->withInput()->with('errorMessage', "❌ Paket {$request->package_name} hanya membolehkan maksimal {$maxBackgrounds} background.");
            }

            $backgrounds = $this->fetchBackgroundsByIds($selectedBackgroundIds);
        }

        // Normalisasi booking_date
        try {
            $bookingDate = Carbon::parse($request->booking_date)->toDateString();
        } catch (\Throwable $e) {
            $bookingDate = $request->booking_date;
        }

        // Simpan booking di transaction
        try {
            $booking = DB::transaction(function () use ($request, $backgrounds, $extras, $totalPrice, $normalizedPhone, $paymentProofPath, $bookingDate) {
                // Tentukan status & payment_deadline
                $status = Booking::STATUS_BOOKED;
                $payment_deadline = null;
                if ($request->input('payment_method') === 'transfer') {
                    if (!empty($paymentProofPath)) {
                        $status = Booking::STATUS_PENDING_VERIFICATION;
                    } else {
                        $status = Booking::STATUS_WAITING_PAYMENT;
                        $payment_deadline = now()->addMinutes(10); // aturan default 10 menit (bisa diubah)
                    }
                }

                return Booking::create([
                    'user_id'              => auth('web')->id(),
                    'customer_id'          => $request->customer_id,
                    'contact_name'         => trim($request->contact_name),
                    'whatsapp_number'      => $normalizedPhone,
                    'booking_date'         => $bookingDate,
                    'booking_time'         => $request->booking_time,
                    'session_name'         => $request->input('session_name'),
                    'package_name'         => trim($request->package_name),
                    'selected_backgrounds' => $backgrounds,
                    'selected_extra_items' => $extras,
                    'total_price'          => $totalPrice,
                    'status'               => $status,
                    'payment_method'       => $request->payment_method,
                    'payment_proof'        => $paymentProofPath,
                    'payment_deadline'     => $payment_deadline,
                    'notes'                => $request->input('notes'),
                    'baby_name'            => $request->input('baby_name'),
                    'baby_age'             => $request->input('baby_age'),
                ]);
            });

            return redirect()->route('bookings.index')->with('successMessage', '✅ Booking #' . $booking->id . ' berhasil ditambahkan.');
        } catch (QueryException $qe) {
            // kemungkinan duplicate key pada booking_date + booking_time jika index unik dibuat
            $msg = $qe->getMessage();
            if (Str::contains($msg, 'bookings_date_time_unique') || Str::contains($msg, 'Duplicate entry') || Str::contains($msg, 'unique constraint')) {
                return back()->withInput()->with('errorMessage', '❌ Slot sudah terisi, silakan pilih waktu lain.');
            }
            Log::error('Admin booking gagal (query): ' . $qe->getMessage(), ['exception' => $qe]);
            return back()->withInput()->with('errorMessage', 'Terjadi kesalahan saat menyimpan booking.');
        } catch (\Exception $e) {
            Log::error('Admin booking gagal: ' . $e->getMessage(), ['exception' => $e]);
            return back()->withInput()->with('errorMessage', 'Terjadi kesalahan saat menyimpan booking.');
        }
    }

    public function edit($id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->status !== Booking::STATUS_BOOKED) {
            return redirect()->route('bookings.index')
                ->with('errorMessage', '❌ Hanya booking dengan status "Sudah Dibooking" yang bisa di-edit.');
        }

        $customers = Customer::all();

        // Ambil background & extras sama seperti create
        $backgroundItems = Background::where('is_active', true)->get();
        $babySmashBackgrounds  = $backgroundItems->where('category', 'baby-smash')->values();
        $plainBackgrounds      = $backgroundItems->where('category', 'plain')->values();
        $grandeBackgrounds     = $backgroundItems->where('category', 'grande')->values();
        $royalBackgrounds      = $backgroundItems->where('category', 'royal')->values();
        $prewedBackgrounds     = $backgroundItems->where('category', 'pre-wedding')->values();
        $familyBackgrounds     = $backgroundItems->where('category', 'family')->values();
        $graduationBackgrounds = $backgroundItems->where('category', 'graduation')->values();
        $maternityBackgrounds  = $backgroundItems->where('category', 'maternity')->values();

        $extraItems = ExtraItem::where('is_active', true)->get();
        $printItems = $extraItems->where('category', 'cetak-foto')->values();
        $frameItems = $extraItems->where('category', 'frame-foto')->values();
        $serviceItems = $extraItems->where('category', 'tambahan-layanan')->values();

        // selected backgrounds & extras from booking (normalized)
        $selectedBackgrounds = $booking->selected_backgrounds ?? [];
        if (!is_array($selectedBackgrounds)) $selectedBackgrounds = [];

        $selectedExtraItems = [];
        if (!empty($booking->selected_extra_items)) {
            $selectedExtraItems = collect($booking->selected_extra_items)->pluck('id')->toArray();
        }

        $defaultDate = old('booking_date', $booking->booking_date ?? Carbon::now()->toDateString());
        $availableTimes = Booking::getAvailableTimes($defaultDate);

        return view('admin.bookings.edit', compact(
            'booking',
            'customers',
            'backgroundItems',
            'babySmashBackgrounds',
            'plainBackgrounds',
            'grandeBackgrounds',
            'royalBackgrounds',
            'prewedBackgrounds',
            'familyBackgrounds',
            'graduationBackgrounds',
            'maternityBackgrounds',
            'printItems',
            'frameItems',
            'serviceItems',
            'selectedBackgrounds',
            'selectedExtraItems',
            'availableTimes'
        ));
    }

    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->status !== Booking::STATUS_BOOKED) {
            return redirect()->route('bookings.index')
                ->with('errorMessage', '❌ Hanya booking dengan status "Sudah Dibooking" yang bisa di-edit.');
        }

        // Normalisasi WA
        if ($request->filled('whatsapp_number')) {
            $request->merge(['whatsapp_number' => $this->normalizePhone($request->input('whatsapp_number'))]);
        }

        // Determine incoming package for validation / max backgrounds
        $incomingPackage = $request->has('package_name') ? (string)$request->input('package_name') : (string)$booking->package_name;
        $incomingKey = Str::lower($incomingPackage);
        $isGroupPackage = in_array($incomingKey, ['plain', 'grande', 'royal'], true);
        $isBabySmash = in_array($incomingKey, ['baby smash cake', 'babysmash'], true);

        // Validation rules (sometimes to allow partial update)
        $rules = [
            'customer_id'     => 'sometimes|exists:customers,id',
            'contact_name'    => 'sometimes|string|max:100',
            'whatsapp_number' => 'sometimes|string|max:25',
            'booking_date'    => 'sometimes|date|after_or_equal:today',
            'booking_time'    => 'sometimes|string',
            'session_name'    => $isGroupPackage ? 'sometimes|required|in:family,graduation,maternity' : 'sometimes|nullable|string|max:100',
            'package_name'    => 'sometimes|string|max:100',
            'payment_method'  => 'sometimes|in:cash,transfer',
            'selected_extra_items' => 'sometimes',
            'payment_proof'   => $request->hasFile('payment_proof') ? 'image|mimes:jpg,jpeg,png|max:2048' : 'sometimes',
            'baby_name'       => 'sometimes|nullable|string|max:255',
            'baby_age'        => 'sometimes|nullable|string|max:50',
            'selected_backgrounds' => 'sometimes',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }

        // Validate booking time if changed
        if ($request->has('booking_time')) {
            $allTimes = Booking::getAllTimes();
            if (!in_array($request->booking_time, $allTimes)) {
                return back()->withInput()->with('errorMessage', 'Waktu booking tidak valid.');
            }
        }

        // If date/time changed, check availability
        $dateChanged = $request->has('booking_date') && $request->input('booking_date') != $booking->booking_date;
        $timeChanged = $request->has('booking_time') && $request->input('booking_time') != $booking->booking_time;
        if (($dateChanged || $timeChanged) && $request->filled('booking_date') && $request->filled('booking_time')) {
            if (!Booking::isSlotAvailable($request->booking_date, $request->booking_time, $booking->id)) {
                return back()->withInput()->with('errorMessage', '❌ Slot sudah terisi, silakan pilih waktu lain.');
            }
        }

        // Extras
        $extras = $booking->selected_extra_items ?? [];
        $extrasChanged = false;
        if ($request->has('selected_extra_items')) {
            $extras = $this->fetchExtrasByIds($request->input('selected_extra_items', []));
            $extrasChanged = true;
        }

        // Package price & change detection
        $packagePrice = $this->getPackagePrice($incomingPackage);
        $packageChanged = $request->has('package_name') && $request->input('package_name') != $booking->package_name;

        // Recompute total when necessary
        if ($packageChanged || $extrasChanged) {
            $extraItemsPrice = array_sum(array_map(fn($e) => (int)($e['price'] ?? 0), $extras));
            $totalPrice = $packagePrice + $extraItemsPrice;
        } else {
            $totalPrice = $booking->total_price ?? 0;
        }

        // Payment proof upload handling
        $paymentProofPath = $booking->payment_proof;
        if ($request->hasFile('payment_proof')) {
            $paymentProofPath = $this->storeUploadedFile($request->file('payment_proof'), 'payment_proofs', $booking->payment_proof);
        }

        // Backgrounds: if provided, validate counts
        $backgrounds = $booking->selected_backgrounds ?? [];
        if ($request->has('selected_backgrounds')) {
            $selectedBackgroundIds = $this->normalizeSelectedIds($request->input('selected_backgrounds', []));
            $maxBackgrounds = $this->getMaxBackgrounds($incomingPackage);

            if (!in_array($incomingKey, ['baby smash cake', 'babysmash'])) {
                if (count($selectedBackgroundIds) < 1) {
                    return back()->withInput()->with('errorMessage', "❌ Paket {$incomingPackage} harus memilih minimal 1 background.");
                }
                if (count($selectedBackgroundIds) > $maxBackgrounds) {
                    return back()->withInput()->with('errorMessage', "❌ Paket {$incomingPackage} hanya membolehkan maksimal {$maxBackgrounds} background.");
                }
            }

            $backgrounds = $this->fetchBackgroundsByIds($selectedBackgroundIds);
        } else {
            // Jika admin mengganti paket ke paket yang memerlukan background tapi booking lama tidak punya background
            if ($packageChanged && !in_array($incomingKey, ['baby smash cake', 'babysmash'])) {
                $existingBg = $booking->selected_backgrounds ?? [];
                if (empty($existingBg)) {
                    return back()->withInput()->with('errorMessage', "❌ Paket {$incomingPackage} memerlukan pemilihan background. Mohon pilih minimal 1 background.");
                }
            }
        }

        // Build update payload for provided fields
        $updateData = [];
        $maybeFields = [
            'customer_id',
            'contact_name',
            'whatsapp_number',
            'booking_date',
            'booking_time',
            'session_name',
            'package_name',
            'payment_method',
            'notes',
            'baby_name',
            'baby_age'
        ];
        foreach ($maybeFields as $field) {
            if ($request->has($field)) {
                $val = $request->input($field);
                if (is_string($val)) $val = trim($val);
                if ($field === 'booking_date') {
                    try {
                        $val = Carbon::parse($val)->toDateString();
                    } catch (\Throwable $e) { /* ignore */
                    }
                }
                $updateData[$field] = $val;
            }
        }

        if ($request->has('selected_backgrounds')) {
            $updateData['selected_backgrounds'] = $backgrounds;
        }

        if ($request->has('selected_extra_items')) {
            $updateData['selected_extra_items'] = $extras;
        }

        if ($packageChanged || $extrasChanged) {
            $updateData['total_price'] = $totalPrice;
        }

        if ($request->hasFile('payment_proof')) {
            $updateData['payment_proof'] = $paymentProofPath;
        }

        // Apply update
        try {
            DB::transaction(function () use (&$booking, $updateData) {
                if (!empty($updateData)) {
                    $booking->update($updateData);
                }
                $booking->refresh();
            });

            return redirect()->route('bookings.index')->with('successMessage', '✅ Booking #' . $booking->id . ' berhasil diperbarui.');
        } catch (QueryException $qe) {
            $msg = $qe->getMessage();
            if (Str::contains($msg, 'bookings_date_time_unique') || Str::contains($msg, 'Duplicate entry') || Str::contains($msg, 'unique constraint')) {
                return back()->withInput()->with('errorMessage', '❌ Slot sudah terisi, silakan pilih waktu lain.');
            }
            Log::error('Admin update booking gagal (query): ' . $qe->getMessage(), ['booking_id' => $id, 'exception' => $qe]);
            return back()->withInput()->with('errorMessage', 'Terjadi kesalahan saat memperbarui booking. Silakan coba lagi.');
        } catch (\Exception $e) {
            Log::error('Admin update booking gagal: ' . $e->getMessage(), ['booking_id' => $id, 'exception' => $e]);
            return back()->withInput()->with('errorMessage', 'Terjadi kesalahan saat memperbarui booking. Silakan coba lagi.');
        }
    }

    // -------------------------
    // Helper methods (sama seperti sebelumnya)
    // -------------------------

    private function getPackagePrice($packageName)
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
                // fallback cases if needed
            default:
                return 0;
        }
    }

    private function getMaxBackgrounds($packageName)
    {
        switch (Str::lower((string)$packageName)) {
            case 'baby smash cake':
            case 'babysmash':
                return 0;
            case 'plain':
                return 1;
            case 'grande':
                return 2;
            case 'royal':
                return 4;
            case 'prewed i':
            case 'prewed1':
                return 2;
            case 'prewed ii':
            case 'prewed2':
                return 3;
            default:
                return 1;
        }
    }

    private function normalizePhone($raw)
    {
        $s = (string)$raw;
        $s = preg_replace('/[^\d+]/', '', $s);

        if (Str::startsWith($s, '+')) {
            return $s;
        }

        if (Str::startsWith($s, '62')) {
            return '+' . $s;
        }

        if (Str::startsWith($s, '0')) {
            return '+62' . substr($s, 1);
        }

        return '+62' . $s;
    }

    private function normalizeSelectedIds($value): array
    {
        if (is_null($value)) return [];

        if (is_string($value)) {
            $value = trim($value);
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

        $arr = array_values(array_filter($arr, function ($v) {
            return $v !== null && $v !== '';
        }));
        $arr = array_map(fn($v) => is_numeric($v) ? (int)$v : $v, $arr);
        $arr = array_values(array_unique($arr, SORT_REGULAR));

        return $arr;
    }

    private function fetchExtrasByIds($idsInput = [])
    {
        $ids = $this->normalizeSelectedIds($idsInput);
        if (empty($ids)) return [];

        $items = ExtraItem::whereIn('id', $ids)->get(['id', 'name', 'price']);
        return $items->map(fn($it) => ['id' => $it->id, 'name' => $it->name, 'price' => (int)$it->price])->toArray();
    }

    private function fetchBackgroundsByIds($idsInput = [])
    {
        $ids = $this->normalizeSelectedIds($idsInput);
        if (empty($ids)) return [];

        $bgs = Background::whereIn('id', $ids)->get(['id', 'name', 'image']);
        return $bgs->map(fn($bg) => ['id' => $bg->id, 'name' => $bg->name, 'image' => $bg->image])->toArray();
    }

    private function storeUploadedFile($file, $folder = 'uploads', $oldPath = null)
    {
        try {
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
            $path = $file->store($folder, 'public');
            return $path;
        } catch (\Exception $e) {
            Log::error("Gagal menyimpan file upload: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Batalkan booking (admin)
     */
    public function cancelBooking(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        if (in_array($booking->status, [Booking::STATUS_COMPLETED, Booking::STATUS_CANCELLED])) {
            return back()->with('errorMessage', '❌ Booking tidak dapat dibatalkan pada status saat ini.');
        }

        $request->validate([
            'cancellation_reason' => 'nullable|string|max:255',
        ]);

        $reason = $request->input('cancellation_reason') ?? 'Dibatalkan oleh admin';

        try {
            DB::transaction(function () use ($booking, $reason) {
                $booking->update([
                    'status' => Booking::STATUS_CANCELLED,
                    'auto_cancelled_at' => now(),
                    'cancellation_reason' => $reason,
                    'cancellation_requested' => false,
                    'cancellation_requested_at' => null,
                ]);
            });

            Log::info("Admin membatalkan booking #{$booking->id}", [
                'booking_id' => $booking->id,
                'admin_id' => auth('web')->id(),
                'reason' => $reason,
            ]);

            return back()->with('successMessage', '✅ Booking #' . $booking->id . ' berhasil dibatalkan.');
        } catch (\Exception $e) {
            Log::error('Gagal membatalkan booking (admin): ' . $e->getMessage(), [
                'booking_id' => $booking->id,
                'exception' => $e,
            ]);
            return back()->with('errorMessage', 'Terjadi kesalahan saat membatalkan booking. Silakan coba lagi.');
        }
    }

    /**
     * Paksa batalkan booking (admin override)
     */
    public function forceCancel(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        if (in_array($booking->status, [Booking::STATUS_COMPLETED, Booking::STATUS_CANCELLED])) {
            return back()->with('errorMessage', '❌ Booking tidak dapat dibatalkan pada status saat ini.');
        }

        $request->validate([
            'cancellation_reason' => 'nullable|string|max:255',
        ]);

        $reason = $request->input('cancellation_reason') ?? 'Dibatalkan paksa oleh admin';

        try {
            DB::transaction(function () use ($booking, $reason) {
                $booking->update([
                    'status' => Booking::STATUS_CANCELLED,
                    'auto_cancelled_at' => now(),
                    'cancellation_reason' => $reason,
                    'cancellation_requested' => false,
                    'cancellation_requested_at' => null,
                ]);
            });

            Log::warning("Admin paksa membatalkan booking #{$booking->id}", [
                'booking_id' => $booking->id,
                'admin_id' => auth('web')->id(),
                'reason' => $reason,
            ]);

            return back()->with('successMessage', '✅ Booking #' . $booking->id . ' berhasil dibatalkan paksa.');
        } catch (\Exception $e) {
            Log::error('Gagal paksa membatalkan booking (admin): ' . $e->getMessage(), [
                'booking_id' => $booking->id,
                'exception' => $e,
            ]);
            return back()->with('errorMessage', 'Terjadi kesalahan saat membatalkan booking. Silakan coba lagi.');
        }
    }

    /**
     * Verifikasi pembayaran
     */
    public function verifyPayment(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->status !== Booking::STATUS_PENDING_VERIFICATION) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Booking tidak dalam status menunggu verifikasi.'], 422);
            }
            return back()->with('errorMessage', '❌ Booking tidak dalam status menunggu verifikasi.');
        }

        $booking->update(['status' => Booking::STATUS_BOOKED]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Booking diverifikasi dan dikonfirmasi.', 'booking_id' => $booking->id]);
        }

        return back()->with('successMessage', '✅ Booking #' . $booking->id . ' berhasil diverifikasi dan dikonfirmasi.');
    }

    /**
     * Tandai booking selesai
     */
    public function completeBooking(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->status !== Booking::STATUS_BOOKED) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Booking tidak dalam status bisa ditandai selesai.'], 422);
            }
            return back()->with('errorMessage', '❌ Booking tidak dalam status bisa ditandai selesai.');
        }

        $booking->update(['status' => Booking::STATUS_COMPLETED]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Booking ditandai selesai.', 'booking_id' => $booking->id]);
        }

        return back()->with('successMessage', '✅ Booking #' . $booking->id . ' berhasil ditandai selesai.');
    }

    /**
     * Proses pembatalan + refund (admin)
     * Endpoint digunakan oleh form proses pembatalan (approve flow).
     * Validasi memastikan refund_amount <= total_price.
     */
    public function processCancellation(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $rules = [
            'refund_amount' => 'nullable|numeric|min:0',
            'refund_proof' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'cancellation_reason' => 'nullable|string|max:255',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
            }
            return back()->withInput()->withErrors($validator->errors());
        }

        $inputRefund = $request->input('refund_amount', null);
        $totalPrice = (float) ($booking->total_price ?? 0);

        // jika tidak diberikan, hitung default 90%
        $refundAmount = is_null($inputRefund) ? round($totalPrice * 0.90, 2) : (float) $inputRefund;

        if ($refundAmount < 0 || $refundAmount > $totalPrice) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Nilai refund tidak valid.'], 422);
            }
            return back()->with('errorMessage', '❌ Nilai refund tidak valid.');
        }

        // deklarasi refundPath di luar closure agar tidak undefined
        $refundPath = null;

        try {
            DB::transaction(function () use ($booking, $request, &$refundPath, $refundAmount) {
                if ($request->hasFile('refund_proof')) {
                    $refundPath = $this->storeUploadedFile($request->file('refund_proof'), 'refund_proofs', $booking->refund_proof ?? null);
                }

                $booking->update([
                    'status' => Booking::STATUS_CANCELLED,
                    'refund_amount' => $refundAmount,
                    'refund_proof' => $refundPath,
                    'auto_cancelled_at' => now(),
                    'cancellation_reason' => $request->input('cancellation_reason', $booking->cancellation_reason),
                    'cancellation_requested' => false,
                    'cancellation_requested_at' => null,
                ]);
            });

            Log::info("Admin memproses pembatalan booking #{$booking->id}. Refund: {$refundAmount}", [
                'booking_id' => $booking->id,
                'admin_id' => auth('web')->id(),
                'refund_amount' => $refundAmount,
            ]);

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Booking dibatalkan dan refund diproses.', 'booking_id' => $booking->id]);
            }

            return back()->with('successMessage', '✅ Booking #' . $booking->id . ' berhasil dibatalkan dengan refund.');
        } catch (QueryException $qe) {
            Log::error('Gagal memproses refund (query): ' . $qe->getMessage(), [
                'booking_id' => $booking->id,
                'exception' => $qe,
            ]);
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Terjadi kesalahan saat memproses refund.'], 500);
            }
            return back()->with('errorMessage', 'Terjadi kesalahan saat memproses refund. Silakan coba lagi.');
        } catch (\Exception $e) {
            Log::error('Gagal memproses refund (admin): ' . $e->getMessage(), [
                'booking_id' => $booking->id,
                'exception' => $e,
            ]);
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Terjadi kesalahan saat memproses refund.'], 500);
            }
            return back()->with('errorMessage', 'Terjadi kesalahan saat memproses refund. Silakan coba lagi.');
        }
    }

    /**
     * ADMIN: Approve cancellation request quickly (helper)
     */
    public function approveCancellation(Request $request, $id)
    {
        return $this->processCancellation($request, $id);
    }

    /**
     * ADMIN: Reject cancellation request
     */
    public function rejectCancellation(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        // allow admin to reject even if the request flag is not set (admin-initiated rejection)
        $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $reason = $request->input('rejection_reason', 'Permohonan pembatalan ditolak oleh admin');

        try {
            DB::transaction(function () use ($booking, $reason) {
                $existingNotes = $booking->notes ?? '';
                $newNotes = trim(($existingNotes ? $existingNotes . "\n\n" : '') . '[Pembatalan Ditolak] ' . $reason);

                $booking->update([
                    'cancellation_requested' => false,
                    'cancellation_requested_at' => null,
                    'notes' => $newNotes,
                ]);
            });

            Log::info("Admin menolak pembatalan booking #{$booking->id}", [
                'booking_id' => $booking->id,
                'admin_id' => auth('web')->id(),
                'reason' => $reason,
            ]);

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Permohonan pembatalan berhasil ditolak.', 'booking_id' => $booking->id]);
            }

            return back()->with('successMessage', '✅ Permohonan pembatalan berhasil ditolak.');
        } catch (\Exception $e) {
            Log::error('Gagal menolak pembatalan (admin): ' . $e->getMessage(), [
                'booking_id' => $booking->id,
                'exception' => $e,
            ]);
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Terjadi kesalahan saat menolak permohonan pembatalan.'], 500);
            }
            return back()->with('errorMessage', 'Terjadi kesalahan saat menolak permohonan pembatalan. Silakan coba lagi.');
        }
    }

    /**
     * Hitungan permohonan pembatalan (untuk badge/notifications)
     */
    public function pendingCancellationCount()
    {
        $count = Booking::where('cancellation_requested', true)->count();
        return response()->json(['count' => $count]);
    }

    /**
     * Simpan booking manual dengan validasi & hitung ulang harga
     * (tidak diubah - tetap seperti sebelumnya)
     */


    /**
     * HAPUS booking - hanya untuk status 'completed' atau 'cancelled'
     */
    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);

        if (!in_array($booking->status, [Booking::STATUS_COMPLETED, Booking::STATUS_CANCELLED])) {
            return back()->with('errorMessage', '❌ Hanya pesanan dengan status "Selesai" atau "Dibatalkan" yang boleh dihapus.');
        }

        try {
            DB::transaction(function () use ($booking) {
                if (!empty($booking->payment_proof) && Storage::disk('public')->exists($booking->payment_proof)) {
                    Storage::disk('public')->delete($booking->payment_proof);
                }

                if (!empty($booking->refund_proof) && Storage::disk('public')->exists($booking->refund_proof)) {
                    Storage::disk('public')->delete($booking->refund_proof);
                }

                $booking->delete();
            });

            Log::info("Admin menghapus booking #{$booking->id}", [
                'booking_id' => $booking->id,
                'admin_id' => auth('web')->id(),
            ]);

            return redirect()->route('bookings.index')->with('successMessage', '✅ Booking #' . $booking->id . ' berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus booking: ' . $e->getMessage(), [
                'booking_id' => $booking->id,
                'exception' => $e,
            ]);
            return back()->with('errorMessage', 'Terjadi kesalahan saat menghapus booking. Silakan coba lagi.');
        }
    }

    // -------------------------
    // Helper methods (unchanged)
    // -------------------------

}
