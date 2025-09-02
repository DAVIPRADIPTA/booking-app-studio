<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Log; // <-- tambahkan ini


/**
 * Class Booking
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $customer_id
 * @property string $contact_name
 * @property string $whatsapp_number
 * @property string $booking_date
 * @property string $booking_time
 * @property string|null $session_name
 * @property string|null $package_name
 * @property array|null $selected_backgrounds
 * @property array|null $selected_extra_items
 * @property int|null $total_price
 * @property string|null $payment_method
 * @property string|null $notes
 * @property string|null $status
 * @property \Illuminate\Support\Carbon|null $payment_deadline
 * @property string|null $payment_proof
 * @property bool|null $cancellation_requested
 * @property \Illuminate\Support\Carbon|null $cancellation_requested_at
 * @property string|null $cancellation_reason
 * @property float|null $refund_amount
 * @property string|null $refund_proof
 * @property \Illuminate\Support\Carbon|null $auto_cancelled_at
 * @property string|null $baby_name
 * @property string|null $baby_age
 */
class Booking extends Model
{
    // -------------------------
    // Status constants
    // -------------------------
    public const STATUS_WAITING_PAYMENT = 'waiting_payment';
    public const STATUS_PENDING_VERIFICATION = 'pending_verification';
    public const STATUS_BOOKED = 'booked';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * Fillable attributes
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
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

    /**
     * Casts
     *
     * @var array
     */
    protected $casts = [
        'selected_backgrounds'      => 'array',
        'selected_extra_items'      => 'array',
        'payment_deadline'          => 'datetime',
        'auto_cancelled_at'         => 'datetime',
        'cancellation_requested'    => 'boolean',
        'cancellation_requested_at' => 'datetime',
        'refund_amount'             => 'decimal:2',
        'total_price'               => 'integer',
    ];

    /**
     * Default available times (can be moved to config if needed)
     *
     * @var array
     */
    protected static array $defaultTimes = [
        '10:00', '11:00', '12:00', '13:00',
        '14:00', '15:00', '16:00'
    ];

    // -------------------------
    // Relations
    // -------------------------
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // -------------------------
    // Status helpers
    // -------------------------
    public static function statuses(): array
    {
        return [
            self::STATUS_WAITING_PAYMENT,
            self::STATUS_PENDING_VERIFICATION,
            self::STATUS_BOOKED,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
        ];
    }

    /**
     * Return badge metadata for current status.
     *
     * Usage in Blade: $badge = $booking->status_badge;
     *  - label: human-readable label
     *  - color: full background+text classes for badge container
     *  - dot: classes for the small dot color
     *
     * @return array
     */
    public function getStatusBadgeAttribute(): array
    {
        $map = [
            self::STATUS_WAITING_PAYMENT => [
                'label' => 'Menunggu Pembayaran',
                'color' => 'bg-yellow-100 text-yellow-800',
                'dot'   => 'bg-yellow-500'
            ],
            self::STATUS_PENDING_VERIFICATION => [
                'label' => 'Menunggu Verifikasi',
                'color' => 'bg-blue-100 text-blue-800',
                'dot'   => 'bg-blue-600'
            ],
            self::STATUS_BOOKED => [
                'label' => 'Dikonfirmasi',
                'color' => 'bg-green-100 text-green-800',
                'dot'   => 'bg-green-600'
            ],
            self::STATUS_COMPLETED => [
                'label' => 'Selesai',
                'color' => 'bg-gray-100 text-gray-700',
                'dot'   => 'bg-gray-500'
            ],
            self::STATUS_CANCELLED => [
                'label' => 'Dibatalkan',
                'color' => 'bg-red-100 text-red-800',
                'dot'   => 'bg-red-600'
            ],
        ];

        $st = (string) $this->status;
        // defensive normalization for legacy values
        $normalized = strtolower(trim($st));
        if ($normalized === 'pending') {
            $normalized = self::STATUS_WAITING_PAYMENT;
        }

        return $map[$normalized] ?? [
            'label' => ucfirst(str_replace(['_','-'], ' ', $normalized ?: 'unknown')),
            'color' => 'bg-gray-100 text-gray-700',
            'dot'   => 'bg-gray-400'
        ];
    }

    /**
     * Shortcut to obtain human label for status.
     *
     * @return string
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->status_badge['label'] ?? (string) $this->status;
    }

    /**
     * Normalize status when setting it. This converts common legacy or
     * malformed inputs (e.g. "pending", "pending-verification") into
     * canonical enum values and logs an unexpected value.
     *
     * @param mixed $value
     * @return void
     */
    public function setStatusAttribute($value): void
    {
        if (is_null($value) || $value === '') {
            $this->attributes['status'] = null;
            return;
        }

        $s = strtolower(trim((string) $value));
        // unify separators
        $s = str_replace(['-', ' '], '_', $s);

        // legacy mappings
        if ($s === 'pending') {
            $s = self::STATUS_WAITING_PAYMENT;
        }
        if (in_array($s, ['pendingverification', 'pending_verification', 'pending-verification'], true)) {
            $s = self::STATUS_PENDING_VERIFICATION;
        }
        if (in_array($s, ['waitingpayment', 'waiting_payment', 'waiting-payment'], true)) {
            $s = self::STATUS_WAITING_PAYMENT;
        }

        // final guard: unknown statuses fallback to waiting_payment (and log)
        if (!in_array($s, self::statuses(), true)) {
            Log::warning("Booking::setStatusAttribute - unknown status '{$value}' normalized to waiting_payment");
            $s = self::STATUS_WAITING_PAYMENT;
        }

        $this->attributes['status'] = $s;
    }

    // -------------------------
    // Payment window helpers
    // -------------------------

    /**
     * Check whether booking is still within payment window
     *
     * @return bool
     */
    public function isWithinPaymentWindow(): bool
    {
        return $this->payment_deadline && now()->lessThan($this->payment_deadline);
    }

    /**
     * Remaining seconds until payment deadline (0 if none/passed)
     *
     * @return int
     */
    public function getRemainingPaymentTime(): int
    {
        if (!$this->payment_deadline) {
            return 0;
        }
        return max(0, $this->payment_deadline->timestamp - now()->timestamp);
    }

    /**
     * Whether booking needs auto-cancellation (deadline passed and still waiting_payment)
     *
     * @return bool
     */
    public function needsAutoCancellation(): bool
    {
        return $this->status === self::STATUS_WAITING_PAYMENT
            && $this->payment_deadline
            && now()->greaterThan($this->payment_deadline);
    }

    /**
     * Perform auto-cancel (update status and note)
     *
     * @return bool
     */
    public function autoCancel(): bool
    {
        if (!$this->needsAutoCancellation()) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_CANCELLED,
            'auto_cancelled_at' => now(),
            'cancellation_reason' => 'Booking otomatis dibatalkan karena melewati batas waktu pembayaran',
        ]);

        return true;
    }

    /**
     * Human friendly remaining time (hari/jam/menit)
     *
     * @return string
     */
    public function getRemainingTimeFormatted(): string
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

        $parts = [];
        if ($diff->d > 0) {
            $parts[] = $diff->d . ' hari';
        }
        if ($diff->h > 0) {
            $parts[] = $diff->h . ' jam';
        }
        if ($diff->i > 0 && $diff->d === 0) {
            $parts[] = $diff->i . ' menit';
        } elseif ($diff->i > 0 && $diff->d > 0 && $diff->h === 0) {
            $parts[] = $diff->i . ' menit';
        }

        return implode(' ', $parts) ?: 'Kurang dari 1 menit';
    }

    // -------------------------
    // JSON accessors/mutators (normalization & safety)
    // -------------------------

    /**
     * Ensure selected backgrounds always return structured array
     *
     * @param mixed $value
     * @return array
     */
    public function getSelectedBackgroundsAttribute($value): array
    {
        $raw = $value;
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            $raw = $decoded === null ? [] : $decoded;
        }
        if (!is_array($raw)) {
            $raw = [];
        }

        return collect($raw)->map(function ($bg) {
            $id = $bg['id'] ?? ($bg[0] ?? null) ?? null;
            if (is_numeric($id)) {
                $id = (int) $id;
            }
            return [
                'id' => $id,
                'name' => $bg['name'] ?? ($bg['title'] ?? 'Background'),
                'image' => $bg['image'] ?? null,
            ];
        })->toArray();
    }

    /**
     * Ensure selected extra items always return structured array
     *
     * @param mixed $value
     * @return array
     */
    public function getSelectedExtraItemsAttribute($value): array
    {
        $raw = $value;
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            $raw = $decoded === null ? [] : $decoded;
        }
        if (!is_array($raw)) {
            $raw = [];
        }

        return collect($raw)->map(function ($item) {
            $id = $item['id'] ?? ($item[0] ?? null) ?? null;
            if (is_numeric($id)) {
                $id = (int) $id;
            }
            return [
                'id' => $id,
                'name' => $item['name'] ?? 'Extra Item',
                'price' => isset($item['price']) ? (int)$item['price'] : 0,
            ];
        })->toArray();
    }

    /**
     * Normalize selected backgrounds on set
     *
     * @param mixed $value
     * @return void
     */
    public function setSelectedBackgroundsAttribute($value): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $value = $decoded;
            }
        }

        if (!is_array($value)) {
            $value = [];
        }

        $normalized = array_map(function ($bg) {
            if (is_array($bg)) {
                $id = $bg['id'] ?? ($bg[0] ?? null) ?? null;
                if (is_numeric($id)) $id = (int)$id;
                return [
                    'id' => $id,
                    'name' => $bg['name'] ?? ($bg['title'] ?? null),
                    'image' => $bg['image'] ?? null,
                ];
            }
            $id = is_numeric($bg) ? (int)$bg : $bg;
            return ['id' => $id];
        }, $value);

        $json = json_encode($normalized, JSON_UNESCAPED_UNICODE);
        $this->attributes['selected_backgrounds'] = $json === false ? '[]' : $json;
    }

    /**
     * Normalize selected extra items on set
     *
     * @param mixed $value
     * @return void
     */
    public function setSelectedExtraItemsAttribute($value): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $value = $decoded;
            }
        }

        if (!is_array($value)) {
            $value = [];
        }

        $normalized = array_map(function ($it) {
            if (is_array($it)) {
                $id = $it['id'] ?? ($it[0] ?? null) ?? null;
                if (is_numeric($id)) $id = (int)$id;
                return [
                    'id' => $id,
                    'name' => $it['name'] ?? null,
                    'price' => isset($it['price']) ? (int)$it['price'] : 0,
                ];
            }
            $id = is_numeric($it) ? (int)$it : $it;
            return ['id' => $id];
        }, $value);

        $json = json_encode($normalized, JSON_UNESCAPED_UNICODE);
        $this->attributes['selected_extra_items'] = $json === false ? '[]' : $json;
    }

    // -------------------------
    // Small helpers for blade/views
    // -------------------------

    /**
     * Public URL for payment proof.
     *
     * - Prefer asset('storage/...') when storage symlink exists.
     * - Fallback to route('booking.proof', ...) if file not accessible directly (useful on XAMPP).
     *
     * @return string|null
     */
    public function getPaymentProofUrlAttribute(): ?string
    {
        if (empty($this->payment_proof)) {
            return null;
        }

        $disk = Storage::disk('public');

        if ($disk->exists($this->payment_proof)) {
            // asset('storage/...') will generate URL like /storage/<path>
            return asset('storage/' . ltrim($this->payment_proof, '/'));
        }

        // fallback route — requires route booking.proof implemented as a controller that checks ownership.
        try {
            return route('booking.proof', ['booking' => $this->id, 'type' => 'payment']);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Public URL for refund proof.
     *
     * @return string|null
     */
    public function getRefundProofUrlAttribute(): ?string
    {
        if (empty($this->refund_proof)) {
            return null;
        }

        $disk = Storage::disk('public');

        if ($disk->exists($this->refund_proof)) {
            return asset('storage/' . ltrim($this->refund_proof, '/'));
        }

        try {
            return route('booking.proof', ['booking' => $this->id, 'type' => 'refund']);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Determine whether a payment proof file exists on disk
     *
     * @return bool
     */
    public function paymentProofExists(): bool
    {
        if (empty($this->payment_proof)) return false;
        return Storage::disk('public')->exists($this->payment_proof);
    }

    /**
     * Determine whether a refund proof file exists on disk
     *
     * @return bool
     */
    public function refundProofExists(): bool
    {
        if (empty($this->refund_proof)) return false;
        return Storage::disk('public')->exists($this->refund_proof);
    }

    /**
     * Sum of extra items price (integer)
     *
     * @return int
     */
    public function extrasTotal(): int
    {
        $items = $this->selected_extra_items ?? [];
        return array_sum(array_map(function ($it) {
            return (int)($it['price'] ?? 0);
        }, $items));
    }

    /**
     * Return array of background names
     *
     * @return array
     */
    public function backgroundNames(): array
    {
        $b = $this->selected_backgrounds ?? [];
        return array_map(function ($x) { return $x['name'] ?? '-'; }, $b);
    }

    // -------------------------
    // Cancellation helpers
    // -------------------------

    /**
     * Whether customer can request cancellation for this booking now.
     *
     * Rules (centralized):
     *  - Not allowed if status is cancelled/completed.
     *  - Allowed if status is waiting_payment (will cancel immediately), pending_verification, or booked.
     *  - Must be at least $hoursBefore hours before booking datetime (default 24).
     *
     * @param int $hoursBefore
     * @return bool
     */
    public function canBeCancelledByCustomer(int $hoursBefore = 24): bool
    {
        if (in_array($this->status, [self::STATUS_CANCELLED, self::STATUS_COMPLETED])) {
            return false;
        }

        // prepare booking datetime
        try {
            $bookingDateTime = Carbon::createFromFormat('Y-m-d H:i', $this->booking_date . ' ' . $this->booking_time);
        } catch (\Throwable $e) {
            $bookingDateTime = Carbon::parse($this->booking_date . ' ' . $this->booking_time);
        }

        $cutoff = $bookingDateTime->copy()->subHours($hoursBefore);
        return now()->lessThan($cutoff);
    }

    /**
     * Suggested refund amount based on default percent (e.g. 90%).
     *
     * @param float $percent
     * @return int
     */
    public function getSuggestedRefundAmount(float $percent = 0.9): int
    {
        $total = (int) ($this->total_price ?? 0);
        return (int) round($total * $percent);
    }

    /**
     * Formatted total price
     *
     * @return string
     */
    public function formattedTotal(): string
    {
        return 'Rp ' . number_format((int) ($this->total_price ?? 0), 0, ',', '.');
    }

    /**
     * Formatted refund amount (if set)
     *
     * @return string|null
     */
    public function formattedRefund(): ?string
    {
        if (is_null($this->refund_amount)) return null;
        return 'Rp ' . number_format((float) $this->refund_amount, 0, ',', '.');
    }

    // -------------------------
    // Static helpers for times & availability
    // -------------------------

    /**
     * All times
     *
     * @return array
     */
    public static function getAllTimes(): array
    {
        return self::$defaultTimes;
    }

    /**
     * Return availability info for a date
     *
     * @param string|null $date
     * @return array
     */
    public static function getAvailableTimes(?string $date = null): array
    {
        if (empty($date)) {
            return [
                'available_times' => [],
                'booked_times'    => [],
                'status'          => 'available',
                'date'            => $date,
            ];
        }

        $allTimes = self::$defaultTimes;

        $bookedTimes = self::where('booking_date', $date)
            ->whereIn('status', [self::STATUS_WAITING_PAYMENT, self::STATUS_PENDING_VERIFICATION, self::STATUS_BOOKED])
            ->pluck('booking_time')
            ->toArray();

        $bookedTimes = array_values(array_map('strval', $bookedTimes));
        $availableTimes = array_values(array_diff($allTimes, $bookedTimes));

        $status = 'available';
        if (empty($availableTimes)) {
            $status = 'full';
        } elseif (count($availableTimes) < 3) {
            $status = 'limited';
        }

        return [
            'available_times' => $availableTimes,
            'booked_times'    => $bookedTimes,
            'status'          => $status,
            'date'            => $date,
        ];
    }

    /**
     * Check if slot is available (optionally exclude a booking id)
     *
     * @param string $date
     * @param string $time
     * @param int|null $excludeId
     * @return bool
     */
    public static function isSlotAvailable(string $date, string $time, ?int $excludeId = null): bool
    {
        $query = self::where('booking_date', $date)
            ->where('booking_time', $time)
            ->whereIn('status', [self::STATUS_WAITING_PAYMENT, self::STATUS_PENDING_VERIFICATION, self::STATUS_BOOKED]);

        if (!is_null($excludeId)) {
            $query->where('id', '!=', $excludeId);
        }

        return !$query->exists();
    }
}
