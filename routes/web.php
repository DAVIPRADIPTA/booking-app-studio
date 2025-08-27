<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\TermsAndConditionController;
use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\Admin\BackgroundController;
use App\Http\Controllers\Admin\ExtraItemsController;
use App\Http\Controllers\BookingAvailabilityController;
use App\Models\Background;
use App\Models\ExtraItem;
use App\Models\TermsAndCondition;
use Livewire\Volt\Volt;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\CustomerPasswordController;

// ========================
// RUTE UNTUK CUSTOMER
// ========================

// Welcome (tanpa login)
Route::get('/', fn() => view('welcome'))->name('home');

// Halaman Syarat & Ketentuan Customer (TERMS)
Route::get('/customer/terms', function () {
    $terms = TermsAndCondition::all();
    return view('web.customer.terms', compact('terms'));
})->name('customer.terms');

// Halaman Privacy Policy Customer (PRIVACY)
Route::get('/customer/privacy', function () {
    return view('web.customer.privacy');
})->name('customer.privacy');

// Customer Auth
Route::prefix('customer')->group(function () {
    Route::controller(CustomerAuthController::class)->group(function () {
        Route::get('login', 'login')->name('customer.login');
        Route::post('login', 'store_login')->name('customer.store_login');
        Route::get('register', 'register')->name('customer.register');
        Route::post('register', 'store_register')->name('customer.store_register');
        Route::post('logout', 'logout')->name('customer.logout');
    });

    // Forgot / Reset password untuk customer
    Route::get('forgot-password', [CustomerPasswordController::class, 'showForgotForm'])
         ->name('customer.forgot-password');

    Route::post('forgot-password', [CustomerPasswordController::class, 'sendResetLink'])
         ->name('customer.forgot-password.send');

    Route::get('reset-password/{token}', [CustomerPasswordController::class, 'showResetForm'])
         ->name('customer.reset-password');

    Route::post('reset-password', [CustomerPasswordController::class, 'reset'])
         ->name('customer.reset-password.update');
});

// ========================
// SEMUA HALAMAN CUSTOMER HARUS LOGIN DULU
// ========================

Route::middleware(['customer.auth'])->group(function () {
    Route::get('/homepage', fn() => view('homepage'))->name('homepage');
    Route::get('/info-more', fn() => view('info'))->name('info');
    Route::post('/booking', [BookingController::class, 'store']);
    
    // Rute untuk Pengaturan Akun (sudah login)
    Route::get('/customer/profile', [CustomerAuthController::class, 'profile'])->name('customer.profile');
    Route::put('/customer/profile/update', [CustomerAuthController::class, 'updateProfile'])->name('customer.profile.update');
    
    // Rute Ubah Password
    Route::get('/customer/password/edit', [CustomerPasswordController::class, 'editPassword'])->name('customer.password.edit');
    Route::put('/customer/password/update', [CustomerPasswordController::class, 'updatePassword'])->name('customer.password.update');
    
    Route::get('/kategori/prewed', function () {
        return view('kategori.prewed', [
            'printItems' => ExtraItem::where('category', 'cetak-foto')->where('is_active', true)->get(),
            'frameItems' => ExtraItem::where('category', 'frame-foto')->where('is_active', true)->get(),
            'serviceItems' => ExtraItem::where('category', 'tambahan-layanan')->where('is_active', true)->get(),
            'backgroundItems' => Background::where('category', 'pre-wedding')->where('is_active', true)->get(),
            'terms' => TermsAndCondition::all()
        ]);
    })->name('kategori.prewed');

    Route::get('/kategori/group', function () {
        return view('kategori.group', [
            'printItems' => ExtraItem::where('category', 'cetak-foto')->where('is_active', true)->get(),
            'frameItems' => ExtraItem::where('category', 'frame-foto')->where('is_active', true)->get(),
            'serviceItems' => ExtraItem::where('category', 'tambahan-layanan')->where('is_active', true)->get(),
            'terms' => TermsAndCondition::all()
        ]);
    })->name('kategori.group');

    Route::get('/kategori/baby', function () {
        return view('kategori.baby', [
            'printItems' => ExtraItem::where('category', 'cetak-foto')->where('is_active', true)->get(),
            'frameItems' => ExtraItem::where('category', 'frame-foto')->where('is_active', true)->get(),
            'serviceItems' => ExtraItem::where('category', 'tambahan-layanan')->where('is_active', true)->get(),
            'terms' => TermsAndCondition::all()
        ]);
    })->name('kategori.baby');
});

// ========================
// RUTE ADMIN (LOGIN BREEZE) - TIDAK TERSENTUH
// ========================

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::resource('backgrounds', BackgroundController::class);
    Route::resource('bookings', AdminBookingController::class)->except(['create', 'store', 'edit']);
    Route::post('/bookings/{id}/confirm-dp', [AdminBookingController::class, 'confirmDp'])->name('bookings.confirmDp');
    Route::post('/bookings/{id}/complete-booking', [AdminBookingController::class, 'completeBooking'])->name('bookings.completeBooking');
    Route::resource('extra-items', ExtraItemsController::class);
    Route::resource('terms', TermsAndConditionController::class);
});

// Settings via Volt
Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

// API
Route::get('/api/available-times', [BookingAvailabilityController::class, 'getAvailableTimes'])->name('api.available.times');

// Auth Routes (Admin Breeze)
require __DIR__ . '/auth.php';
