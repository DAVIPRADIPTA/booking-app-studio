<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
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

    // --- deadline helpers ---
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
            'cancellation_reason' => 'Booking otomatis dibatalkan karena melewati batas waktu 10 menit',
        ]);

        return true;
    }
}
