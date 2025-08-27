<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password;

class CustomerAuthController extends Controller
{
    /**
     * Tampilkan form login
     */
    public function login()
    {
        // Jika sudah login, langsung ke homepage
        if (Auth::guard('customer')->check()) {
            return redirect()->route('homepage');
        }

        // Simpan URL sebelumnya ke sesi.
        // Ini memastikan tombol 'Kembali' akan berfungsi dengan benar
        // bahkan setelah form login disubmit (metode POST).
        $previousUrl = url()->previous();
        // Cek apakah URL sebelumnya bukan halaman login atau store_login itu sendiri.
        if ($previousUrl !== route('customer.login') && $previousUrl !== route('customer.store_login')) {
            session()->put('previous_url', $previousUrl);
        }

        return view('web.customer.login', [
            'title' => 'Masuk ke Akun Anda'
        ]);
    }

    /**
     * Tampilkan form register
     */
    public function register()
    {
        // Jika sudah login, langsung ke homepage
        if (Auth::guard('customer')->check()) {
            return redirect()->route('homepage');
        }

        return view('web.customer.register', [
            'title' => 'Daftar Akun Baru'
        ]);
    }

    /**
     * Proses registrasi customer baru
     */
    public function store_register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'whatsapp_number' => 'nullable|string|max:20',
            'password' => 'required|min:8|confirmed',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // Buat customer baru
        $customer = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'whatsapp_number' => $request->whatsapp_number,
            'password' => Hash::make($request->password),
        ]);

        // Login otomatis setelah registrasi
        Auth::guard('customer')->login($customer);

        // Arahkan kembali ke halaman sebelumnya atau ke homepage sebagai fallback
        $previousUrl = session()->get('previous_url', route('homepage'));
        session()->forget('previous_url');

        return redirect($previousUrl)->with('successMessage', 'Registrasi berhasil!');
    }

    /**
     * Proses login customer
     */
    public function store_login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Proteksi brute force: maksimal 5 kali percobaan per 1 menit (berdasarkan IP)
        $key = 'login.' . $request->ip();

        if (!RateLimiter::attempt($key, 5, fn() => 60)) {
            throw ValidationException::withMessages([
                'email' => ['Terlalu banyak percobaan login. Coba lagi dalam 1 menit.']
            ]);
        }

        $customer = Customer::where('email', $request->email)->first();

        if ($customer && Hash::check($request->password, $customer->password)) {
            // Reset throttle jika login berhasil
            RateLimiter::clear($key);

            Auth::guard('customer')->login($customer);

            // Ambil URL sebelumnya dari sesi atau gunakan homepage sebagai fallback
            $previousUrl = session()->get('previous_url', route('homepage'));

            // Hapus URL dari sesi setelah digunakan
            session()->forget('previous_url');

            return redirect($previousUrl)->with('successMessage', 'Login berhasil!');
        }

        // Tambahkan ke throttle jika login gagal
        RateLimiter::hit($key);

        return back()->with('errorMessage', 'Email atau password salah.');
    }

    /**
     * Tampilkan halaman profil customer.
     */
    public function profile()
    {
        // Ambil data customer yang sedang login
        $customer = Auth::guard('customer')->user();
        
        return view('web.customer.profile', [
            'title' => 'Pengaturan Akun',
            'customer' => $customer,
        ]);
    }
    
    /**
     * Proses pembaruan profil dan password customer.
     */
    public function updateProfile(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        // Aturan validasi
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            // Pastikan email unik, kecuali untuk email customer itu sendiri
            'email' => ['required', 'string', 'email', 'max:255', 'unique:customers,email,' . $customer->id],
            'whatsapp_number' => ['nullable', 'string', 'max:20'],
        ];

        // Tambahkan validasi password hanya jika pengguna ingin memperbarui password
        if ($request->filled('current_password') || $request->filled('password') || $request->filled('password_confirmation')) {
            $rules['current_password'] = ['required', function ($attribute, $value, $fail) use ($customer) {
                if (!Hash::check($value, $customer->password)) {
                    $fail('Password saat ini tidak cocok dengan catatan kami.');
                }
            }];
            $rules['password'] = ['required', 'confirmed', Password::min(8)];
        }

        $request->validate($rules);
        
        // Perbarui data profil
        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->whatsapp_number = $request->whatsapp_number;
        
        // Perbarui password jika ada dan valid
        if ($request->filled('password')) {
            $customer->password = Hash::make($request->password);
        }

        $customer->save();

        // Redirect kembali ke halaman profil dengan pesan sukses
        return back()->with('status', 'profile-updated');
    }

    /**
     * Proses logout customer
     */
    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/customer/login')->with('successMessage', 'Anda telah berhasil logout.');
    }
}