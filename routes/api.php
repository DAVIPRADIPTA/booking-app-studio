<?php

use App\Models\Background;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ... kode rute lainnya di sini ...

Route::get('/backgrounds/{category}', function (string $category) {
    $backgrounds = Background::where('category', $category)
        ->where('is_active', true)
        ->get();

    return response()->json($backgrounds);
});
