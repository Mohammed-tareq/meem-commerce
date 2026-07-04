<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    /**
     * GET /api/v1/dashboard/overview
     *
     * Main dashboard summary with key metrics:
     * - total_revenue, todays_revenue, total_refunds
     * - total_orders, total_products, total_customers, new_customers
     * - total_shops, total_vendors
     *
     * Access: Super Admin (platform-wide), Store Owner/Staff (shop-scoped)
     * Middleware: auth:sanctum, role:super_admin|store_owner|staff
     */
    public function overview(Request $request): JsonResponse
    {
        $data = $this->dashboardService->getOverview($request);

        return response()->json([
            'success' => true,
            'message' => __('Dashboard overview fetched successfully'),
            'data'    => $data,
        ]);
    }

    /**
     * GET /api/v1/dashboard/revenue
     *
     * Revenue analytics breakdown:
     * - total_revenue (all-time completed orders)
     * - todays_revenue (last 24 hours)
     * - monthly_breakdown (current year, by month)
     *
     * Access: Super Admin (platform-wide), Store Owner/Staff (shop-scoped)
     * Middleware: auth:sanctum, role:super_admin|store_owner|staff
     */
    public function revenue(Request $request): JsonResponse
    {
        $data = $this->dashboardService->getRevenueOverview($request);

        return response()->json([
            'success' => true,
            'message' => __('Revenue data fetched successfully'),
            'data'    => $data,
        ]);
    }

    /**
     * GET /api/v1/dashboard/order-stats
     *
     * Order status breakdown for multiple time ranges:
     * - today, weekly (7 days), monthly (30 days), yearly (365 days)
     * Each range returns counts by: pending, processing, completed,
     * cancelled, refunded, failed, local_facility, out_for_delivery
     *
     * Access: Super Admin (platform-wide), Store Owner/Staff (shop-scoped)
     * Middleware: auth:sanctum, role:super_admin|store_owner|staff
     */
    public function orderStats(Request $request): JsonResponse
    {
        $data = $this->dashboardService->getOrderStatusOverview($request);

        return response()->json([
            'success' => true,
            'message' => __('Order statistics fetched successfully'),
            'data'    => $data,
        ]);
    }

    /**
     * GET /api/v1/dashboard/recent-orders
     *
     * Latest orders with eager-loaded relations (products, user, shop).
     * Supports ?limit=N query param (default: 10, max: 50).
     *
     * Access: Super Admin (all orders), Store Owner/Staff (shop-scoped)
     * Middleware: auth:sanctum, role:super_admin|store_owner|staff
     */
    public function recentOrders(Request $request): JsonResponse
    {
        $limit = min((int) ($request->limit ?? 10), 50);
        $orders = $this->dashboardService->getRecentOrders($request, $limit);

        return response()->json([
            'success' => true,
            'message' => __('Recent orders fetched successfully'),
            'data'    => $orders,
        ]);
    }

    /**
     * GET /api/v1/dashboard/top-products
     *
     * Top selling products ranked by sold_quantity descending.
     * Supports ?limit=N query param (default: 10, max: 50).
     *
     * Access: Super Admin (all products), Store Owner/Staff (shop-scoped)
     * Middleware: auth:sanctum, role:super_admin|store_owner|staff
     */
    public function topProducts(Request $request): JsonResponse
    {
        $limit = min((int) ($request->limit ?? 10), 50);
        $products = $this->dashboardService->getTopSellingProducts($request, $limit);

        return response()->json([
            'success' => true,
            'message' => __('Top selling products fetched successfully'),
            'data'    => $products,
        ]);
    }

    /**
     * GET /api/v1/dashboard/category-stats
     *
     * Category distribution analytics:
     * - product_distribution: product count per category
     * - sales_distribution: sales volume per category
     *
     * Supports ?language=en query param.
     * Access: Super Admin (all), Store Owner/Staff (shop-scoped)
     * Middleware: auth:sanctum, role:super_admin|store_owner|staff
     */
    public function categoryStats(Request $request): JsonResponse
    {
        $data = $this->dashboardService->getCategoryStats($request);

        return response()->json([
            'success' => true,
            'message' => __('Category statistics fetched successfully'),
            'data'    => $data,
        ]);
    }

    /**
     * GET /api/v1/dashboard/low-stock
     *
     * Products with quantity below 10 units.
     * Supports ?limit=N and ?shop_id=X query params.
     *
     * Access: Super Admin (all), Store Owner/Staff (shop-scoped)
     * Middleware: auth:sanctum, role:super_admin|store_owner|staff
     */
    public function lowStock(Request $request): JsonResponse
    {
        $limit = min((int) ($request->limit ?? 10), 50);
        $products = $this->dashboardService->getLowStockProducts($request, $limit);

        return response()->json([
            'success' => true,
            'message' => __('Low stock products fetched successfully'),
            'data'    => $products,
        ]);
    }
}
