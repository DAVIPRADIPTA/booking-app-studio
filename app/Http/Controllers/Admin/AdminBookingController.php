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

class AdminBookingController extends Controller
{
    /**
     * ✅ List semua booking (dengan filter & search)
     */
    public function index(Request $request)
    {
        $query = Booking::latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('contact_name', 'like', '%' . $request->q . '%')
                  ->orWhere('package_name', 'like', '%' . $request->q . '%');
            });
        }

        $bookings = $query->paginate(10)->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
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
        $babySmashBackgrounds = $backgroundItems->where('category', 'baby-smash');
        $plainBackgrounds = $backgroundItems->where('category', 'plain');
        $grandeBackgrounds = $backgroundItems->where('category', 'grande');
        $royalBackgrounds = $backgroundItems->where('category', 'royal');
        $prewedBackgrounds = $backgroundItems->where('category', 'pre-wedding');
        $familyBackgrounds = $backgroundItems->where('category', 'family');
        $graduationBackgrounds = $backgroundItems->where('category', 'graduation');

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
            'selectedExtraItems'
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
            return redirect()->route('bookings.show', $id)
                ->with('errorMessage', '❌ Hanya booking dengan status "Sudah Dibooking" yang bisa di-edit.');
        }
        
        $customers = Customer::all();
        
        // Group Backgrounds by Category
        $backgroundItems = Background::where('is_active', true)->get();
        $babySmashBackgrounds = $backgroundItems->where('category', 'baby-smash');
        $plainBackgrounds = $backgroundItems->where('category', 'plain');
        $grandeBackgrounds = $backgroundItems->where('category', 'grande');
        $royalBackgrounds = $backgroundItems->where('category', 'royal');
        $prewedBackgrounds = $backgroundItems->where('category', 'pre-wedding');
        $familyBackgrounds = $backgroundItems->where('category', 'family');
        $graduationBackgrounds = $backgroundItems->where('category', 'graduation');

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
            'selectedExtraItems'
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
            return back()->with('errorMessage', '❌ Hanya booking dengan status "Sudah Dibooking" yang bisa di-edit.');
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
                'selected_backgrounds' => 'required',
                'selected_extra_items' => 'nullable',
                'payment_proof'        => $request->payment_method === 'transfer' ? 'nullable|image|mimes:jpg,jpeg,png|max:2048' : 'nullable',
                'baby_name'            => 'nullable|string|max:255',
                'baby_age'             => 'nullable|string|max:50',
            ]);
        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        }

        // ✅ Cek jadwal bentrok - SAMA DENGAN BookingController
        $exists = Booking::where('booking_date', $request->booking_date)
            ->where('booking_time', $request->booking_time)
            ->where('id', '!=', $id) // Kecualikan booking yang sedang diupdate
            ->whereIn('status', ['waiting_payment', 'pending_verification', 'booked'])
            ->exists();

        if ($exists) {
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

            return redirect()->route('bookings.show', $id)
                ->with('successMessage', '✅ Booking berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Admin update booking gagal: ' . $e->getMessage(), [
                'payload' => $request->all(), 
                'booking_id' => $id,
                'exception' => $e
            ]);
            
            return back()->withInput()->with('errorMessage', 'Terjadi kesalahan saat memperbarui booking.');
        }
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
                'selected_backgrounds' => 'required',
                'selected_extra_items' => 'nullable',
                'payment_proof'        => $request->payment_method === 'transfer' ? 'required|image|mimes:jpg,jpeg,png|max:2048' : 'nullable',
                'baby_name'            => 'nullable|string|max:255',
                'baby_age'             => 'nullable|string|max:50',
            ]);
        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        }

        // ✅ Cek jadwal bentrok
        $exists = Booking::where('booking_date', $request->booking_date)
            ->where('booking_time', $request->booking_time)
            ->whereIn('status', ['waiting_payment', 'pending_verification', 'booked'])
            ->exists();

        if ($exists) {
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

            return redirect()->route('bookings.index')->with('successMessage', '✅ Booking berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Admin booking gagal: ' . $e->getMessage(), ['payload' => $request->all(), 'exception' => $e]);
            return back()->withInput()->with('errorMessage', 'Terjadi kesalahan saat menyimpan booking.');
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