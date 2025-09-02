<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * CustomerController
 *
 * Responsible for customer-facing pages such as booking history.
 */
class CustomerController extends Controller
{
    /**
     * Tampilkan daftar / riwayat booking untuk customer yang login.
     *
     * - Auto-cancel booking 'waiting_payment' yang melewati payment_deadline.
     * - Support filter: q (search contact_name/whatsapp/package), status, date_from, date_to.
     * - Paginate dan kirim statusCounts untuk ringkasan di UI.
     *
     * Default ordering: newest first by id (id DESC).
     *
     * @param Request $request
     * @return View|RedirectResponse
     */
    public function bookings(Request $request)
    {
        // pastikan customer login
        $customer = Auth::guard('customer')->user();
        if (! $customer) {
            return redirect()->route('customer.login');
        }

        try {
            // ------------------------------
            // 1) Auto-cancel overdue waiting_payment untuk customer ini
            // ------------------------------
            $pendingToCheck = Booking::where('customer_id', $customer->id)
                ->where('status', Booking::STATUS_WAITING_PAYMENT)
                ->whereNotNull('payment_deadline')
                ->get();

            foreach ($pendingToCheck as $b) {
                try {
                    if (method_exists($b, 'needsAutoCancellation') && $b->needsAutoCancellation()) {
                        $b->autoCancel();
                    }
                } catch (\Throwable $e) {
                    // log per-record, jangan ganggu UX
                    Log::warning("Auto-cancel gagal untuk booking #{$b->id}: {$e->getMessage()}", [
                        'booking_id' => $b->id,
                        'customer_id' => $customer->id,
                    ]);
                }
            }

            // ------------------------------
            // 2) Build query untuk menampilkan bookings customer
            // ------------------------------
            $query = Booking::query()->where('customer_id', $customer->id);

            // quick search q => contact_name / whatsapp_number / package_name
            if ($request->filled('q')) {
                $q = trim($request->q);
                // simple sanitization
                $q = mb_substr($q, 0, 200);
                $query->where(function ($sub) use ($q) {
                    $sub->where('contact_name', 'like', "%{$q}%")
                        ->orWhere('whatsapp_number', 'like', "%{$q}%")
                        ->orWhere('package_name', 'like', "%{$q}%");
                });
            }

            // status filter (optional)
            if ($request->filled('status') && $request->status !== 'all') {
                $status = $request->status;
                // only allow known statuses
                if (in_array($status, Booking::statuses(), true)) {
                    $query->where('status', $status);
                }
            }

            // date range on booking_date (safe parsing)
            if ($request->filled('date_from')) {
                try {
                    $df = Carbon::parse($request->date_from)->toDateString();
                    $query->whereDate('booking_date', '>=', $df);
                } catch (\Throwable $e) {
                    // ignore invalid date_from
                }
            }
            if ($request->filled('date_to')) {
                try {
                    $dt = Carbon::parse($request->date_to)->toDateString();
                    $query->whereDate('booking_date', '<=', $dt);
                } catch (\Throwable $e) {
                    // ignore invalid date_to
                }
            }

            // sort: default newest by id (id desc)
            $sortBy = $request->get('sort_by', 'id_desc');
            switch ($sortBy) {
                case 'booking_date_asc':
                    $query->orderBy('booking_date', 'asc')->orderBy('booking_time', 'asc');
                    break;
                case 'booking_date_desc':
                    $query->orderBy('booking_date', 'desc')->orderBy('booking_time', 'desc');
                    break;
                case 'created_at_asc':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'created_at_desc':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'id_asc':
                    $query->orderBy('id', 'asc');
                    break;
                case 'id_desc':
                default:
                    $query->orderBy('id', 'desc');
                    break;
            }

            // pagination with sane limits
            $perPage = (int) $request->get('per_page', 10);
            $perPage = max(1, min(100, $perPage));

            $bookings = $query->paginate($perPage)->withQueryString();

            // ------------------------------
            // 3) Status counts (per-customer) untuk ringkasan di UI
            // ------------------------------
            $rawCounts = Booking::where('customer_id', $customer->id)
                ->selectRaw('status, COUNT(*) as cnt')
                ->groupBy('status')
                ->pluck('cnt', 'status')
                ->toArray();

            // ensure all statuses present (default 0)
            $statusCounts = [];
            foreach (Booking::statuses() as $s) {
                $statusCounts[$s] = isset($rawCounts[$s]) ? (int) $rawCounts[$s] : 0;
            }

            // tambahan: apakah ada permohonan pembatalan aktif
            $pendingCancellationCount = Booking::where('customer_id', $customer->id)
                ->where('cancellation_requested', true)
                ->count();

            // jam minimal sebelum bisa batalkan (konsisten dg model)
            $hoursBeforeCancel = 24;

            // kirim ke view
            return view('customer.bookings.index', compact(
                'bookings',
                'statusCounts',
                'pendingCancellationCount',
                'hoursBeforeCancel'
            ));
        } catch (\Throwable $e) {
            Log::error('Gagal menampilkan riwayat booking customer: ' . $e->getMessage(), [
                'customer_id' => $customer->id ?? null,
                'exception' => $e,
            ]);

            return back()->with('errorMessage', 'Terjadi kesalahan saat memuat riwayat pemesanan. Silakan coba lagi.');
        }
    }
}
