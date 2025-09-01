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

    /**
     * Default timeslots (ubah jika perlu)
     *
     * @var array
     */
    protected static array $defaultTimes = [
        '10:00', '11:00', '12:00', '13:00',
        '14:00', '15:00', '16:00'
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
    /**
     * Kembalikan selected_backgrounds sebagai array objek yang
     * mengandung minimal ['id', 'name', 'image'] jika tersedia.
     *
     * @param  mixed  $value
     * @return array
     */
    public function getSelectedBackgroundsAttribute($value)
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
            return [
                'id'    => $bg['id']    ?? null,
                'name'  => $bg['name']  ?? ($bg['title'] ?? 'Background'),
                'image' => $bg['image'] ?? null,
            ];
        })->toArray();
    }

    /**
     * Kembalikan selected_extra_items sebagai array objek yang
     * mengandung minimal ['id', 'name', 'price'] jika tersedia.
     *
     * @param  mixed  $value
     * @return array
     */
    public function getSelectedExtraItemsAttribute($value)
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
            return [
                'id'    => $item['id']    ?? null,
                'name'  => $item['name']  ?? 'Extra Item',
                'price' => isset($item['price']) ? (int) $item['price'] : 0,
            ];
        })->toArray();
    }

    // --- MUTATORS ---
    /**
     * Pastikan selected_backgrounds disimpan dalam bentuk JSON yang konsisten.
     */
    public function setSelectedBackgroundsAttribute($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $value = $decoded;
            }
        }

        if (!is_array($value)) {
            $value = [];
        }

        $normalized = array_map(function ($bg) {
            if (is_array($bg)) {
                return [
                    'id'    => $bg['id']    ?? ($bg[0] ?? null),
                    'name'  => $bg['name']  ?? ($bg['title'] ?? null),
                    'image' => $bg['image'] ?? null,
                ];
            }
            return [
                'id' => $bg,
            ];
        }, $value);

        $this->attributes['selected_backgrounds'] = json_encode($normalized);
    }

    /**
     * Pastikan selected_extra_items disimpan konsisten.
     */
    public function setSelectedExtraItemsAttribute($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $value = $decoded;
            }
        }

        if (!is_array($value)) {
            $value = [];
        }

        $normalized = array_map(function ($it) {
            if (is_array($it)) {
                return [
                    'id'    => $it['id']    ?? ($it[0] ?? null),
                    'name'  => $it['name']  ?? null,
                    'price' => isset($it['price']) ? (int) $it['price'] : 0,
                ];
            }
            return [
                'id' => $it,
            ];
        }, $value);

        $this->attributes['selected_extra_items'] = json_encode($normalized);
    }

    // --- STATIC HELPERS ---
    /**
     * Dapatkan semua slot jam tetap (untuk halaman admin/frontend yang perlu daftar jam).
     *
     * @return array
     */
    public static function getAllTimes(): array
    {
        return self::$defaultTimes;
    }

    /**
     * Dapatkan waktu yang tersedia untuk sebuah tanggal.
     *
     * @param  string|null  $date  (format YYYY-MM-DD). Jika null -> kembalikan struktur kosong.
     * @return array [
     *   'available_times' => array of time strings,
     *   'booked_times'    => array of time strings,
     *   'status'          => 'available'|'limited'|'full',
     *   'date'            => $date,
     * ]
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

        // Ambil booked times untuk tanggal tsb (hanya status yang mem-block)
        $bookedTimes = self::where('booking_date', $date)
            ->whereIn('status', ['waiting_payment', 'pending_verification', 'booked'])
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
     * Cek apakah sebuah slot tersedia.
     *
     * @param  string  $date  Format YYYY-MM-DD
     * @param  string  $time  Format HH:MM atau string waktu yang sama dengan stored value
     * @param  int|null $excludeId  Jika diberi, exclude booking dengan id ini (berguna saat update)
     * @return bool  true = slot tersedia, false = sudah terpakai
     */
    public static function isSlotAvailable(string $date, string $time, ?int $excludeId = null): bool
    {
        $query = self::where('booking_date', $date)
            ->where('booking_time', $time)
            ->whereIn('status', ['waiting_payment', 'pending_verification', 'booked']);

        if (!is_null($excludeId)) {
            $query->where('id', '!=', $excludeId);
        }

        return !$query->exists();
    }
}
