<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    /**
     * Render dashboard view (server snapshot, default 14 days)
     */
    public function dashboard(Request $request)
    {
        $days = 14;
        $today = Carbon::today();
        $start = (clone $today)->subDays($days - 1);

        // top-level stats
        $totalBookings = Booking::count();
        $bookingsToday = Booking::whereDate('created_at', $today)->count();
        $bookingsThisWeek = Booking::whereBetween('created_at', [(clone $today)->startOfWeek(), (clone $today)->endOfWeek()])->count();
        $bookingsThisMonth = Booking::whereBetween('created_at', [(clone $today)->startOfMonth(), (clone $today)->endOfMonth()])->count();

        // revenue (only booked/completed)
        $revenueBase = Booking::whereIn('status', ['booked', 'completed']);
        $revenueToday = (float) $revenueBase->whereDate('created_at', $today)->sum('total_price');
        $revenueWeek  = (float) $revenueBase->whereBetween('created_at', [(clone $today)->startOfWeek(), (clone $today)->endOfWeek()])->sum('total_price');
        $revenueMonth = (float) $revenueBase->whereBetween('created_at', [(clone $today)->startOfMonth(), (clone $today)->endOfMonth()])->sum('total_price');

        // recent bookings (server snapshot)
        $recentBookings = Booking::orderBy('created_at', 'desc')->limit(8)->get();

        // line chart: bookings per day for last $days
        $counts = Booking::selectRaw('DATE(created_at) as dt, count(*) as cnt')
            ->whereBetween('created_at', [$start->startOfDay(), $today->endOfDay()])
            ->groupBy('dt')
            ->pluck('cnt', 'dt')
            ->toArray();

        $lineLabels = [];
        $lineData = [];
        for ($i = 0; $i < $days; $i++) {
            $d = (clone $start)->addDays($i);
            $lineLabels[] = $d->format('d M');
            $lineData[] = (int) ($counts[$d->toDateString()] ?? 0);
        }

        // bar chart: top packages in period
        $packages = Booking::selectRaw('package_name, count(*) as cnt')
            ->whereBetween('created_at', [$start->startOfDay(), $today->endOfDay()])
            ->whereNotNull('package_name')
            ->groupBy('package_name')
            ->orderByDesc('cnt')
            ->limit(8)
            ->get()
            ->toArray();

        $barLabels = array_map(fn($r) => $r['package_name'] ?? '—', $packages);
        $barData   = array_map(fn($r) => (int)$r['cnt'], $packages);

        // pie chart: status distribution in period
        $statusCounts = Booking::selectRaw('status, count(*) as cnt')
            ->whereBetween('created_at', [$start->startOfDay(), $today->endOfDay()])
            ->groupBy('status')
            ->pluck('cnt', 'status')
            ->toArray();

        $commonStatuses = ['waiting_payment','pending_verification','booked','completed','cancelled'];
        $pieLabels = [];
        $pieData = [];
        foreach ($commonStatuses as $s) {
            $pieLabels[] = $s;
            $pieData[] = (int) ($statusCounts[$s] ?? 0);
        }
        foreach (array_diff(array_keys($statusCounts), $commonStatuses) as $s) {
            $pieLabels[] = $s;
            $pieData[] = (int) ($statusCounts[$s] ?? 0);
        }

        // top extras parsing (safe)
        $bookingsWithExtras = Booking::select('selected_extra_items')
            ->whereBetween('created_at', [$start->startOfDay(), $today->endOfDay()])
            ->whereNotNull('selected_extra_items')
            ->get();

        $extrasAgg = [];
        foreach ($bookingsWithExtras as $b) {
            $payload = $b->selected_extra_items;
            if (is_string($payload)) {
                $arr = json_decode($payload, true);
                if (!is_array($arr)) $arr = [];
            } elseif (is_array($payload)) {
                $arr = $payload;
            } else {
                $arr = [];
            }

            foreach ($arr as $item) {
                $id = Arr::get($item, 'id', null);
                $name = Arr::get($item, 'name', Arr::get($item, 'title', 'Item'));
                $price = (float) (Arr::get($item, 'price', 0) ?? 0);
                $key = $id !== null ? 'id::'.$id : 'name::'.$name;

                if (!isset($extrasAgg[$key])) {
                    $extrasAgg[$key] = ['id'=>$id, 'name'=>$name, 'price'=>$price, 'count'=>0, 'revenue'=>0];
                }
                $extrasAgg[$key]['count'] += 1;
                $extrasAgg[$key]['revenue'] += $price;
            }
        }

        $topExtras = collect($extrasAgg)
            ->sortByDesc(fn($v) => $v['count'])
            ->values()
            ->take(6)
            ->map(fn($v) => ['name'=>$v['name'], 'count'=>$v['count'], 'revenue' => (int)$v['revenue']])
            ->toArray();

        return view('admin.dashboard', [
            'totalBookings' => $totalBookings,
            'bookingsToday' => $bookingsToday,
            'bookingsThisWeek' => $bookingsThisWeek,
            'bookingsThisMonth' => $bookingsThisMonth,
            'revenueToday' => $revenueToday,
            'revenueWeek' => $revenueWeek,
            'revenueMonth' => $revenueMonth,
            'lineChart' => ['labels' => $lineLabels, 'data' => $lineData],
            'barChart'  => ['labels' => $barLabels, 'data' => $barData],
            'pieChart'  => ['labels' => $pieLabels, 'data' => $pieData],
            'topExtras' => $topExtras,
            'recentBookings' => $recentBookings,
        ]);
    }

    /**
     * API: dynamic stats for frontend (JSON)
     * GET /api/dashboard/stats?days=14
     */
    public function apiStats(Request $request)
    {
        $days = (int) $request->query('days', 14);
        $days = max(1, min(1000, $days));
        $today = Carbon::today();
        $start = (clone $today)->subDays($days - 1);

        $totalBookings = Booking::count();
        $bookingsToday = Booking::whereDate('created_at', $today)->count();
        $bookingsThisWeek = Booking::whereBetween('created_at', [(clone $today)->startOfWeek(), (clone $today)->endOfWeek()])->count();
        $bookingsThisMonth = Booking::whereBetween('created_at', [(clone $today)->startOfMonth(), (clone $today)->endOfMonth()])->count();

        $revenueBase = Booking::whereIn('status', ['booked', 'completed']);
        $revenueToday = (float) $revenueBase->whereDate('created_at', $today)->sum('total_price');
        $revenueWeek  = (float) $revenueBase->whereBetween('created_at', [(clone $today)->startOfWeek(), (clone $today)->endOfWeek()])->sum('total_price');
        $revenueMonth = (float) $revenueBase->whereBetween('created_at', [(clone $today)->startOfMonth(), (clone $today)->endOfMonth()])->sum('total_price');

        $counts = Booking::selectRaw('DATE(created_at) as dt, count(*) as cnt')
            ->whereBetween('created_at', [$start->startOfDay(), $today->endOfDay()])
            ->groupBy('dt')
            ->pluck('cnt', 'dt')
            ->toArray();

        $labels = []; $data = [];
        for ($i = 0; $i < $days; $i++) {
            $d = (clone $start)->addDays($i);
            $labels[] = $d->format('d M');
            $data[] = (int) ($counts[$d->toDateString()] ?? 0);
        }

        $packages = Booking::selectRaw('package_name, count(*) as cnt')
            ->whereBetween('created_at', [$start->startOfDay(), $today->endOfDay()])
            ->whereNotNull('package_name')
            ->groupBy('package_name')
            ->orderByDesc('cnt')
            ->limit(8)
            ->get()
            ->toArray();

        $statusCounts = Booking::selectRaw('status, count(*) as cnt')
            ->whereBetween('created_at', [$start->startOfDay(), $today->endOfDay()])
            ->groupBy('status')
            ->pluck('cnt', 'status')
            ->toArray();

        $commonStatuses = ['waiting_payment','pending_verification','booked','completed','cancelled'];
        $pieLabels = []; $pieData = [];
        foreach ($commonStatuses as $s) {
            $pieLabels[] = $s;
            $pieData[] = (int) ($statusCounts[$s] ?? 0);
        }
        foreach (array_diff(array_keys($statusCounts), $commonStatuses) as $s) {
            $pieLabels[] = $s;
            $pieData[] = (int) ($statusCounts[$s] ?? 0);
        }

        $recent = Booking::orderBy('created_at','desc')->whereBetween('created_at', [$start->startOfDay(), $today->endOfDay()])->limit(8)->get();
        $rowsHtml = '';
        foreach ($recent as $b) {
            $rowsHtml .= $this->buildRecentRowHtml($b);
        }

        return response()->json([
            'line' => ['labels' => $labels, 'data' => $data],
            'bar'  => ['labels' => array_map(fn($r) => $r['package_name'] ?? '—', $packages), 'data' => array_map(fn($r) => (int)$r['cnt'], $packages)],
            'pie'  => ['labels' => $pieLabels, 'data' => $pieData],
            'stats' => [
                'totalBookings' => $totalBookings,
                'bookingsToday' => $bookingsToday,
                'bookingsThisWeek' => $bookingsThisWeek,
                'bookingsThisMonth' => $bookingsThisMonth,
                'revenueToday' => $revenueToday,
                'revenueWeek' => $revenueWeek,
                'revenueMonth' => $revenueMonth,
            ],
            'recent_html' => $rowsHtml,
        ]);
    }

    /**
     * Paginated recent rows for "Load more" (GET /api/bookings/recent?page=2)
     */
    public function apiRecent(Request $request)
    {
        $page = max(1, (int)$request->query('page', 1));
        $perPage = 8;
        $offset = ($page -1) * $perPage;

        $rows = Booking::orderBy('created_at','desc')->skip($offset)->take($perPage)->get();
        if ($rows->isEmpty()) return response()->json(['html' => '']);

        $html = '';
        foreach ($rows as $b) {
            $html .= $this->buildRecentRowHtml($b);
        }

        return response()->json(['html' => $html]);
    }

    /**
     * Export CSV (streamed)
     * GET /dashboard/export?days=14
     */
    public function export(Request $request)
    {
        $days = max(1, min(1000, (int)$request->query('days', 14)));
        $today = Carbon::today();
        $start = (clone $today)->subDays($days - 1);

        $query = Booking::whereBetween('created_at', [$start->startOfDay(), $today->endOfDay()])->orderBy('created_at','desc');

        $filename = 'bookings_'.$start->format('Ymd').'_to_'.$today->format('Ymd').'.csv';

        $response = new StreamedResponse(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID','Created At','Contact','WhatsApp','Package','Status','Total']);
            $query->chunk(200, function ($rows) use ($handle) {
                foreach ($rows as $r) {
                    fputcsv($handle, [
                        $r->id,
                        optional($r->created_at)->toDateTimeString(),
                        $r->contact_name,
                        $r->whatsapp_number,
                        $r->package_name,
                        $r->status,
                        $r->total_price,
                    ]);
                }
            });
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'"');

        return $response;
    }

    /**
     * Helper: build <tr> HTML for a booking row
     */
    protected function buildRecentRowHtml(Booking $b): string
    {
        $id = e($b->id);
        $contact = e($b->contact_name ?? optional($b->customer)->name ?? '-');
        $wh = e($b->whatsapp_number ?? '');
        $pkg = e($b->package_name ?? '-');
        $created = e(optional($b->created_at)->format('d M Y H:i') ?? '');
        $total = number_format($b->total_price ?? 0,0,',','.');
        $status = e($b->status ?? 'unknown');

        $badge = match (($b->status ?? '')) {
            'booked' => 'bg-green-100 text-green-700',
            'waiting_payment' => 'bg-yellow-100 text-yellow-800',
            'cancelled' => 'bg-red-100 text-red-700',
            default => 'bg-gray-100 text-gray-700',
        };

        return <<<HTML
<tr class="border-t">
  <td class="py-3 px-3 font-medium">#{$id}</td>
  <td class="py-3 px-3">{$contact}<div class="text-xs text-gray-400">{$wh}</div></td>
  <td class="py-3 px-3">{$pkg}</td>
  <td class="py-3 px-3">{$created}</td>
  <td class="py-3 px-3">Rp{$total}</td>
  <td class="py-3 px-3"><span class="inline-block px-2 py-1 rounded text-xs {$badge}">{$status}</span></td>
</tr>
HTML;
    }
}
