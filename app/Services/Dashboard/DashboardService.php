<?php

namespace App\Services\Dashboard;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Marvel\Database\Models\Category;
use Marvel\Database\Models\Order;
use Marvel\Database\Models\Product;
use Marvel\Database\Models\User;
use Marvel\Enums\OrderStatus;
use Marvel\Enums\Permission;

class DashboardService
{
    public function getOverview(Request $request): array
    {
        $totalRevenue = DB::table('orders as childOrder')
            ->whereDate('childOrder.created_at', '<=', Carbon::now())
            ->where('childOrder.order_status', OrderStatus::COMPLETED)
            ->whereNotNull('childOrder.parent_id')
            ->join('orders as parentOrder', 'childOrder.parent_id', '=', 'parentOrder.id')
            ->whereDate('parentOrder.created_at', '<=', Carbon::now())
            ->where('parentOrder.order_status', OrderStatus::COMPLETED)
            ->select(
                'childOrder.id',
                'childOrder.parent_id',
                'childOrder.paid_total',
                'parentOrder.delivery_fee',
                'parentOrder.sales_tax',
            )
            ->get();

        $totalRevenueSum = $totalRevenue->sum('paid_total')
            + $totalRevenue->unique('parent_id')->sum('delivery_fee')
            + $totalRevenue->unique('parent_id')->sum('sales_tax');

        $todaysRevenue = DB::table('orders as A')
            ->whereDate('A.created_at', '>', Carbon::now()->subDays(1))
            ->where('A.order_status', OrderStatus::COMPLETED)
            ->whereNotNull('A.parent_id')
            ->join('orders as B', 'A.parent_id', '=', 'B.id')
            ->where('B.order_status', OrderStatus::COMPLETED)
            ->select('A.id', 'A.parent_id', 'A.paid_total', 'B.delivery_fee', 'B.sales_tax')
            ->get();

        $todaysRevenueSum = $todaysRevenue->sum('paid_total')
            + $todaysRevenue->unique('parent_id')->sum('delivery_fee')
            + $todaysRevenue->unique('parent_id')->sum('sales_tax');

        $totalRefunds = (float) DB::table('refunds')
            ->whereDate('created_at', '<', Carbon::now())
            ->sum('amount');

        $totalOrders = DB::table('orders')
            ->whereDate('created_at', '<=', Carbon::now())
            ->whereNull('parent_id')
            ->count();

        $totalProducts = Product::count();

        $totalCustomers = User::permission(Permission::CUSTOMER)->count();
        $newCustomers = User::permission(Permission::CUSTOMER)
            ->whereDate('created_at', '>', Carbon::now()->subDays(30))
            ->count();

        return [
            'total_revenue'     => round($totalRevenueSum, 2),
            'todays_revenue'    => round($todaysRevenueSum, 2),
            'total_refunds'     => round($totalRefunds, 2),
            'total_orders'      => $totalOrders,
            'total_products'    => $totalProducts,
            'total_customers'   => $totalCustomers,
            'new_customers'     => $newCustomers,
        ];
    }

    public function getRevenueOverview(Request $request): array
    {
        $totalRevenue = DB::table('orders as childOrder')
            ->whereDate('childOrder.created_at', '<=', Carbon::now())
            ->where('childOrder.order_status', OrderStatus::COMPLETED)
            ->whereNotNull('childOrder.parent_id')
            ->join('orders as parentOrder', 'childOrder.parent_id', '=', 'parentOrder.id')
            ->whereDate('parentOrder.created_at', '<=', Carbon::now())
            ->where('parentOrder.order_status', OrderStatus::COMPLETED)
            ->select(
                'childOrder.id',
                'childOrder.parent_id',
                'childOrder.paid_total',
                'parentOrder.delivery_fee',
                'parentOrder.sales_tax',
            )
            ->get();

        $totalRevenueSum = $totalRevenue->sum('paid_total')
            + $totalRevenue->unique('parent_id')->sum('delivery_fee')
            + $totalRevenue->unique('parent_id')->sum('sales_tax');

        $todaysRevenue = DB::table('orders as A')
            ->whereDate('A.created_at', '>', Carbon::now()->subDays(1))
            ->where('A.order_status', OrderStatus::COMPLETED)
            ->whereNotNull('A.parent_id')
            ->join('orders as B', 'A.parent_id', '=', 'B.id')
            ->where('B.order_status', OrderStatus::COMPLETED)
            ->select('A.id', 'A.parent_id', 'A.paid_total', 'B.delivery_fee', 'B.sales_tax')
            ->get();

        $todaysRevenueSum = $todaysRevenue->sum('paid_total')
            + $todaysRevenue->unique('parent_id')->sum('delivery_fee')
            + $todaysRevenue->unique('parent_id')->sum('sales_tax');

        $months = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December',
        ];

        $salesByMonth = DB::table('orders as A')
            ->where('A.order_status', OrderStatus::COMPLETED)
            ->whereYear('A.created_at', Carbon::now()->year)
            ->whereNull('A.parent_id')
            ->join('orders as B', 'A.id', '=', 'B.parent_id')
            ->where('B.order_status', OrderStatus::COMPLETED)
            ->select(DB::raw("SUM(A.paid_total) as total"), DB::raw("DATE_FORMAT(A.created_at, '%M') as month"))
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $monthlyBreakdown = array_map(fn ($month) => [
            'month' => $month,
            'total' => round($salesByMonth[$month] ?? 0, 2),
        ], $months);

        return [
            'total_revenue'      => round($totalRevenueSum, 2),
            'todays_revenue'     => round($todaysRevenueSum, 2),
            'monthly_breakdown'  => $monthlyBreakdown,
        ];
    }

    public function getOrderStatusOverview(Request $request): array
    {
        $countByDays = function (int $days): array {
            $results = DB::table('orders as A')
                ->whereDate('A.created_at', '>', Carbon::now()->subDays($days))
                ->whereNull('A.parent_id')
                ->select('A.order_status', DB::raw('count(*) as order_count'))
                ->groupBy('A.order_status')
                ->pluck('order_count', 'order_status');

            return [
                'pending'           => $results[OrderStatus::PENDING]           ?? 0,
                'processing'        => $results[OrderStatus::PROCESSING]        ?? 0,
                'completed'         => $results[OrderStatus::COMPLETED]         ?? 0,
                'cancelled'         => $results[OrderStatus::CANCELLED]         ?? 0,
                'refunded'          => $results[OrderStatus::REFUNDED]          ?? 0,
                'failed'            => $results[OrderStatus::FAILED]            ?? 0,
                'local_facility'    => $results[OrderStatus::AT_LOCAL_FACILITY] ?? 0,
                'out_for_delivery'  => $results[OrderStatus::OUT_FOR_DELIVERY]  ?? 0,
            ];
        };

        return [
            'today'   => $countByDays(1),
            'weekly'  => $countByDays(7),
            'monthly' => $countByDays(30),
            'yearly'  => $countByDays(365),
        ];
    }

    public function getRecentOrders(Request $request, int $limit = 10)
    {
        return Order::with(['products', 'user'])
            ->whereNull('parent_id')
            ->take($limit)
            ->get();
    }

    public function getTopSellingProducts(Request $request, int $limit = 10)
    {
        return Product::where('sold_quantity', '>', 0)
            ->orderBy('sold_quantity', 'desc')
            ->take($limit)
            ->get(['id', 'name', 'slug', 'price', 'sold_quantity', 'image']);
    }

    public function getLowStockProducts(Request $request, int $limit = 10)
    {
        return Product::with('type')
            ->where('quantity', '<', 10)
            ->take($limit)
            ->get();
    }

    public function getCategoryStats(Request $request): array
    {
        $language = $request->language ?? DEFAULT_LANGUAGE;

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
                DB::raw('COALESCE(SUM(order_product.order_quantity), 0) as total_sales')
            )
            ->leftJoin('category_product', 'category_product.category_id', '=', 'categories.id')
            ->leftJoin('products', 'category_product.product_id', '=', 'products.id')
            ->leftJoin('order_product', 'order_product.product_id', '=', 'products.id')
            ->leftJoin('orders', 'order_product.order_id', '=', 'orders.id')
            ->where('orders.parent_id', null)
            ->where('orders.order_status', OrderStatus::COMPLETED)
            ->where('categories.language', $language)
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_sales', 'desc')
            ->limit(15)
            ->get();

        return [
            'product_distribution' => $productCounts,
            'sales_distribution'   => $salesData,
        ];
    }
}
