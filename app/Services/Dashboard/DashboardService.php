<?php

namespace App\Services\Dashboard;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Marvel\Database\Models\Category;
use Marvel\Database\Models\Order;
use Marvel\Database\Models\Product;
use Marvel\Database\Models\User;
use Marvel\Enums\Permission;

class DashboardService
{
    public function getOverview(Request $request): array
    {
        return Cache::remember('dashboard_overview', 300, function () {
            $totalRevenue = (float) Order::where('status', 'completed')
                ->whereDate('created_at', '<=', Carbon::now())
                ->sum('total_price');

            $todaysRevenue = (float) Order::where('status', 'completed')
                ->whereDate('created_at', '>', Carbon::now()->subDays(1))
                ->sum('total_price');

            $totalRefunds = (float) DB::table('refunds')
                ->whereDate('created_at', '<', Carbon::now())
                ->sum('amount');

            $totalOrders = Order::whereDate('created_at', '<=', Carbon::now())->count();

            $totalProducts = Product::count();

            $totalCustomers = User::where('type', 'user')->count();
            $newCustomers = User::where('type', 'user')
                ->whereDate('created_at', '>', Carbon::now()->subDays(30))
                ->count();

            return [
                'total_revenue'     => round($totalRevenue, 2),
                'todays_revenue'    => round($todaysRevenue, 2),
                'total_refunds'     => round($totalRefunds, 2),
                'total_orders'      => $totalOrders,
                'total_products'    => $totalProducts,
                'total_customers'   => $totalCustomers,
                'new_customers'     => $newCustomers,
            ];
        });
    }

    public function getRevenueOverview(Request $request): array
    {
        return Cache::remember('dashboard_revenue', 300, function () {
            $totalRevenue = (float) Order::where('status', 'completed')
                ->whereDate('created_at', '<=', Carbon::now())
                ->sum('total_price');

            $todaysRevenue = (float) Order::where('status', 'completed')
                ->whereDate('created_at', '>', Carbon::now()->subDays(1))
                ->sum('total_price');

            $months = [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December',
            ];

            $salesByMonth = Order::select(
                    DB::raw("SUM(total_price) as total"),
                    DB::raw("DATE_FORMAT(created_at, '%M') as month")
                )
                ->where('status', 'completed')
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->pluck('total', 'month')
                ->toArray();

            $monthlyBreakdown = array_map(fn ($month) => [
                'month' => $month,
                'total' => round((float) ($salesByMonth[$month] ?? 0), 2),
            ], $months);

            return [
                'total_revenue'      => round($totalRevenue, 2),
                'todays_revenue'     => round($todaysRevenue, 2),
                'monthly_breakdown'  => $monthlyBreakdown,
            ];
        });
    }

    public function getOrderStatusOverview(Request $request): array
    {
        return Cache::remember('dashboard_order_stats', 300, function () {
            $countByDays = function (int $days): array {
                $results = Order::select('status', DB::raw('count(*) as order_count'))
                    ->whereDate('created_at', '>', Carbon::now()->subDays($days))
                    ->groupBy('status')
                    ->pluck('order_count', 'status');

                return [
                    'pending'           => (int) ($results['pending'] ?? 0),
                    'processing'        => 0,
                    'completed'         => (int) ($results['completed'] ?? 0),
                    'cancelled'         => (int) ($results['cancelled'] ?? 0),
                    'refunded'          => 0,
                    'failed'            => 0,
                    'local_facility'    => 0,
                    'out_for_delivery'  => 0,
                ];
            };

            return [
                'today'   => $countByDays(1),
                'weekly'  => $countByDays(7),
                'monthly' => $countByDays(30),
                'yearly'  => $countByDays(365),
            ];
        });
    }

    public function getRecentOrders(Request $request, int $limit = 10)
    {
        return Cache::remember("dashboard_recent_orders_{$limit}", 300, function () use ($limit) {
            return Order::with(['products', 'user'])
                ->take($limit)
                ->get();
        });
    }

    public function getTopSellingProducts(Request $request, int $limit = 10)
    {
        return Cache::remember("dashboard_top_products_{$limit}", 300, function () use ($limit) {
            return Product::where('sold_quantity', '>', 0)
                ->orderBy('sold_quantity', 'desc')
                ->take($limit)
                ->get(['id', 'name', 'slug', 'price', 'sold_quantity']);
        });
    }

    public function getLowStockProducts(Request $request, int $limit = 10)
    {
        return Cache::remember("dashboard_low_stock_{$limit}", 300, function () use ($limit) {
            return Product::with('type')
                ->where('quantity', '<', 10)
                ->take($limit)
                ->get();
        });
    }

    public function getCategoryStats(Request $request): array
    {
        $language = $request->language ?? DEFAULT_LANGUAGE;

        return Cache::remember("dashboard_category_stats_{$language}", 300, function () use ($language) {
            $productCounts = DB::table('category_product')
                ->select(
                    'categories.id as category_id',
                    'categories.name as category_name',
                    DB::raw('COUNT(category_product.product_id) as product_count')
                )
                ->join('products', 'category_product.product_id', '=', 'products.id')
                ->join('categories', 'category_product.category_id', '=', 'categories.id')
                ->where('categories.language', $language)
                ->groupBy('categories.id', 'categories.name')
                ->orderBy('product_count', 'desc')
                ->limit(15)
                ->get();

            $salesData = DB::table('categories')
                ->select(
                    'categories.id as category_id',
                    'categories.name as category_name',
                    DB::raw('COALESCE(SUM(order_product.product_quantity), 0) as total_sales')
                )
                ->leftJoin('category_product', 'category_product.category_id', '=', 'categories.id')
                ->leftJoin('products', 'category_product.product_id', '=', 'products.id')
                ->leftJoin('order_product', 'order_product.product_id', '=', 'products.id')
                ->leftJoin('orders', 'order_product.order_id', '=', 'orders.id')
                ->where('orders.status', 'completed')
                ->where('categories.language', $language)
                ->groupBy('categories.id', 'categories.name')
                ->orderBy('total_sales', 'desc')
                ->limit(15)
                ->get();

            return [
                'product_distribution' => $productCounts,
                'sales_distribution'   => $salesData,
            ];
        });
    }
}
