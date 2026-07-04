<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\DashboardService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    public function overview(Request $request): JsonResponse
    {
        try {
            $data = $this->dashboardService->getOverview($request);

            return response()->json([
                'success' => true,
                'message' => __('Dashboard overview fetched successfully'),
                'data'    => $data,
            ]);
        } catch (Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    public function revenue(Request $request): JsonResponse
    {
        try {
            $data = $this->dashboardService->getRevenueOverview($request);

            return response()->json([
                'success' => true,
                'message' => __('Revenue data fetched successfully'),
                'data'    => $data,
            ]);
        } catch (Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    public function orderStats(Request $request): JsonResponse
    {
        try {
            $data = $this->dashboardService->getOrderStatusOverview($request);

            return response()->json([
                'success' => true,
                'message' => __('Order statistics fetched successfully'),
                'data'    => $data,
            ]);
        } catch (Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    public function recentOrders(Request $request): JsonResponse
    {
        try {
            $limit = min((int) ($request->limit ?? 10), 50);
            $orders = $this->dashboardService->getRecentOrders($request, $limit);

            return response()->json([
                'success' => true,
                'message' => __('Recent orders fetched successfully'),
                'data'    => $orders,
            ]);
        } catch (Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    public function topProducts(Request $request): JsonResponse
    {
        try {
            $limit = min((int) ($request->limit ?? 10), 50);
            $products = $this->dashboardService->getTopSellingProducts($request, $limit);

            return response()->json([
                'success' => true,
                'message' => __('Top selling products fetched successfully'),
                'data'    => $products,
            ]);
        } catch (Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    public function categoryStats(Request $request): JsonResponse
    {
        try {
            $data = $this->dashboardService->getCategoryStats($request);

            return response()->json([
                'success' => true,
                'message' => __('Category statistics fetched successfully'),
                'data'    => $data,
            ]);
        } catch (Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    public function lowStock(Request $request): JsonResponse
    {
        try {
            $limit = min((int) ($request->limit ?? 10), 50);
            $products = $this->dashboardService->getLowStockProducts($request, $limit);

            return response()->json([
                'success' => true,
                'message' => __('Low stock products fetched successfully'),
                'data'    => $products,
            ]);
        } catch (Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    protected function errorResponse(Throwable $e): JsonResponse
    {
        if ($e instanceof QueryException) {
            $message = __('Database error occurred. Please check your request and try again.');
        } else {
            $message = $e->getMessage();
        }

        return response()->json([
            'success' => false,
            'message' => $message,
        ], 409);
    }
}
