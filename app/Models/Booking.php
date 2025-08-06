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
        'dp_amount',
        'dp_proof',
        'final_payment_amount',
        'final_payment_proof',
        'baby_name',      // tambahkan
        'baby_age',       // tambahkan
    ];


    protected $casts = [
        'booking_date' => 'date',
        'selected_backgrounds' => 'array',
        'selected_extra_items' => 'array',
    ];


    public static function getAvailableTimes($date)
    {
        $booked = self::where('booking_date', $date)->pluck('booking_time')->toArray();
        $all = ['10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00'];
        return array_values(array_diff($all, $booked));
    }
}
