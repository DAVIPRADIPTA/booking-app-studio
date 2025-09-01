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

class AdminBookingController extends Controller
{
    /**
     * ✅ List semua booking (dengan filter & search)
     */
    /**
     * List semua booking (dengan filter & search)
     */
    public function index(Request $request)
    {
        // Basic query with optional relations
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

        // sorting (default newest booking_date desc then id)
        $sortBy = $request->get('sort_by', 'booking_date_desc');
        switch ($sortBy) {
            case 'booking_date_asc':
                $query->orderBy('booking_date', 'asc')->orderBy('booking_time', 'asc');
                break;
            case 'created_at_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'created_at_desc':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                // default booking_date desc
                $query->orderBy('booking_date', 'desc')->orderBy('booking_time', 'desc');
                break;
        }

        // per-page
        $perPage = (int) $request->get('per_page', 10);
        if ($perPage <= 0) $perPage = 10;

        // stats: counts by status (for quick badges in UI)
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
     * ✅ Tampilkan detail booking
     */
    public function show($id)
    {
        $booking = Booking::findOrFail($id);
        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * ✅ Form tambah booking manual
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

        // Pastikan selected_backgrounds selalu berupa array
        $selectedBackgrounds = old('selected_backgrounds', []);
        if (is_string($selectedBackgrounds)) {
            try {
                $selectedBackgrounds = json_decode($selectedBackgrounds, true);
                if (!is_array($selectedBackgrounds)) {
                    $selectedBackgrounds = [];
                }
            } catch (\Exception $e) {
                $selectedBackgrounds = [];
            }
        }

        // Pastikan selected_extra_items selalu berupa array
        $selectedExtraItems = old('selected_extra_items', []);
        if (is_string($selectedExtraItems)) {
            try {
                $selectedExtraItems = json_decode($selectedExtraItems, true);
                if (!is_array($selectedExtraItems)) {
                    $selectedExtraItems = [];
                }
            } catch (\Exception $e) {
                $selectedExtraItems = [];
            }
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
     * ✅ Form edit booking - HANYA UNTUK STATUS 'booked'
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

        // Pastikan selected_backgrounds selalu berupa array
        $selectedBackgrounds = $booking->selected_backgrounds ?? [];
        if (!is_array($selectedBackgrounds)) {
            $selectedBackgrounds = [];
        }

        // Pastikan selected_extra_items selalu berupa array
        $selectedExtraItems = [];
        if (!empty($booking->selected_extra_items)) {
            $selectedExtraItems = collect($booking->selected_extra_items)->pluck('id')->toArray();
        }

        // Available times based on booking date (so admin form can show correct slots)
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
     * ✅ Update booking - HANYA UNTUK STATUS 'booked'
     */
    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        // Hanya izinkan update untuk booking dengan status 'booked'
        if ($booking->status !== 'booked') {
            return redirect()->route('bookings.index')
                ->with('errorMessage', '❌ Hanya booking dengan status "Sudah Dibooking" yang bisa di-edit.');
        }

        // Normalisasi nomor WhatsApp
        $normalizedPhone = $this->normalizePhone($request->whatsapp_number);
        $request->merge(['whatsapp_number' => $normalizedPhone]);

        // Validasi dasar dengan penambahan validasi tanggal
        try {
            $request->validate([
                'customer_id'          => 'required|exists:customers,id',
                'contact_name'         => 'required|string|max:100',
                'whatsapp_number'      => 'required|string|max:20',
                'booking_date'         => 'required|date|after_or_equal:today',
                'booking_time'         => 'required|string',
                'session_name'         => 'required|string|max:100',
                'package_name'         => 'required|string|max:100',
                'payment_method'       => 'required|in:cash,transfer',
                'selected_backgrounds' => strtolower($request->package_name) === 'baby smash cake' || strtolower($request->package_name) === 'babysmash' ? 'nullable' : 'required',
                'selected_extra_items' => 'nullable',
                'payment_proof'        => $request->payment_method === 'transfer' ? 'nullable|image|mimes:jpg,jpeg,png|max:2048' : 'nullable',
                'baby_name'            => 'nullable|string|max:255',
                'baby_age'             => 'nullable|string|max:50',
            ]);
        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        }

        // Validasi booking_time harus termasuk daftar jam yang valid
        $allTimes = Booking::getAllTimes();
        if (!in_array($request->booking_time, $allTimes)) {
            return back()->withInput()->with('errorMessage', 'Waktu booking tidak valid.')->withInput();
        }

        // ✅ Cek jadwal bentrok - gunakan helper model (kecualikan booking yang sedang diupdate)
        if (!Booking::isSlotAvailable($request->booking_date, $request->booking_time, $id)) {
            return back()->with('errorMessage', '❌ Slot sudah terisi, silakan pilih waktu lain.')
                ->withInput();
        }

        // ✅ Hitung ulang total harga di server - SAMA DENGAN FRONTEND
        $packagePrice = $this->getPackagePrice($request->package_name);

        // Ambil harga dari database untuk extra items, jangan dari request
        $extraItemsPrice = 0;
        $extras = [];

        if ($request->filled('selected_extra_items')) {
            // Pastikan selected_extra_items adalah array
            $selectedExtraItems = is_string($request->selected_extra_items) ?
                json_decode($request->selected_extra_items, true) :
                $request->selected_extra_items;

            if (!is_array($selectedExtraItems)) {
                $selectedExtraItems = [];
            }

            $extras = ExtraItem::whereIn('id', $selectedExtraItems)
                ->get(['id', 'name', 'price'])
                ->map(function ($item) use (&$extraItemsPrice) {
                    $extraItemsPrice += (int) $item->price;
                    return [
                        'id'    => $item->id,
                        'name'  => $item->name,
                        'price' => (int) $item->price,
                    ];
                })->values()->toArray();
        }

        $totalPrice = $packagePrice + $extraItemsPrice;

        // ✅ Upload bukti transfer jika ada
        $paymentProofPath = $booking->payment_proof; // Pertahankan bukti lama jika tidak ada yang baru
        if ($request->hasFile('payment_proof')) {
            // Hapus bukti lama jika ada
            if ($booking->payment_proof) {
                Storage::disk('public')->delete($booking->payment_proof);
            }
            $paymentProofPath = $request->file('payment_proof')->store('payment_proofs', 'public');
        }

        // ✅ Background terpilih - SAMA DENGAN BookingController
        $backgrounds = [];
        $maxBackgrounds = $this->getMaxBackgrounds($request->package_name);

        // Jika bukan Baby Smash Cake, kita harus memilih background
        if (strtolower($request->package_name) !== 'baby smash cake' && strtolower($request->package_name) !== 'babysmash') {
            if ($request->filled('selected_backgrounds')) {
                // Pastikan selected_backgrounds adalah array
                $selectedBackgrounds = is_string($request->selected_backgrounds) ?
                    json_decode($request->selected_backgrounds, true) :
                    $request->selected_backgrounds;

                if (!is_array($selectedBackgrounds)) {
                    $selectedBackgrounds = [];
                }

                // Validasi jumlah background sesuai paket
                if (count($selectedBackgrounds) < 1) {
                    return back()->with('errorMessage', "❌ Paket {$request->package_name} harus memilih minimal 1 background.")
                        ->withInput();
                }

                if (count($selectedBackgrounds) > $maxBackgrounds) {
                    return back()->with('errorMessage', "❌ Paket {$request->package_name} hanya membolehkan maksimal {$maxBackgrounds} background.")
                        ->withInput();
                }

                $backgrounds = Background::whereIn('id', $selectedBackgrounds)
                    ->get(['id', 'name', 'image'])
                    ->map(fn($bg) => [
                        'id'    => $bg->id,
                        'name'  => $bg->name,
                        'image' => $bg->image,
                    ])->values()->toArray();
            } else {
                return back()->with('errorMessage', "❌ Paket {$request->package_name} harus memilih minimal 1 background.")
                    ->withInput();
            }
        }

        // ✅ Update booking - HANYA DATA PEMESANAN, BUKAN STATUS
        try {
            $booking->update([
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
                'payment_method'       => $request->payment_method,
                'payment_proof'        => $paymentProofPath,
                'notes'                => $request->notes,
                'baby_name'            => $request->baby_name,
                'baby_age'             => $request->baby_age,
                // Tidak mengupdate status karena harus tetap 'booked'
            ]);

            // Ambil data terbaru setelah update
            $booking->refresh();

            return redirect()->route('bookings.index')
                ->with('successMessage', '✅ Booking #' . $booking->id . ' berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Admin update booking gagal: ' . $e->getMessage(), [
                'payload' => $request->all(),
                'booking_id' => $id,
                'exception' => $e
            ]);

            return back()->withInput()->with('errorMessage', 'Terjadi kesalahan saat memperbarui booking. Silakan coba lagi.');
        }
    }

    /**
     * ✅ Batalkan booking (admin)
     *
     * Sekarang: bisa dibatalkan di berbagai status kecuali 'completed' atau 'cancelled'.
     * Jika frontend tidak mengirimkan `cancellation_reason`, kita pakai fallback message.
     */
    public function cancelBooking(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        // Tidak boleh batalkan jika sudah selesai atau sudah dibatalkan
        if (in_array($booking->status, ['completed', 'cancelled'])) {
            return back()->with('errorMessage', '❌ Booking tidak dapat dibatalkan pada status saat ini.');
        }

        // Terima alasan pembatalan jika ada (opsional)
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
     * ✅ Paksa batalkan booking (admin override)
     *
     * Sama seperti cancelBooking tetapi selalu mengizinkan (kecuali sudah 'completed'/'cancelled').
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
     * ✅ Verifikasi pembayaran
     */
    public function verifyPayment(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        // Hanya izinkan verifikasi untuk booking dengan status 'pending_verification'
        if ($booking->status !== 'pending_verification') {
            return back()->with('errorMessage', '❌ Booking tidak dalam status menunggu verifikasi.');
        }

        // Update status menjadi 'booked'
        $booking->update([
            'status' => 'booked',
        ]);

        return back()->with('successMessage', '✅ Booking #' . $booking->id . ' berhasil diverifikasi dan dikonfirmasi.');
    }

    /**
     * ✅ Tandai booking selesai
     */
    public function completeBooking(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        // Hanya izinkan menandai selesai untuk booking dengan status 'booked'
        if ($booking->status !== 'booked') {
            return back()->with('errorMessage', '❌ Booking tidak dalam status bisa ditandai selesai.');
        }

        // Update status menjadi 'completed'
        $booking->update([
            'status' => 'completed',
        ]);

        return back()->with('successMessage', '✅ Booking #' . $booking->id . ' berhasil ditandai selesai.');
    }

    /**
     * ✅ Proses pembatalan + refund
     */
    public function processCancellation(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $request->validate([
            'refund_amount' => 'required|numeric|min:0',
            'refund_proof' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'cancellation_reason' => 'required|string|max:255',
        ]);

        $refundPath = null;
        if ($request->hasFile('refund_proof')) {
            $refundPath = $request->file('refund_proof')->store('refund_proofs', 'public');
        }

        $booking->update([
            'status' => 'cancelled',
            'refund_amount' => $request->refund_amount,
            'refund_proof' => $refundPath,
            'auto_cancelled_at' => now(),
            'cancellation_reason' => $request->cancellation_reason,
        ]);

        return back()->with('successMessage', '✅ Booking #' . $booking->id . ' berhasil dibatalkan dengan refund.');
    }

    /**
     * ✅ Simpan booking manual dengan validasi & hitung ulang harga
     */
    public function store(Request $request)
    {
        // Normalisasi nomor WhatsApp
        $normalizedPhone = $this->normalizePhone($request->whatsapp_number);
        $request->merge(['whatsapp_number' => $normalizedPhone]);

        // Validasi dasar dengan penambahan validasi tanggal
        try {
            $request->validate([
                'customer_id'          => 'required|exists:customers,id',
                'contact_name'         => 'required|string|max:100',
                'whatsapp_number'      => 'required|string|max:20',
                'booking_date'         => 'required|date|after_or_equal:today',
                'booking_time'         => 'required|string',
                'session_name'         => 'required|string|max:100',
                'package_name'         => 'required|string|max:100',
                'payment_method'       => 'required|in:cash,transfer',
                'selected_backgrounds' => strtolower($request->package_name) === 'baby smash cake' || strtolower($request->package_name) === 'babysmash' ? 'nullable' : 'required',
                'selected_extra_items' => 'nullable',
                'payment_proof'        => $request->payment_method === 'transfer' ? 'required|image|mimes:jpg,jpeg,png|max:2048' : 'nullable',
                'baby_name'            => 'nullable|string|max:255',
                'baby_age'             => 'nullable|string|max:50',
            ]);
        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        }

        // Validasi booking_time harus termasuk daftar jam yang valid
        $allTimes = Booking::getAllTimes();
        if (!in_array($request->booking_time, $allTimes)) {
            return back()->withInput()->with('errorMessage', 'Waktu booking tidak valid.');
        }

        // ✅ Cek jadwal bentrok (gunakan helper model)
        if (!Booking::isSlotAvailable($request->booking_date, $request->booking_time)) {
            return back()->with('errorMessage', '❌ Slot sudah terisi, silakan pilih waktu lain.')
                ->withInput();
        }

        // ✅ Hitung ulang total harga di server
        $packagePrice = $this->getPackagePrice($request->package_name);

        // Ambil harga dari database untuk extra items
        $extraItemsPrice = 0;
        $extras = [];

        if ($request->filled('selected_extra_items')) {
            // Pastikan selected_extra_items adalah array
            $selectedExtraItems = is_string($request->selected_extra_items) ?
                json_decode($request->selected_extra_items, true) :
                $request->selected_extra_items;

            if (!is_array($selectedExtraItems)) {
                $selectedExtraItems = [];
            }

            $extras = ExtraItem::whereIn('id', $selectedExtraItems)
                ->get(['id', 'name', 'price'])
                ->map(function ($item) use (&$extraItemsPrice) {
                    $extraItemsPrice += (int) $item->price;
                    return [
                        'id'    => $item->id,
                        'name'  => $item->name,
                        'price' => (int) $item->price,
                    ];
                })->values()->toArray();
        }

        $totalPrice = $packagePrice + $extraItemsPrice;

        // ✅ Upload bukti transfer jika ada
        $paymentProofPath = null;
        if ($request->payment_method === 'transfer' && $request->hasFile('payment_proof')) {
            $paymentProofPath = $request->file('payment_proof')->store('payment_proofs', 'public');
        }

        // ✅ Background terpilih
        $backgrounds = [];
        $maxBackgrounds = $this->getMaxBackgrounds($request->package_name);

        // Jika bukan Baby Smash Cake, kita harus memilih background
        if (strtolower($request->package_name) !== 'baby smash cake' && strtolower($request->package_name) !== 'babysmash') {
            if ($request->filled('selected_backgrounds')) {
                // Pastikan selected_backgrounds adalah array
                $selectedBackgrounds = is_string($request->selected_backgrounds) ?
                    json_decode($request->selected_backgrounds, true) :
                    $request->selected_backgrounds;

                if (!is_array($selectedBackgrounds)) {
                    $selectedBackgrounds = [];
                }

                // Validasi jumlah background sesuai paket
                if (count($selectedBackgrounds) < 1) {
                    return back()->with('errorMessage', "❌ Paket {$request->package_name} harus memilih minimal 1 background.")
                        ->withInput();
                }

                if (count($selectedBackgrounds) > $maxBackgrounds) {
                    return back()->with('errorMessage', "❌ Paket {$request->package_name} hanya membolehkan maksimal {$maxBackgrounds} background.")
                        ->withInput();
                }

                $backgrounds = Background::whereIn('id', $selectedBackgrounds)
                    ->get(['id', 'name', 'image'])
                    ->map(fn($bg) => [
                        'id'    => $bg->id,
                        'name'  => $bg->name,
                        'image' => $bg->image,
                    ])->values()->toArray();
            } else {
                return back()->with('errorMessage', "❌ Paket {$request->package_name} harus memilih minimal 1 background.")
                    ->withInput();
            }
        }

        // ✅ Tentukan status otomatis - SEMUA LANGSUNG BOOKED KARENA ADMIN OFFLINE
        $status = 'booked'; // Semua langsung booked karena sudah dibayar di tempat
        $paymentDeadline = null;

        // ✅ Simpan booking
        try {
            $booking = Booking::create([
                'user_id'              => auth('web')->id(), // Admin yang membuat booking
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
                'status'               => $status,
                'payment_method'       => $request->payment_method,
                'payment_proof'        => $paymentProofPath,
                'payment_deadline'     => $paymentDeadline,
                'notes'                => $request->notes,
                'baby_name'            => $request->baby_name,
                'baby_age'             => $request->baby_age,
            ]);

            return redirect()->route('bookings.index')->with('successMessage', '✅ Booking #' . $booking->id . ' berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Admin booking gagal: ' . $e->getMessage(), ['payload' => $request->all(), 'exception' => $e]);
            return back()->withInput()->with('errorMessage', 'Terjadi kesalahan saat menyimpan booking.');
        }
    }

    /**
     * ✅ HAPUS booking - hanya untuk status 'completed' atau 'cancelled'
     *
     * Menghapus file bukti pembayaran/refund jika ada, lalu hapus record.
     */
    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);

        // Hanya izinkan hapus untuk booking yang sudah selesai atau sudah dibatalkan
        if (!in_array($booking->status, ['completed', 'cancelled'])) {
            return back()->with('errorMessage', '❌ Hanya pesanan dengan status "Selesai" atau "Dibatalkan" yang boleh dihapus.');
        }

        try {
            DB::transaction(function () use ($booking) {
                // Hapus file payment_proof jika ada
                if (!empty($booking->payment_proof) && Storage::disk('public')->exists($booking->payment_proof)) {
                    Storage::disk('public')->delete($booking->payment_proof);
                }

                // Hapus file refund_proof jika ada
                if (!empty($booking->refund_proof) && Storage::disk('public')->exists($booking->refund_proof)) {
                    Storage::disk('public')->delete($booking->refund_proof);
                }

                // Hapus record booking (hard delete). Jika ingin soft delete, gunakan $booking->delete() dengan SoftDeletes di model.
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
     * ✅ Helper untuk dapatkan harga paket
     */
    private function getPackagePrice($packageName)
    {
        // Pastikan nama paket sesuai dengan yang di frontend
        switch (strtolower($packageName)) {
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
     * ✅ Helper untuk dapatkan jumlah maksimal background per paket
     */
    private function getMaxBackgrounds($packageName)
    {
        switch (strtolower($packageName)) {
            case 'baby smash cake':
            case 'babysmash':
                return 0; // Tidak boleh pilih background
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
     * ✅ Helper untuk normalisasi nomor WhatsApp
     */
    private function normalizePhone($raw)
    {
        $s = (string)$raw;
        $s = preg_replace('/[^\d+]/', '', $s);

        if (strpos($s, '+') === 0) {
            return $s;
        }

        if (strpos($s, '62') === 0) {
            return '+' . $s;
        }

        if (strpos($s, '0') === 0) {
            return '+62' . substr($s, 1);
        }

        return '+62' . $s;
    }
}
