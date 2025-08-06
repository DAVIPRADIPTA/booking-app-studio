<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\TermsAndConditionController;
use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\Admin\BackgroundController;
use App\Http\Controllers\Admin\ExtraItemsController;
use App\Models\Background;
use App\Models\ExtraItem;
use App\Models\TermsAndCondition;
use Livewire\Volt\Volt;

// Homepage
Route::get('/', fn() => view('welcome'))->name('home');
Route::get('/homepage', fn() => view('homepage'))->name('homepage');

// Kategori: Prewedding
Route::get('/kategori/prewed', function () {
    return view('kategori.prewed', [
        'printItems' => ExtraItem::where('category', 'cetak-foto')->where('is_active', true)->get(),
        'frameItems' => ExtraItem::where('category', 'frame-foto')->where('is_active', true)->get(),
        'serviceItems' => ExtraItem::where('category', 'tambahan-layanan')->where('is_active', true)->get(),
        'backgroundItems' => Background::where('category', 'pre-wedding')->where('is_active', true)->get(),
        'terms' => TermsAndCondition::all()
    ]);
})->name('kategori.prewed');

// Kategori: Group Session
Route::get('/kategori/group', function () {
    return view('kategori.group', [
        'printItems' => ExtraItem::where('category', 'cetak-foto')->where('is_active', true)->get(),
        'frameItems' => ExtraItem::where('category', 'frame-foto')->where('is_active', true)->get(),
        'serviceItems' => ExtraItem::where('category', 'tambahan-layanan')->where('is_active', true)->get(),
        'terms' => TermsAndCondition::all()
    ]);
})->name('kategori.group');

// Kategori: Baby Smash Cake
Route::get('/kategori/baby', function () {
    return view('kategori.baby', [
        'printItems' => ExtraItem::where('category', 'cetak-foto')->where('is_active', true)->get(),
        'frameItems' => ExtraItem::where('category', 'frame-foto')->where('is_active', true)->get(),
        'serviceItems' => ExtraItem::where('category', 'tambahan-layanan')->where('is_active', true)->get(),
        'terms' => TermsAndCondition::all()
    ]);
})->name('kategori.baby');

// Info & Sosial Media
Route::get('/info-more', fn() => view('info'))->name('info');

// Booking Store
Route::post('/booking', [BookingController::class, 'store']);

// Authenticated Admin Area
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

// Auth Routes (Login, Logout, Register) — note: register dapat dimatikan di file auth.php
require __DIR__ . '/auth.php';
