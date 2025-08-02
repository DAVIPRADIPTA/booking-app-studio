<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtraItem extends Model
{
    protected $fillable = [
        'name',
        'price',
        'category',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public const CATEGORIES = [
        'cetak-foto',
        'frame-foto',
        'tambahan-layanan',
    ];
}
