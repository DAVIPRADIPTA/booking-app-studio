<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class DashboardController extends Controller
{
    public function dashboard()
    {
        // Ranges
        $today = Carbon::today();
        $startOfWeek = (clone $today)->startOfWeek();
        $endOfWeek = (clone $today)->endOfWeek();
        $startOfMonth = (clone $today)->startOfMonth();
        $endOfMonth = (clone $today)->endOfMonth();
        $start30 = (clone $today)->subDays(29); // last 30 days inclusive

        // Basic counts
        $totalBookings = Booking::count();

        // NOTE: using created_at as "booking activity". 
        // If you prefer scheduled date -> use whereDate('booking_date', ...) instead.
        $bookingsToday = Booking::whereDate('created_at', $today)->count();
        $bookingsThisWeek = Booking::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();
        $bookingsThisMonth = Booking::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();

        // Revenue: consider only bookings that we consider "counted" (booked/completed)
        $revenueQuery = Booking::whereIn('status', ['booked', 'completed']);

        $revenueToday = (float) $revenueQuery->whereDate('created_at', $today)->sum('total_price');
        $revenueWeek = (float) $revenueQuery->whereBetween('created_at', [$startOfWeek, $endOfWeek])->sum('total_price');
        $revenueMonth = (float) $revenueQuery->whereBetween('created_at', [$startOfMonth, $endOfMonth])->sum('total_price');

        // Recent bookings (latest 8)
        $recentBookings = Booking::with([])->orderBy('created_at', 'desc')->limit(8)->get();

        // LINE CHART: bookings per day last 30 days (created_at)
        $labels = [];
        $lineData = [];
        $period = collect();

        for ($d = 0; $d < 30; $d++) {
            $dt = (clone $start30)->addDays($d);
            $labels[] = $dt->format('d M'); // e.g. "01 Sep"
            $period->push($dt->toDateString());
        }

        // get counts grouped by date to reduce queries
        $countsByDate = Booking::selectRaw("DATE(created_at) as dt, count(*) as cnt")
            ->whereBetween('created_at', [$start30->startOfDay(), $today->endOfDay()])
            ->groupBy('dt')
            ->pluck('cnt', 'dt')
            ->toArray();

        foreach ($period as $date) {
            $lineData[] = (int) ($countsByDate[$date] ?? 0);
        }

        $lineChart = [
            'labels' => $labels,
            'data' => $lineData,
        ];

        // BAR CHART: top packages in last 30 days
        $packages = Booking::selectRaw('package_name, count(*) as cnt')
            ->whereBetween('created_at', [$start30->startOfDay(), $today->endOfDay()])
            ->whereNotNull('package_name')
            ->groupBy('package_name')
            ->orderByDesc('cnt')
            ->limit(8)
            ->get()
            ->toArray();

        $barChart = [
            'labels' => array_map(function ($r) { return $r['package_name'] ?? '—'; }, $packages),
            'data' => array_map(function ($r) { return (int)$r['cnt']; }, $packages),
        ];

        // PIE CHART: status distribution (all time)
        $statusCounts = Booking::selectRaw('status, count(*) as cnt')
            ->groupBy('status')
            ->pluck('cnt', 'status')
            ->toArray();

        // ensure consistent order and zero defaults for common statuses
        $commonStatuses = ['waiting_payment','pending_verification','booked','completed','cancelled'];
        $pieLabels = [];
        $pieData = [];
        foreach ($commonStatuses as $s) {
            $pieLabels[] = $s;
            $pieData[] = (int) ($statusCounts[$s] ?? 0);
        }
        // plus any extra statuses
        $extraStatuses = array_diff(array_keys($statusCounts), $commonStatuses);
        foreach ($extraStatuses as $s) {
            $pieLabels[] = $s;
            $pieData[] = (int) ($statusCounts[$s] ?? 0);
        }

        $pieChart = [
            'labels' => $pieLabels,
            'data' => $pieData,
        ];

        // TOP EXTRAS: last 30 days — parse selected_extra_items JSON payload
        // selected_extra_items assumed stored as array of {id, name, price}
        $bookingsWithExtras = Booking::select('selected_extra_items')
            ->whereBetween('created_at', [$start30->startOfDay(), $today->endOfDay()])
            ->whereNotNull('selected_extra_items')
            ->get();

        $extrasAgg = [];
        foreach ($bookingsWithExtras as $b) {
            $payload = $b->selected_extra_items;
            // payload might already be array (cast) or JSON string
            if (is_string($payload)) {
                $arr = json_decode($payload, true);
                if (!is_array($arr)) $arr = [];
            } elseif (is_array($payload)) {
                $arr = $payload;
            } else {
                $arr = [];
            }

            foreach ($arr as $item) {
                // handle cases where item is id or object
                $id = Arr::get($item, 'id', null) ?? Arr::get($item, '0', null);
                $name = Arr::get($item, 'name', 'Item');
                $price = (float) (Arr::get($item, 'price', 0) ?? 0);

                if ($id === null) {
                    // fallback: use name as key
                    $key = 'name::' . $name;
                } else {
                    $key = 'id::' . $id;
                }

                if (!isset($extrasAgg[$key])) {
                    $extrasAgg[$key] = [
                        'id' => $id,
                        'name' => $name,
                        'price' => $price,
                        'count' => 0,
                        'revenue' => 0,
                    ];
                }

                $extrasAgg[$key]['count'] += 1;
                $extrasAgg[$key]['revenue'] += $price;
            }
        }

        // sort by count desc and take top 6
        $topExtras = collect($extrasAgg)
            ->sortByDesc(fn($v) => $v['count'])
            ->values()
            ->take(6)
            ->map(function ($v) {
                // normalize revenue to int
                $v['revenue'] = (int) $v['revenue'];
                return $v;
            })
            ->toArray();

        // compact data for view
        return view('admin.dashboard', compact(
            'totalBookings',
            'bookingsToday',
            'bookingsThisWeek',
            'bookingsThisMonth',
            'revenueToday',
            'revenueWeek',
            'revenueMonth',
            'lineChart',
            'barChart',
            'pieChart',
            'topExtras',
            'recentBookings'
        ));
    }
}
