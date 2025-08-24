<?php

namespace App\Http\Controllers\Statistics;

use App\Models\BehaviorLog;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Reservation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StatisticsService
{


    private function get_amount_text($amount,$separator='')
    {
        return rtrim(rtrim(number_format($amount, 2, '.', ','), '0'), '.'). $separator;
    }

    private function total_orders()
    {
        $totalOrders = Order::count();

        return $this->get_amount_text($totalOrders);
    }

    private function total_reservations()
    {
        $totalReservations = Reservation::count();

        return $this->get_amount_text($totalReservations);
    }

    private function total_customers()
    {
        $totalCustomers = Customer::count();

        return $this->get_amount_text($totalCustomers);
    }

    private function monthly_profits()
    {
        // أولاً: اجلب الأرباح الفعلية من قاعدة البيانات
        $rawData = \DB::table('payments')
            ->whereYear('created_at', now()->year)
            ->selectRaw('
                MONTH(created_at) as month,
                SUM(amount) as revenue
            ')
            ->groupBy('month')
            ->pluck('revenue', 'month'); // [7 => 153064]

        // ثانياً: أنشئ جميع الأشهر من 1 إلى 12
        $monthlyProfits = collect(range(1, 12))->map(function ($month) use ($rawData) {
            $revenue = $rawData[$month] ?? 0;

            return [
                'month' => \Carbon\Carbon::create()->month($month)->format('M'), // Jan, Feb, ...
                'revenue' => $this->get_amount_text($revenue, ' $'),
            ];
        });

        return $monthlyProfits;
    }

    private function revenue_distribution()
    {
        $lang = Auth::user()->preferred_language;

        $excludedNames = ['Top Ratings', 'الأعلى تقييما'];

        $revenueByCategory = DB::table('categories')
            ->join('products', 'categories.id', '=', 'products.category_id')
            ->join('cart_items', 'products.id', '=', 'cart_items.product_id')
            ->whereNotIn("categories.name->{$lang}", $excludedNames)
            ->groupBy('categories.id', DB::raw("JSON_UNQUOTE(JSON_EXTRACT(categories.name, '$.\"{$lang}\"'))"))
            ->selectRaw("
                categories.id as category_id,
                JSON_UNQUOTE(JSON_EXTRACT(categories.name, '$.\"{$lang}\"')) as category_name,
                SUM(cart_items.total_price) as revenue
            ")
            ->orderByDesc('revenue')
            ->get();

        $totalRevenue = $revenueByCategory->sum('revenue');


        $revenueByCategoryWithPercentage = $revenueByCategory->map(function ($item) use ($totalRevenue) {
            $item->percentage = round(($item->revenue / $totalRevenue) * 100, 2); // كنسبة مئوية
            return [
                'category_id' => $item->category_id,
                'category_name' => $item->category_name,
                'revenue' => $this->get_amount_text($item->revenue,' $'),
                'percentage' => $item->percentage.' %'
            ];
        });

        return $revenueByCategoryWithPercentage;

    }

    private function daily_revenue():array
    {

        // بيانات ثابتة للأيام
    $dailyRevenue = collect([
        ['date' => 'Sun', 'total' => '100 $'],
        ['date' => 'Mon', 'total' => '150 $'],
        ['date' => 'Tue', 'total' => '200 $'],
        ['date' => 'Wed', 'total' => '50 $'],
        ['date' => 'Thu', 'total' => '300 $'],
        ['date' => 'Fri', 'total' => '400 $'],
        ['date' => 'Sat', 'total' => '250 $'],
    ]);

    // بيانات ثابتة للمقارنة الأسبوعية
    $weeklyComparison = [
        'current_week_total' => 1450,
        'last_week_total' => 1200,
        'change_percent' => '+20%',
    ];

    // إضافة المقارنة الأسبوعية إلى آخر عنصر
    $dailyRevenue->push([
        'weekly_comparison' => $weeklyComparison
    ]);

    return [$dailyRevenue];
        $startOfWeek = now()->startOfWeek(Carbon::SUNDAY);
        $endOfWeek = now()->endOfWeek(Carbon::SATURDAY);

        $rawDaily = DB::table('payments')
            ->selectRaw("DATE(created_at) as date, SUM(amount) as total")
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->groupByRaw("DATE(created_at)")
            ->pluck('total', 'date');


        $dailyRevenue = collect();
        for ($date = $startOfWeek->copy(); $date->lte($endOfWeek); $date->addDay()) {
            $dateString = $date->toDateString();
            $dailyRevenue->push([
                'date' => $date->format('D'), // مثل: Sun, Jul 07
                'total' => $this->get_amount_text($rawDaily[$dateString] ?? 0, ' $'),
            ]);
        }


        $today = Carbon::today();
        $isEndOfWeek = $today->isSaturday();

        if (true) {

            $startThisWeek = $today->copy()->startOfWeek(Carbon::SUNDAY);
            $endThisWeek = $today->copy()->endOfWeek(Carbon::SATURDAY);


            $startLastWeek = $startThisWeek->copy()->subWeek();
            $endLastWeek = $endThisWeek->copy()->subWeek();


            $currentWeekTotal = DB::table('payments')
                ->whereBetween('created_at', [$startThisWeek, $endThisWeek])
                ->sum('amount');


            $lastWeekTotal = DB::table('payments')
                ->whereBetween('created_at', [$startLastWeek, $endLastWeek])
                ->sum('amount');


            $change = 0;
            if ($lastWeekTotal > 0) {
                $change = (($currentWeekTotal - $lastWeekTotal) / $lastWeekTotal) * 100;
            }

            $weeklyComparison = [
                'current_week_total' => round($currentWeekTotal, 2),
                'last_week_total' => round($lastWeekTotal, 2),
                'change_percent' => ($change >= 0 ? '+' : '') . round($change, 2) . '%',
            ];

            $dailyRevenue->push([
                'weekly_comparison'=> $weeklyComparison
            ]);
        }

        return [$dailyRevenue];
    }

    private function returning_vs_new_customers()
    {
        $customersWithOrders = Customer::withCount('orders')->get();

        $newCustomers = $customersWithOrders->where('orders_count', 1)->count();
        $returningCustomers = $customersWithOrders->where('orders_count', '>', 1)->count();
        $totalCustomers = $newCustomers + $returningCustomers;

        $percentageNew = $totalCustomers > 0 ? ($newCustomers / $totalCustomers) * 100 : 0;
        $percentageReturning = $totalCustomers > 0 ? ($returningCustomers / $totalCustomers) * 100 : 0;

            return [
                ['new_customers' => $this->get_amount_text($percentageNew, ' %')
                ,'returning_customers' => $this->get_amount_text($percentageReturning, ' %')],
            ];
    }

    private function average_order_value()
    {
        $totalRevenue = Order::sum('total_amount');
        $totalOrders = Order::count();

        if ($totalOrders > 0)
        {
            $averageOrderValue = $totalRevenue / $totalOrders;
        }
        else
        {
            $averageOrderValue = 0;
        }

        return $this->get_amount_text($averageOrderValue,' $');

    }

    public function no_show_rate()
    {
        $totalReservations = Reservation::count();
        $noShowCount = BehaviorLog::count();

        $noShowRate = $totalReservations > 0
            ? round(($noShowCount / $totalReservations) * 100, 2)
            : 0;

        return $noShowRate.' %';

    }

    public function top_3_products_sales()
    {
        $lang = Auth::user()->preferred_language;

        $topProducts = DB::table('cart_items')
            ->join('products', 'cart_items.product_id', '=', 'products.id')
            ->select(
                'products.id',
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(products.name, '$.\"{$lang}\"')) as product_name"),
                DB::raw('COUNT(cart_items.product_id) as sales_count')
            )
            ->groupBy(
                'products.id',
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(products.name, '$.\"{$lang}\"'))")
            )
            ->orderByDesc('sales_count')
            ->limit(3)
            ->get();

        return $topProducts;

    }


    public function get_statistics():array
    {


        $data = [
            'total_orders' => $this->total_orders(),
            'total_reservations' => $this->total_reservations(),
            'total_customers' => $this->total_customers(),
            'average_order_value'=> $this->average_order_value(),
            'no_show_rate' => $this->no_show_rate(),
            'returning_vs_new_customers' => $this->returning_vs_new_customers(),
            'monthly_profits' => $this->monthly_profits(),
            'revenue_distribution' => $this->revenue_distribution(),
            'daily_revenue' => $this->daily_revenue(),
            'top_3_products_sales' => $this->top_3_products_sales()
        ];

        $message ='';
        $code = 200;


        return ['data' =>$data,'message'=>$message,'code'=>$code];

    }
}
