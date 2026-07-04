<?php

namespace App\Services\Dashboard;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Marvel\Database\Models\Category;
use Marvel\Database\Models\Order;
use Marvel\Database\Models\Product;
use Marvel\Database\Models\Shop;
use Marvel\Database\Models\User;
use Marvel\Enums\OrderStatus;
use Marvel\Enums\Permission;
use Marvel\Exceptions\MarvelException;

class DashboardService
{
    /**
     * Get main dashboard overview: revenue, orders, customers, products, shops counts.
     */
    public function getOverview(Request $request): array
    {
        $user = $request->user();
        $shops = $user?->shops->pluck('id') ?? [];
        $isSuperAdmin = $user && $user->hasPermissionTo(Permission::SUPER_ADMIN);

        $totalRevenue = $this->calculateTotalRevenue($user, $shops, $isSuperAdmin);
        $todaysRevenue = $this->calculateTodaysRevenue($user, $shops, $isSuperAdmin);
        $totalRefunds = $this->calculateTotalRefunds($user, $shops, $isSuperAdmin);
        $totalOrders = $this->countTotalOrders($user, $shops, $isSuperAdmin);
        $totalProducts = $this->countTotalProducts($user, $shops, $isSuperAdmin);
        $totalCustomers = User::permission(Permission::CUSTOMER)->count();
        $newCustomers = User::permission(Permission::CUSTOMER)
            ->whereDate('created_at', '>', Carbon::now()->subDays(30))
            ->count();

        if ($isSuperAdmin) {
            $totalShops = Shop::count();
            $totalVendors = User::whereHas('permissions', function ($q) {
                $q->where('name', Permission::STORE_OWNER);
            })->count();
        } else {
            $totalShops = Shop::where('owner_id', $user->id)->count();
            $totalVendors = 0;
        }

        return [
            'total_revenue'     => round($totalRevenue, 2),
            'todays_revenue'    => round($todaysRevenue, 2),
            'total_refunds'     => round($totalRefunds, 2),
            'total_orders'      => $totalOrders,
            'total_products'    => $totalProducts,
            'total_customers'   => $totalCustomers,
            'new_customers'     => $newCustomers,
            'total_shops'       => $totalShops,
            'total_vendors'     => $totalVendors,
        ];
    }

    /**
     * Revenue breakdown: total, today, monthly for current year.
     */
    public function getRevenueOverview(Request $request): array
    {
        $user = $request->user();
        $shops = $user?->shops->pluck('id') ?? [];
        $isSuperAdmin = $user && $user->hasPermissionTo(Permission::SUPER_ADMIN);

        $totalRevenue = $this->calculateTotalRevenue($user, $shops, $isSuperAdmin);
        $todaysRevenue = $this->calculateTodaysRevenue($user, $shops, $isSuperAdmin);

        $monthlyBreakdown = $this->getMonthlyRevenue($user, $isSuperAdmin);

        return [
            'total_revenue'      => round($totalRevenue, 2),
            'todays_revenue'     => round($todaysRevenue, 2),
            'monthly_breakdown'  => $monthlyBreakdown,
        ];
    }

    /**
     * Order status counts for today, weekly, monthly, yearly.
     */
    public function getOrderStatusOverview(Request $request): array
    {
        return [
            'today'   => $this->orderCountByStatus($request, 1),
            'weekly'  => $this->orderCountByStatus($request, 7),
            'monthly' => $this->orderCountByStatus($request, 30),
            'yearly'  => $this->orderCountByStatus($request, 365),
        ];
    }

    /**
     * Recent orders with eager-loaded relations.
     */
    public function getRecentOrders(Request $request, int $limit = 10)
    {
        $user = $request->user();
        $isSuperAdmin = $user && $user->hasPermissionTo(Permission::SUPER_ADMIN);

        $query = Order::with(['products', 'user', 'shop'])
            ->whereNull('parent_id');

        if (!$isSuperAdmin) {
            $shops = $user?->shops->pluck('id') ?? [];
            $query->where(function ($q) use ($user, $shops) {
                $q->whereIn('shop_id', $shops)
                  ->orWhere('customer_id', $user->id);
            });
        }

        return $query->take($limit)->get();
    }

    /**
     * Top selling products by sold quantity.
     */
    public function getTopSellingProducts(Request $request, int $limit = 10)
    {
        $user = $request->user();
        $isSuperAdmin = $user && $user->hasPermissionTo(Permission::SUPER_ADMIN);

        $query = Product::where('sold_quantity', '>', 0);

        if (!$isSuperAdmin) {
            $shops = $user?->shops->pluck('id') ?? [];
            $query->whereIn('shop_id', $shops);
        }

        return $query->orderBy('sold_quantity', 'desc')
            ->take($limit)
            ->get(['id', 'name', 'slug', 'price', 'sold_quantity', 'image', 'shop_id']);
    }

    /**
     * Low stock products (quantity < 10).
     */
    public function getLowStockProducts(Request $request, int $limit = 10)
    {
        $user = $request->user();
        $isSuperAdmin = $user && $user->hasPermissionTo(Permission::SUPER_ADMIN);

        $query = Product::with(['type', 'shop'])
            ->where('quantity', '<', 10);

        if (!$isSuperAdmin) {
            $shops = $user?->shops->pluck('id') ?? [];
            $query->whereIn('shop_id', $shops);
        }

        if ($request->filled('shop_id')) {
            $query->where('shop_id', $request->shop_id);
        }

        return $query->take($limit)->get();
    }

    /**
     * Category-wise product count and sales distribution.
     */
    public function getCategoryStats(Request $request): array
    {
        $user = $request->user();
        $language = $request->language ?? DEFAULT_LANGUAGE;
        $isSuperAdmin = $user && $user->hasPermissionTo(Permission::SUPER_ADMIN);

        $shopFilter = function ($query) use ($user, $isSuperAdmin) {
            if (!$isSuperAdmin) {
                $shops = $user?->shops->pluck('id') ?? [];
                $query->whereIn('products.shop_id', $shops);
            }
        };

        $productCountQuery = DB::table('category_product')
            ->select(
                'categories.id as category_id',
                'categories.name as category_name',
                DB::raw('COUNT(category_product.product_id) as product_count')
            )
            ->join('products', 'category_product.product_id', '=', 'products.id')
            ->join('categories', 'category_product.category_id', '=', 'categories.id')
            ->where('categories.language', $language);

        $shopFilter($productCountQuery);

        $productCounts = $productCountQuery
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('product_count', 'desc')
            ->limit(15)
            ->get();

        $salesQuery = DB::table('categories')
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
            ->where('categories.language', $language);

        $shopFilter($salesQuery);

        $salesData = $salesQuery
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_sales', 'desc')
            ->limit(15)
            ->get();

        return [
            'product_distribution' => $productCounts,
            'sales_distribution'   => $salesData,
        ];
    }

    // --- Private helpers ---

    private function calculateTotalRevenue($user, $shops, bool $isSuperAdmin): float
    {
        $query = DB::table('orders as childOrder')
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
            );

        if ($isSuperAdmin) {
            $results = $query->get();
            return $results->sum('paid_total')
                + $results->unique('parent_id')->sum('delivery_fee')
                + $results->unique('parent_id')->sum('sales_tax');
        }

        return (float) $query->whereIn('childOrder.shop_id', $shops)->sum('paid_total');
    }

    private function calculateTodaysRevenue($user, $shops, bool $isSuperAdmin): float
    {
        $query = DB::table('orders as A')
            ->whereDate('A.created_at', '>', Carbon::now()->subDays(1))
            ->where('A.order_status', OrderStatus::COMPLETED)
            ->whereNotNull('A.parent_id')
            ->join('orders as B', 'A.parent_id', '=', 'B.id')
            ->where('B.order_status', OrderStatus::COMPLETED)
            ->select('A.id', 'A.parent_id', 'A.paid_total', 'B.delivery_fee', 'B.sales_tax');

        if ($isSuperAdmin) {
            $results = $query->get();
            return $results->sum('paid_total')
                + $results->unique('parent_id')->sum('delivery_fee')
                + $results->unique('parent_id')->sum('sales_tax');
        }

        return (float) $query->whereIn('A.shop_id', $shops)->sum('paid_total');
    }

    private function calculateTotalRefunds($user, $shops, bool $isSuperAdmin): float
    {
        $query = DB::table('refunds')->whereDate('created_at', '<', Carbon::now());
        if ($isSuperAdmin) {
            return (float) $query->where('shop_id', null)->sum('amount');
        }
        return (float) $query->whereIn('shop_id', $shops)->sum('amount');
    }

    private function countTotalOrders($user, $shops, bool $isSuperAdmin): int
    {
        $query = DB::table('orders')->whereDate('created_at', '<=', Carbon::now());
        if ($isSuperAdmin) {
            return $query->whereNull('parent_id')->count();
        }
        return $query->whereIn('shop_id', $shops)->count();
    }

    private function countTotalProducts($user, $shops, bool $isSuperAdmin): int
    {
        $query = Product::query();
        if (!$isSuperAdmin) {
            $query->whereIn('shop_id', $shops);
        }
        return $query->count();
    }

    private function getMonthlyRevenue($user, bool $isSuperAdmin): array
    {
        $months = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December',
        ];

        $query = DB::table('orders as A')
            ->where('A.order_status', OrderStatus::COMPLETED)
            ->whereYear('A.created_at', Carbon::now()->year);

        if ($isSuperAdmin) {
            $query->whereNull('A.parent_id')
                ->join('orders as B', 'A.id', '=', 'B.parent_id')
                ->where('B.order_status', OrderStatus::COMPLETED)
                ->select(DB::raw("SUM(A.paid_total) as total"), DB::raw("DATE_FORMAT(A.created_at, '%M') as month"));
        } else {
            $shops = $user?->shops->pluck('id') ?? [];
            $query->whereNotNull('A.parent_id')
                ->whereIn('A.shop_id', $shops)
                ->select(DB::raw("SUM(A.paid_total) as total"), DB::raw("DATE_FORMAT(A.created_at, '%M') as month"));
        }

        $salesByMonth = $query->groupBy('month')->pluck('total', 'month')->toArray();

        return array_map(fn ($month) => [
            'month' => $month,
            'total' => round($salesByMonth[$month] ?? 0, 2),
        ], $months);
    }

    private function orderCountByStatus(Request $request, int $days): array
    {
        $user = $request->user();
        $isSuperAdmin = $user && $user->hasPermissionTo(Permission::SUPER_ADMIN);

        $query = DB::table('orders as A')
            ->whereDate('A.created_at', '>', Carbon::now()->subDays($days))
            ->select('A.order_status', DB::raw('count(*) as order_count'))
            ->groupBy('A.order_status');

        if ($isSuperAdmin) {
            $query->whereNull('A.parent_id');
        } elseif ($user && $user->hasPermissionTo(Permission::STORE_OWNER)) {
            $shops = $user->shops->pluck('id') ?? [];
            $query->whereNotNull('A.parent_id')->whereIn('A.shop_id', $shops);
        } elseif ($user && $user->hasPermissionTo(Permission::STAFF)) {
            $query->whereNotNull('A.parent_id')->where('A.shop_id', $user->shop_id);
        }

        $results = $query->pluck('order_count', 'order_status');

        return [
            'pending'        => $results[OrderStatus::PENDING]           ?? 0,
            'processing'     => $results[OrderStatus::PROCESSING]        ?? 0,
            'completed'      => $results[OrderStatus::COMPLETED]         ?? 0,
            'cancelled'      => $results[OrderStatus::CANCELLED]         ?? 0,
            'refunded'       => $results[OrderStatus::REFUNDED]          ?? 0,
            'failed'         => $results[OrderStatus::FAILED]            ?? 0,
            'local_facility' => $results[OrderStatus::AT_LOCAL_FACILITY] ?? 0,
            'out_for_delivery' => $results[OrderStatus::OUT_FOR_DELIVERY] ?? 0,
        ];
    }
}
