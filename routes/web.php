<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\TermsAndConditionController;
use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\Admin\BackgroundController;
use App\Http\Controllers\Admin\ExtraItemsController;
use App\Http\Controllers\BookingAvailabilityController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\CustomerPasswordController;
use App\Http\Controllers\CustomerController; // ✅ ditambah, biar rapih
use App\Models\Background;
use App\Models\ExtraItem;
use App\Models\TermsAndCondition;
use Livewire\Volt\Volt;

// ========================
// LANDING PAGE & INFO (tanpa login)
// ========================

Route::get('/', fn() => view('welcome'))->name('home');

Route::get('/customer/terms', function () {
    $terms = TermsAndCondition::all();
    return view('web.customer.terms', compact('terms'));
})->name('customer.terms');

Route::get('/customer/privacy', fn() => view('web.customer.privacy'))->name('customer.privacy');

// ========================
// CUSTOMER AUTH (login/register/forgot password)
// ========================

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
// HALAMAN CUSTOMER (HARUS LOGIN)
// ========================

Route::middleware(['customer.auth'])->group(function () {
    // Booking
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/booking/{booking}/payment', [BookingController::class, 'payment'])
        ->where('booking', '[0-9]+')
        ->name('booking.payment');
    Route::post('/booking/{booking}/upload-proof', [BookingController::class, 'uploadProof'])
        ->where('booking', '[0-9]+')
        ->name('booking.uploadProof');
    Route::get('/booking/{booking}/check-status', [BookingController::class, 'checkStatus'])
        ->where('booking', '[0-9]+')
        ->name('booking.checkStatus');

    // Pages
    Route::get('/homepage', fn() => view('homepage'))->name('homepage');
    Route::get('/info-more', fn() => view('info'))->name('info');

    // Riwayat booking
    Route::get('/riwayat-pemesanan', [CustomerController::class, 'bookings'])->name('customer.bookings');

    // Pengaturan Akun
    Route::get('/customer/profile', [CustomerAuthController::class, 'profile'])->name('customer.profile');
    Route::put('/customer/profile/update', [CustomerAuthController::class, 'updateProfile'])->name('customer.profile.update');
    Route::get('/customer/password/edit', [CustomerPasswordController::class, 'editPassword'])->name('customer.password.edit');
    Route::put('/customer/password/update', [CustomerPasswordController::class, 'updatePassword'])->name('customer.password.update');

    // ========================
    // KATEGORI (Paket Booking)
    // ========================
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
// ADMIN AREA (HARUS LOGIN BREEZE)
// ========================

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    
    // Manajemen booking (admin)
    Route::resource('bookings', AdminBookingController::class)->except(['edit']);
    Route::post('/bookings/{id}/verify-payment', [AdminBookingController::class, 'verifyPayment'])->name('bookings.verifyPayment');
    Route::post('/bookings/{id}/cancel-booking', [AdminBookingController::class, 'cancelBooking'])->name('bookings.cancelBooking');
    Route::post('/bookings/{id}/force-cancel', [AdminBookingController::class, 'forceCancel'])->name('bookings.forceCancel');
    Route::post('/bookings/{id}/complete-booking', [AdminBookingController::class, 'completeBooking'])->name('bookings.completeBooking');
    Route::post('/bookings/{id}/process-cancellation', [AdminBookingController::class, 'processCancellation'])->name('bookings.processCancellation');

    // Resource lainnya
    Route::resource('backgrounds', BackgroundController::class);
    Route::resource('extra-items', ExtraItemsController::class);
    Route::resource('terms', TermsAndConditionController::class);
});

// ========================
// SETTINGS (ADMIN) via Volt
// ========================

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

// ========================
// API
// ========================

Route::get('/api/available-times', [BookingAvailabilityController::class, 'getAvailableTimes'])->name('api.available.times');

// ========================
// AUTH ROUTES (ADMIN BREEZE)
// ========================

require __DIR__ . '/auth.php';
