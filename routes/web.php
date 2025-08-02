<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\TermsAndConditionController;
use App\Http\Controllers\Admin\AdminBookingController; 


use App\Http\Controllers\Admin\BackgroundController;
use App\Http\Controllers\Admin\ExtraItemsController;
use App\Models\Background;
use Illuminate\Support\Facades\Route;
use App\Models\ExtraItem;
use App\Models\TermsAndCondition;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');
// 📷 Homepage
Route::get('/homepage', function () {
    return view('homepage');
})->name('homepage');

// 📸 Kategori: Prewedding
Route::get('/kategori/prewed', function () {
    // Ambil semua item dari kategori 'cetak-foto'
    $printItems = ExtraItem::where('category', 'cetak-foto')->where('is_active', true)->get();

    // Ambil semua item dari kategori 'frame-foto'
    $frameItems = ExtraItem::where('category', 'frame-foto')->where('is_active', true)->get();

    // Ambil semua item dari kategori 'tambahan' (saya asumsikan ini yang Anda maksud dengan 'layanan tambahan')
    $serviceItems = ExtraItem::where('category', 'tambahan-layanan')->where('is_active', true)->get();

    $backgroundItems = Background::where('category', 'pre-wedding')->where('is_active', true)->get();

    
    $termsAndCondition = TermsAndCondition::all();

    return view('kategori.prewed', [
        'printItems' => $printItems,
        'frameItems' => $frameItems,
        'serviceItems' => $serviceItems,
        'backgroundItems' => $backgroundItems,
        'terms' => $termsAndCondition
    ]);
})->name('kategori.prewed');

// 👥 Kategori: Group Session
Route::get('/kategori/group', function () {
    // Ambil semua item dari kategori 'cetak-foto'
    $printItems = ExtraItem::where('category', 'cetak-foto')->where('is_active', true)->get();

    // Ambil semua item dari kategori 'frame-foto'
    $frameItems = ExtraItem::where('category', 'frame-foto')->where('is_active', true)->get();

    // Ambil semua item dari kategori 'tambahan' (saya asumsikan ini yang Anda maksud dengan 'layanan tambahan')
    $serviceItems = ExtraItem::where('category', 'tambahan-layanan')->where('is_active', true)->get();

    $termsAndCondition = TermsAndCondition::all();
    return view('kategori.group', [
        'printItems' => $printItems,
        'frameItems' => $frameItems,
        'serviceItems' => $serviceItems,
        'terms' => $termsAndCondition
    ]);
})->name('kategori.group');

// 👶 Kategori: Baby Smash Cake
Route::get('/kategori/baby', function () {
    // Ambil semua item dari kategori 'cetak-foto'
    $printItems = ExtraItem::where('category', 'cetak-foto')->where('is_active', true)->get();

    // Ambil semua item dari kategori 'frame-foto'
    $frameItems = ExtraItem::where('category', 'frame-foto')->where('is_active', true)->get();

    // Ambil semua item dari kategori 'tambahan' (saya asumsikan ini yang Anda maksud dengan 'layanan tambahan')
    $serviceItems = ExtraItem::where('category', 'tambahan-layanan')->where('is_active', true)->get();
    
    $termsAndCondition = TermsAndCondition::all();
    return view('kategori.baby', [
        'printItems' => $printItems,
        'frameItems' => $frameItems,
        'serviceItems' => $serviceItems,
        'terms' => $termsAndCondition
    ]);
})->name('kategori.baby');

// ℹ️ Halaman Info & Sosial Media
Route::get('/info-more', function () {
    return view('info');
})->name('info');

Route::post('/booking', [BookingController::class, 'store']);


Route::middleware(['auth', 'verified'])->group(function(){
    Route::get('/dashboard',[DashboardController::class, 'dashboard'] )->name('dashboard');
    // Route::get('/ekstra-item',[DashboardController::class, 'ekstra'] )->name('ekstra');
    Route::resource('backgrounds', BackgroundController::class);
    Route::resource('bookings', AdminBookingController::class)->except(['create', 'store', 'edit']);
    Route::resource('extra-items', ExtraItemsController::class);
    Route::resource('terms', TermsAndConditionController::class);

    // Route::get('/order', [OrderController::class, 'index'])->name('order');
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
