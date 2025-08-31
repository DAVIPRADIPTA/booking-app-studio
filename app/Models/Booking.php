<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Booking extends Model
{
    protected $fillable = [
        'user_id',   // admin yang input
        'customer_id',
        'contact_name',
        'whatsapp_number',
        'booking_date',
        'booking_time',
        'session_name',
        'package_name',
        'selected_backgrounds',
        'selected_extra_items',
        'total_price',
        'payment_method',
        'notes',
        'status',
        'payment_deadline',
        'payment_proof',
        'cancellation_requested',
        'cancellation_requested_at',
        'cancellation_reason',
        'refund_amount',
        'refund_proof',
        'auto_cancelled_at',
        'baby_name',
        'baby_age',
    ];

    protected $casts = [
        'selected_backgrounds'      => 'array',
        'selected_extra_items'      => 'array',
        'payment_deadline'          => 'datetime',
        'auto_cancelled_at'         => 'datetime',
        'cancellation_requested'    => 'boolean',
        'cancellation_requested_at' => 'datetime',
        'refund_amount'             => 'decimal:2',
    ];

    // --- RELASI ---
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class); // admin
    }

    // --- DEADLINE HELPERS ---
    public function isWithinPaymentWindow(): bool
    {
        return $this->payment_deadline && now()->lessThan($this->payment_deadline);
    }

    public function getRemainingPaymentTime(): int
    {
        if (!$this->payment_deadline) return 0;
        return max(0, $this->payment_deadline->timestamp - now()->timestamp);
    }

    public function needsAutoCancellation(): bool
    {
        return $this->status === 'waiting_payment'
            && $this->payment_deadline
            && now()->greaterThan($this->payment_deadline);
    }

    public function autoCancel(): bool
    {
        if (!$this->needsAutoCancellation()) return false;

        $this->update([
            'status' => 'cancelled',
            'auto_cancelled_at' => now(),
            'cancellation_reason' => 'Booking otomatis dibatalkan karena melewati batas waktu pembayaran',
        ]);

        return true;
    }

    public function getRemainingTimeFormatted()
    {
        if (!$this->payment_deadline) {
            return '-';
        }

        $now = Carbon::now();
        $deadline = Carbon::parse($this->payment_deadline);

        if ($now->greaterThan($deadline)) {
            return 'Waktu habis';
        }

        $diff = $now->diff($deadline);

        if ($diff->h > 0) {
            return $diff->h . ' jam ' . $diff->i . ' menit';
        }

        return $diff->i . ' menit';
    }

    // --- ACCESSORS ---
    public function getSelectedBackgroundsAttribute($value)
    {
        $backgrounds = json_decode($value, true) ?? [];
        return collect($backgrounds)->map(function ($bg) {
            return [
                'name'  => $bg['name']  ?? 'Background',
                'image' => $bg['image'] ?? null,
            ];
        })->toArray();
    }

    public function getSelectedExtraItemsAttribute($value)
    {
        $items = json_decode($value, true) ?? [];
        return collect($items)->map(function ($item) {
            return [
                'name'  => $item['name']  ?? 'Extra Item',
                'price' => $item['price'] ?? 0,
            ];
        })->toArray();
    }
}
