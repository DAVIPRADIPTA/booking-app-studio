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
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

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
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('contact_name', 'like', "%{$q}%")
                    ->orWhere('whatsapp_number', 'like', "%{$q}%")
                    ->orWhere('package_name', 'like', "%{$q}%");
            });
        }

        // filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
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

        // **sorting** (DEFAULT: created_at desc — Dibuat baru -> lama)
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
                // default: created_at desc
                $query->orderBy('created_at', 'desc');
                break;
        }

        // per-page
        $perPage = (int) $request->get('per_page', 10);
        if ($perPage <= 0) $perPage = 10;

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

        $bookings = $query->paginate($perPage)->withQueryString();

        return view('admin.bookings.index', compact('bookings', 'statusCounts', 'packages'));
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
     * Form tambah booking manual
     */
    public function create()
    {
        $customers = Customer::all();

        // Group Backgrounds by Category
        $backgroundItems = Background::where('is_active', true)->get();
        $babySmashBackgrounds = $backgroundItems->where('category', 'baby-smash')->values();
        $plainBackgrounds = $backgroundItems->where('category', 'plain')->values();
        $grandeBackgrounds = $backgroundItems->where('category', 'grande')->values();
        $royalBackgrounds = $backgroundItems->where('category', 'royal')->values();
        $prewedBackgrounds = $backgroundItems->where('category', 'pre-wedding')->values();
        $familyBackgrounds = $backgroundItems->where('category', 'family')->values();
        $graduationBackgrounds = $backgroundItems->where('category', 'graduation')->values();

        // Group Extra Items by Category
        $extraItems = ExtraItem::where('is_active', true)->get();
        $printItems = $extraItems->where('category', 'cetak-foto')->values();
        $frameItems = $extraItems->where('category', 'frame-foto')->values();
        $serviceItems = $extraItems->where('category', 'tambahan-layanan')->values();

        // Normalize old inputs for blade/js
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

        // Available times for default date (used to populate the time select initially)
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
            'printItems',
            'frameItems',
            'serviceItems',
            'selectedBackgrounds',
            'selectedExtraItems',
            'availableTimes'
        ));
    }

    /**
     * Form edit booking - HANYA UNTUK STATUS 'booked'
     */
    public function edit($id)
    {
        $booking = Booking::findOrFail($id);

        // Hanya izinkan edit untuk booking dengan status 'booked'
        if ($booking->status !== 'booked') {
            return redirect()->route('bookings.index')
                ->with('errorMessage', '❌ Hanya booking dengan status "Sudah Dibooking" yang bisa di-edit.');
        }

        $customers = Customer::all();

        // Group Backgrounds by Category
        $backgroundItems = Background::where('is_active', true)->get();
        $babySmashBackgrounds = $backgroundItems->where('category', 'baby-smash')->values();
        $plainBackgrounds = $backgroundItems->where('category', 'plain')->values();
        $grandeBackgrounds = $backgroundItems->where('category', 'grande')->values();
        $royalBackgrounds = $backgroundItems->where('category', 'royal')->values();
        $prewedBackgrounds = $backgroundItems->where('category', 'pre-wedding')->values();
        $familyBackgrounds = $backgroundItems->where('category', 'family')->values();
        $graduationBackgrounds = $backgroundItems->where('category', 'graduation')->values();

        // Group Extra Items by Category
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

        // Available times based on booking date
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
            'printItems',
            'frameItems',
            'serviceItems',
            'selectedBackgrounds',
            'selectedExtraItems',
            'availableTimes'
        ));
    }

    /**
     * Update booking - HANYA UNTUK STATUS 'booked'
     *
     * Mendukung partial update:
     * - Hanya field yang dikirim akan di-update.
     * - Cek slot bentrok hanya kalau tanggal atau jam berubah.
     * - File payment_proof hanya menimpa jika ada upload baru.
     */
    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        // Hanya izinkan update untuk booking dengan status 'booked'
        if ($booking->status !== 'booked') {
            return redirect()->route('bookings.index')
                ->with('errorMessage', '❌ Hanya booking dengan status "Sudah Dibooking" yang bisa di-edit.');
        }

        // jika nomor WA dikirim -> normalisasi
        if ($request->filled('whatsapp_number')) {
            $normalizedPhone = $this->normalizePhone($request->input('whatsapp_number'));
            $request->merge(['whatsapp_number' => $normalizedPhone]);
        }

        // Tentukan paket target (incoming atau fallback ke existing)
        $incomingPackage = $request->has('package_name') ? (string) $request->input('package_name') : (string) $booking->package_name;
        $isBabySmash = in_array(Str::lower($incomingPackage), ['baby smash cake', 'babysmash']);

        // Rules 'sometimes' agar partial update dimungkinkan
        $rules = [
            'customer_id'     => 'sometimes|exists:customers,id',
            'contact_name'    => 'sometimes|string|max:100',
            'whatsapp_number' => 'sometimes|string|max:20',
            'booking_date'    => 'sometimes|date|after_or_equal:today',
            'booking_time'    => 'sometimes|string',
            'session_name'    => 'sometimes|string|max:100',
            'package_name'    => 'sometimes|string|max:100',
            'payment_method'  => 'sometimes|in:cash,transfer',
            'selected_extra_items' => 'sometimes',
            // payment_proof only validated if a new file is uploaded
            'payment_proof'   => $request->hasFile('payment_proof') ? 'image|mimes:jpg,jpeg,png|max:2048' : 'sometimes',
            'baby_name'       => 'sometimes|nullable|string|max:255',
            'baby_age'        => 'sometimes|nullable|string|max:50',
            'selected_backgrounds' => 'sometimes',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }

        // booking_time validity if provided
        if ($request->has('booking_time')) {
            $allTimes = Booking::getAllTimes();
            if (!in_array($request->booking_time, $allTimes)) {
                return back()->withInput()->with('errorMessage', 'Waktu booking tidak valid.');
            }
        }

        // check slot availability only if date or time changed (and both provided)
        $dateChanged = $request->has('booking_date') && $request->input('booking_date') != $booking->booking_date;
        $timeChanged = $request->has('booking_time') && $request->input('booking_time') != $booking->booking_time;
        if (($dateChanged || $timeChanged) && $request->filled('booking_date') && $request->filled('booking_time')) {
            if (!Booking::isSlotAvailable($request->booking_date, $request->booking_time, $id)) {
                return back()->withInput()->with('errorMessage', '❌ Slot sudah terisi, silakan pilih waktu lain.');
            }
        }

        // Extras handling: if provided in request, fetch from DB, otherwise keep existing
        $extras = $booking->selected_extra_items ?? [];
        $extrasChanged = false;
        if ($request->has('selected_extra_items')) {
            $extras = $this->fetchExtrasByIds($request->input('selected_extra_items', []));
            $extrasChanged = true;
        }

        // Package price: use incomingPackage (either new or existing)
        $packagePrice = $this->getPackagePrice($incomingPackage);
        $packageChanged = $request->has('package_name') && $request->input('package_name') != $booking->package_name;

        // Compute totalPrice if package or extras changed; otherwise, keep existing total
        if ($packageChanged || $extrasChanged) {
            $extraItemsPrice = array_sum(array_map(function ($e) { return (int) ($e['price'] ?? 0); }, $extras));
            $totalPrice = $packagePrice + $extraItemsPrice;
        } else {
            $totalPrice = $booking->total_price ?? 0;
        }

        // Handle payment_proof upload: keep old if not uploaded
        $paymentProofPath = $booking->payment_proof;
        if ($request->hasFile('payment_proof')) {
            $paymentProofPath = $this->storeUploadedFile($request->file('payment_proof'), 'payment_proofs', $booking->payment_proof);
        }

        // Backgrounds: if provided, normalize & validate counts; otherwise, keep existing
        $backgrounds = $booking->selected_backgrounds ?? [];
        if ($request->has('selected_backgrounds')) {
            $selectedBackgroundIds = $this->normalizeSelectedIds($request->input('selected_backgrounds', []));
            $maxBackgrounds = $this->getMaxBackgrounds($incomingPackage);

            if (!in_array(Str::lower($incomingPackage), ['baby smash cake', 'babysmash'])) {
                if (count($selectedBackgroundIds) < 1) {
                    return back()->withInput()->with('errorMessage', "❌ Paket {$incomingPackage} harus memilih minimal 1 background.");
                }
                if (count($selectedBackgroundIds) > $maxBackgrounds) {
                    return back()->withInput()->with('errorMessage', "❌ Paket {$incomingPackage} hanya membolehkan maksimal {$maxBackgrounds} background.");
                }
            }
            $backgrounds = $this->fetchBackgroundsByIds($selectedBackgroundIds);
        } else {
            // jika paket berubah ke paket yang butuh background dan booking sebelumnya tidak punya background -> force admin memilih
            if ($packageChanged && !in_array(Str::lower($incomingPackage), ['baby smash cake', 'babysmash'])) {
                $existingBg = $booking->selected_backgrounds ?? [];
                if (empty($existingBg)) {
                    return back()->withInput()->with('errorMessage', "❌ Paket {$incomingPackage} memerlukan pemilihan background. Mohon pilih minimal 1 background.");
                }
            }
        }

        // Build update payload (only include fields that were sent)
        $updateData = [];

        $maybeFields = [
            'customer_id', 'contact_name', 'whatsapp_number', 'booking_date', 'booking_time',
            'session_name', 'package_name', 'payment_method', 'notes', 'baby_name', 'baby_age'
        ];

        foreach ($maybeFields as $field) {
            if ($request->has($field)) {
                $updateData[$field] = $request->input($field);
            }
        }

        // add backgrounds / extras if provided
        if ($request->has('selected_backgrounds')) {
            $updateData['selected_backgrounds'] = $backgrounds;
        }

        if ($request->has('selected_extra_items')) {
            $updateData['selected_extra_items'] = $extras;
        }

        // set computed total_price if changed
        if ($packageChanged || $extrasChanged) {
            $updateData['total_price'] = $totalPrice;
        }

        // set payment_proof if a new file uploaded
        if ($request->hasFile('payment_proof')) {
            $updateData['payment_proof'] = $paymentProofPath;
        }

        // Perform update inside transaction
        try {
            DB::transaction(function () use (&$booking, $updateData) {
                if (!empty($updateData)) {
                    $booking->update($updateData);
                }
                $booking->refresh();
            });

            return redirect()->route('bookings.index')->with('successMessage', '✅ Booking #' . $booking->id . ' berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Admin update booking gagal: ' . $e->getMessage(), [
                'booking_id' => $id,
                'exception' => $e,
            ]);

            return back()->withInput()->with('errorMessage', 'Terjadi kesalahan saat memperbarui booking. Silakan coba lagi.');
        }
    }

    /**
     * Batalkan booking (admin)
     */
    public function cancelBooking(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        if (in_array($booking->status, ['completed', 'cancelled'])) {
            return back()->with('errorMessage', '❌ Booking tidak dapat dibatalkan pada status saat ini.');
        }

        $request->validate([
            'cancellation_reason' => 'nullable|string|max:255',
        ]);

        $reason = $request->input('cancellation_reason') ?? 'Dibatalkan oleh admin';

        try {
            DB::transaction(function () use ($booking, $reason) {
                $booking->update([
                    'status' => 'cancelled',
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

        if (in_array($booking->status, ['completed', 'cancelled'])) {
            return back()->with('errorMessage', '❌ Booking tidak dapat dibatalkan pada status saat ini.');
        }

        $request->validate([
            'cancellation_reason' => 'nullable|string|max:255',
        ]);

        $reason = $request->input('cancellation_reason') ?? 'Dibatalkan paksa oleh admin';

        try {
            DB::transaction(function () use ($booking, $reason) {
                $booking->update([
                    'status' => 'cancelled',
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

        if ($booking->status !== 'pending_verification') {
            return back()->with('errorMessage', '❌ Booking tidak dalam status menunggu verifikasi.');
        }

        $booking->update(['status' => 'booked']);

        return back()->with('successMessage', '✅ Booking #' . $booking->id . ' berhasil diverifikasi dan dikonfirmasi.');
    }

    /**
     * Tandai booking selesai
     */
    public function completeBooking(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->status !== 'booked') {
            return back()->with('errorMessage', '❌ Booking tidak dalam status bisa ditandai selesai.');
        }

        $booking->update(['status' => 'completed']);

        return back()->with('successMessage', '✅ Booking #' . $booking->id . ' berhasil ditandai selesai.');
    }

    /**
     * Proses pembatalan + refund
     */
    public function processCancellation(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $request->validate([
            'refund_amount' => 'required|numeric|min:0',
            'refund_proof' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'cancellation_reason' => 'required|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($booking, $request, &$refundPath) {
                $refundPath = null;
                if ($request->hasFile('refund_proof')) {
                    $refundPath = $this->storeUploadedFile($request->file('refund_proof'), 'refund_proofs', $booking->refund_proof ?? null);
                }

                $booking->update([
                    'status' => 'cancelled',
                    'refund_amount' => $request->refund_amount,
                    'refund_proof' => $refundPath,
                    'auto_cancelled_at' => now(),
                    'cancellation_reason' => $request->cancellation_reason,
                ]);
            });

            return back()->with('successMessage', '✅ Booking #' . $booking->id . ' berhasil dibatalkan dengan refund.');
        } catch (\Exception $e) {
            Log::error('Gagal memproses refund (admin): ' . $e->getMessage(), [
                'booking_id' => $booking->id,
                'exception' => $e,
            ]);
            return back()->with('errorMessage', 'Terjadi kesalahan saat memproses refund. Silakan coba lagi.');
        }
    }

    /**
     * Simpan booking manual dengan validasi & hitung ulang harga
     */
    public function store(Request $request)
    {
        // Normalisasi nomor WhatsApp
        $normalizedPhone = $this->normalizePhone($request->input('whatsapp_number', ''));
        $request->merge(['whatsapp_number' => $normalizedPhone]);

        $packageName = (string) $request->input('package_name', '');
        $isBabySmash = in_array(Str::lower($packageName), ['baby smash cake', 'babysmash']);

        $rules = [
            'customer_id'          => 'required|exists:customers,id',
            'contact_name'         => 'required|string|max:100',
            'whatsapp_number'      => 'required|string|max:20',
            'booking_date'         => 'required|date|after_or_equal:today',
            'booking_time'         => 'required|string',
            'session_name'         => 'required|string|max:100',
            'package_name'         => 'required|string|max:100',
            'payment_method'       => 'required|in:cash,transfer',
            'selected_extra_items' => 'nullable',
            'payment_proof'        => $request->input('payment_method') === 'transfer' ? 'required|image|mimes:jpg,jpeg,png|max:2048' : 'nullable',
            'baby_name'            => 'nullable|string|max:255',
            'baby_age'             => 'nullable|string|max:50',
        ];

        $rules['selected_backgrounds'] = $isBabySmash ? 'nullable' : 'required';

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }

        // Validasi booking_time valid
        $allTimes = Booking::getAllTimes();
        if (!in_array($request->booking_time, $allTimes)) {
            return back()->withInput()->with('errorMessage', 'Waktu booking tidak valid.');
        }

        // Cek jadwal bentrok
        if (!Booking::isSlotAvailable($request->booking_date, $request->booking_time)) {
            return back()->with('errorMessage', '❌ Slot sudah terisi, silakan pilih waktu lain.')->withInput();
        }

        // Hitung ulang total harga
        $packagePrice = $this->getPackagePrice($request->package_name);

        // Ambil harga extra items dari DB
        $extras = $this->fetchExtrasByIds($request->input('selected_extra_items', []));
        $extraItemsPrice = array_sum(array_map(function ($e) { return (int) ($e['price'] ?? 0); }, $extras));
        $totalPrice = $packagePrice + $extraItemsPrice;

        // Upload bukti transfer jika ada
        $paymentProofPath = null;
        if ($request->input('payment_method') === 'transfer' && $request->hasFile('payment_proof')) {
            $paymentProofPath = $this->storeUploadedFile($request->file('payment_proof'), 'payment_proofs', null);
        }

        // Background terpilih
        $backgrounds = [];
        $maxBackgrounds = $this->getMaxBackgrounds($request->package_name);

        if (!$isBabySmash) {
            $selectedBackgroundIds = $this->normalizeSelectedIds($request->input('selected_backgrounds', []));
            if (count($selectedBackgroundIds) < 1) {
                return back()->with('errorMessage', "❌ Paket {$request->package_name} harus memilih minimal 1 background.")->withInput();
            }
            if (count($selectedBackgroundIds) > $maxBackgrounds) {
                return back()->with('errorMessage', "❌ Paket {$request->package_name} hanya membolehkan maksimal {$maxBackgrounds} background.")->withInput();
            }

            $backgrounds = $this->fetchBackgroundsByIds($selectedBackgroundIds);
        }

        // Simpan booking (transaction)
        try {
            $booking = DB::transaction(function () use ($request, $backgrounds, $extras, $totalPrice, $normalizedPhone, $paymentProofPath) {
                return Booking::create([
                    'user_id'              => auth('web')->id(),
                    'customer_id'          => $request->customer_id,
                    'contact_name'         => $request->contact_name,
                    'whatsapp_number'      => $normalizedPhone,
                    'booking_date'         => $request->booking_date,
                    'booking_time'         => $request->booking_time,
                    'session_name'         => $request->session_name,
                    'package_name'         => $request->package_name,
                    'selected_backgrounds' => $backgrounds,
                    'selected_extra_items' => $extras,
                    'total_price'          => $totalPrice,
                    'status'               => 'booked', // admin langsung booked
                    'payment_method'       => $request->payment_method,
                    'payment_proof'        => $paymentProofPath,
                    'payment_deadline'     => null,
                    'notes'                => $request->notes,
                    'baby_name'            => $request->baby_name,
                    'baby_age'             => $request->baby_age,
                ]);
            });

            return redirect()->route('bookings.index')->with('successMessage', '✅ Booking #' . $booking->id . ' berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Admin booking gagal: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
            return back()->withInput()->with('errorMessage', 'Terjadi kesalahan saat menyimpan booking.');
        }
    }

    /**
     * HAPUS booking - hanya untuk status 'completed' atau 'cancelled'
     */
    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);

        if (!in_array($booking->status, ['completed', 'cancelled'])) {
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

    /**
     * Helper untuk dapatkan harga paket
     */
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
            case 'family':
                return 800000;
            case 'graduation':
                return 500000;
            default:
                return 0;
        }
    }

    /**
     * Helper untuk dapatkan jumlah maksimal background per paket
     */
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

    /**
     * Normalisasi nomor WhatsApp
     */
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

    /**
     * Normalisasi input selected ids.
     * Bisa menerima array, JSON string, or comma separated string.
     * Mengembalikan array of ints (unique).
     */
    private function normalizeSelectedIds($value): array
    {
        if (is_null($value)) return [];

        if (is_string($value)) {
            $value = trim($value);
            // try json first
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $arr = $decoded;
            } elseif (Str::contains($value, ',')) {
                $arr = array_filter(array_map('trim', explode(',', $value)));
            } else {
                // single value
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

        $arr = array_map(function ($v) {
            return is_numeric($v) ? (int)$v : $v;
        }, $arr);

        // dedupe & ensure ints where possible
        $arr = array_values(array_unique($arr, SORT_REGULAR));

        return $arr;
    }

    /**
     * Ambil extra items dari DB berdasarkan array id.
     * Mengembalikan array objek ['id','name','price'].
     */
    private function fetchExtrasByIds($idsInput = [])
    {
        $ids = $this->normalizeSelectedIds($idsInput);
        if (empty($ids)) return [];

        $items = ExtraItem::whereIn('id', $ids)->get(['id', 'name', 'price']);

        return $items->map(function ($it) {
            return [
                'id' => $it->id,
                'name' => $it->name,
                'price' => (int)$it->price,
            ];
        })->toArray();
    }

    /**
     * Ambil backgrounds dari DB berdasarkan array id.
     * Mengembalikan array objek ['id','name','image'].
     */
    private function fetchBackgroundsByIds($idsInput = [])
    {
        $ids = $this->normalizeSelectedIds($idsInput);
        if (empty($ids)) return [];

        $bgs = Background::whereIn('id', $ids)->get(['id', 'name', 'image']);

        return $bgs->map(function ($bg) {
            return [
                'id'    => $bg->id,
                'name'  => $bg->name,
                'image' => $bg->image,
            ];
        })->toArray();
    }

    /**
     * Simpan file upload ke disk 'public' dan hapus old path jika ada.
     * Mengembalikan path yang disimpan atau null.
     */
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
}
