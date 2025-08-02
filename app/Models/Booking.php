<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'contact_name',
        'whatsapp_number',
        'booking_date',
        'booking_time',
        'session_name',
        'package_name',
        'selected_backgrounds',
        'selected_extra_items',
        'total_price',
        'notes',
        'status',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'selected_backgrounds' => 'array',
        'selected_extra_items' => 'array',
    ];
}
