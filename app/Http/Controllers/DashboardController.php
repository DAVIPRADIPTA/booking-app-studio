<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard (simple, dengan chart fallback).
     */
    public function dashboard(Request $request)
    {
        // filter periode default
        $bookingsDays = (int) $request->get('bookings_days', 30);
        $financeMonths = (int) $request->get('finance_months', 12);

        $bookingsDays = max(7, min(180, $bookingsDays));
        $financeMonths = max(1, min(36, $financeMonths));

        $today = Carbon::today();
        $bookingsStart = $today->copy()->subDays($bookingsDays - 1);
        $financeStart = $today->copy()->subMonths($financeMonths - 1)->startOfMonth();

        // KPIs
        $totalBookings = Booking::count();
        $totalRevenue = (float) Booking::whereNotIn('status', [Booking::STATUS_CANCELLED])->sum('total_price');
        $totalRefunds = (float) Booking::whereNotNull('refund_amount')->sum('refund_amount');
        $netRevenue = $totalRevenue - $totalRefunds;
        $pendingPayments = Booking::where('status', Booking::STATUS_WAITING_PAYMENT)->count();
        $pendingVerification = Booking::where('status', Booking::STATUS_PENDING_VERIFICATION)->count();
        $cancellationRequests = Booking::where('cancellation_requested', true)->count();
        $bookingsToday = Booking::whereDate('booking_date', $today->toDateString())->count();
        $avgOrderValue = Booking::whereNotIn('status', [Booking::STATUS_CANCELLED])->avg('total_price') ?? 0;

        // upcoming
        $upcoming = Booking::whereDate('booking_date', '>=', $today->toDateString())
            ->orderBy('booking_date')->orderBy('booking_time')
            ->limit(10)
            ->get(['id','contact_name','booking_date','booking_time','package_name','status','total_price']);

        // top packages (12 months)
        $topPackages = Booking::select('package_name', DB::raw('count(*) as cnt'), DB::raw('COALESCE(SUM(total_price),0) as revenue'))
            ->whereDate('created_at', '>=', $today->copy()->subMonths(12)->toDateString())
            ->groupBy('package_name')
            ->orderByDesc('cnt')
            ->limit(10)
            ->get();

        // financial summary per month (driver-aware)
        $driver = DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            $ymExpr = "strftime('%Y-%m', created_at)";
            $ymExprRefund = "strftime('%Y-%m', auto_cancelled_at)";
        } else {
            // mysql, mariadb
            $ymExpr = "DATE_FORMAT(created_at, '%Y-%m')";
            $ymExprRefund = "DATE_FORMAT(auto_cancelled_at, '%Y-%m')";
        }

        $months = [];
        for ($m = 0; $m < $financeMonths; $m++) {
            $dt = $financeStart->copy()->addMonths($m);
            $label = $dt->format('Y-m');
            $months[$label] = [
                'label' => $dt->format('M Y'),
                'revenue' => 0.0,
                'refunds' => 0.0,
                'net' => 0.0,
            ];
        }

        $revRows = Booking::select(DB::raw("$ymExpr as ym"), DB::raw('COALESCE(SUM(total_price),0) as sum'))
            ->whereDate('created_at', '>=', $financeStart->toDateString())
            ->whereNotIn('status', [Booking::STATUS_CANCELLED])
            ->groupBy('ym')
            ->get();

        foreach ($revRows as $r) {
            if (isset($months[$r->ym])) {
                $months[$r->ym]['revenue'] = (float) $r->sum;
            }
        }

        $refundRows = Booking::select(DB::raw("$ymExprRefund as ym"), DB::raw('COALESCE(SUM(refund_amount),0) as sum'))
            ->whereNotNull('refund_amount')
            ->whereDate('auto_cancelled_at', '>=', $financeStart->toDateString())
            ->groupBy('ym')
            ->get();

        foreach ($refundRows as $r) {
            if (isset($months[$r->ym])) {
                $months[$r->ym]['refunds'] = (float) $r->sum;
            }
        }

        foreach ($months as $k => $v) {
            $months[$k]['net'] = $v['revenue'] - $v['refunds'];
        }

        // extras revenue (approx)
        $extrasRevenue = 0;
        $extrasCount = 0;
        $bookingsWithExtras = Booking::whereDate('created_at', '>=', $financeStart->toDateString())
            ->whereNotNull('selected_extra_items')
            ->get(['selected_extra_items']);

        foreach ($bookingsWithExtras as $b) {
            $items = $b->selected_extra_items ?? [];
            if (!is_array($items)) {
                $items = json_decode($items, true) ?: [];
            }
            foreach ($items as $it) {
                $price = (float) ($it['price'] ?? 0);
                if ($price > 0) {
                    $extrasRevenue += $price;
                    $extrasCount++;
                }
            }
        }

        // bookings by day for last N days (for chart)
        $bookingsByDayRows = Booking::select(DB::raw("DATE(booking_date) as day"), DB::raw('count(*) as cnt'))
            ->whereDate('booking_date', '>=', $bookingsStart->toDateString())
            ->groupBy('day')
            ->orderBy('day','asc')
            ->get();

        // transform to arrays for chart (fill missing days)
        $days = [];
        for ($d = 0; $d < $bookingsDays; $d++) {
            $dt = $bookingsStart->copy()->addDays($d);
            $days[$dt->toDateString()] = 0;
        }
        foreach ($bookingsByDayRows as $r) {
            $days[$r->day] = (int) $r->cnt;
        }

        $bookingsByDay = collect($days)->map(fn($v,$k) => ['day' => $k, 'count' => $v])->values()->toArray();

        // cancellations latest
        $cancellations = Booking::where('cancellation_requested', true)
            ->orderByDesc('cancellation_requested_at')
            ->limit(10)
            ->get(['id','contact_name','package_name','booking_date','cancellation_reason','cancellation_requested_at','status']);

        // bookings list (paginated)
        $listQuery = Booking::query();
        if ($request->filled('q')) {
            $q = trim($request->q);
            $listQuery->where(function($sq) use ($q){
                $sq->where('contact_name','like',"%{$q}%")
                   ->orWhere('whatsapp_number','like',"%{$q}%")
                   ->orWhere('package_name','like',"%{$q}%");
            });
        }
        if ($request->filled('status') && $request->status !== 'all') {
            $listQuery->where('status', $request->status);
        }
        if ($request->filled('date_from')) $listQuery->whereDate('booking_date','>=', $request->date_from);
        if ($request->filled('date_to')) $listQuery->whereDate('booking_date','<=', $request->date_to);

        $bookingsList = $listQuery->orderByDesc('created_at')->paginate(15)->withQueryString();

        // prepare chart payloads (JSON-safe)
        $chartBookingsDays = json_encode(array_values($bookingsByDay), JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT);
        $chartMonths = json_encode(array_values($months), JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT);

        return view('admin.dashboard', compact(
            'totalBookings','totalRevenue','totalRefunds','netRevenue','pendingPayments','pendingVerification','cancellationRequests',
            'bookingsToday','avgOrderValue','upcoming','topPackages','months','extrasRevenue','extrasCount','bookingsByDay','cancellations','bookingsList','bookingsDays','financeMonths',
            'chartBookingsDays','chartMonths'
        ));
    }

    /**
     * Export bookings CSV (filtered by same params as list).
     */
    public function exportBookingsCsv(Request $request)
    {
        $query = Booking::query();
        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function($sq) use ($q){
                $sq->where('contact_name','like',"%{$q}%")
                   ->orWhere('whatsapp_number','like',"%{$q}%")
                   ->orWhere('package_name','like',"%{$q}%");
            });
        }
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) $query->whereDate('booking_date','>=',$request->date_from);
        if ($request->filled('date_to')) $query->whereDate('booking_date','<=',$request->date_to);

        $fileName = 'bookings_export_'.now()->format('Ymd_His').'.csv';

        $response = new StreamedResponse(function() use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['id','contact_name','whatsapp_number','package_name','booking_date','booking_time','status','total_price','payment_method','created_at']);
            $query->orderByDesc('created_at')->chunk(500, function($rows) use ($handle){
                foreach ($rows as $r) {
                    fputcsv($handle, [
                        $r->id,$r->contact_name,$r->whatsapp_number,$r->package_name,$r->booking_date,$r->booking_time,$r->status,$r->total_price,$r->payment_method,$r->created_at
                    ]);
                }
            });
            fclose($handle);
        });

        $response->headers->set('Content-Type','text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition',"attachment; filename={$fileName}");
        return $response;
    }

    /**
     * Export financial summary CSV (months table).
     */
    public function exportFinancialCsv(Request $request)
    {
        $months = json_decode($request->get('months_json', '[]'), true);
        $fileName = 'financial_summary_'.now()->format('Ymd_His').'.csv';
        $response = new StreamedResponse(function() use ($months) {
            $handle = fopen('php://output','w');
            fputcsv($handle, ['month_label','revenue','refunds','net']);
            foreach ($months as $m) {
                fputcsv($handle, [$m['label'], $m['revenue'], $m['refunds'], $m['net']]);
            }
            fclose($handle);
        });
        $response->headers->set('Content-Type','text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition',"attachment; filename={$fileName}");
        return $response;
    }
}
