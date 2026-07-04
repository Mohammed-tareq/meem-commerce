# Dashboard API

## Overview

The Dashboard module provides analytics and summary endpoints for admin panels. It serves 7 endpoints covering KPI overview, revenue analytics, order status distribution, recent orders, top-selling products, category statistics, and low-stock alerts.

**Version:** 1.0
**Base URL:** `/api/v1/dashboard`

---

## Authentication & Authorization

| Aspect | Requirement |
|--------|-------------|
| **Authentication** | `auth:sanctum` |
| **Verified Email** | `verified` |
| **Role** | Any authenticated user (platform-wide data) |

---

## Response Envelope

All endpoints return:

```json
{
    "success": true,
    "message": "Translated message string",
    "data": {}
}
```

Error Response:

```json
{
    "success": false,
    "message": "Error description"
}
```

---

## Endpoints

### 1. GET /dashboard/overview — Main Dashboard Summary

**Purpose:** Returns key KPI metrics for the dashboard overview cards in a single request.

**URL:** `/dashboard/overview`

**Query Parameters:** None

**Business Logic:**
1. Total revenue: SUM of `total_price` from orders with `status = completed`
2. Today's revenue: Same filtered to last 24 hours
3. Total refunds: SUM of `amount` from `refunds` table
4. Total orders: COUNT of all orders
5. Total products: COUNT of all products
6. Total customers: COUNT of users with `customer` permission
7. New customers: COUNT of customers created in last 30 days

**Success Response (200):**
```json
{
    "success": true,
    "message": "Dashboard overview fetched successfully",
    "data": {
        "total_revenue": 152340.50,
        "todays_revenue": 2340.00,
        "total_refunds": 1200.00,
        "total_orders": 1850,
        "total_products": 3420,
        "total_customers": 890,
        "new_customers": 45
    }
}
```

---

### 2. GET /dashboard/revenue — Revenue Analytics

**Purpose:** Revenue breakdown including total all-time revenue, today's revenue, and monthly breakdown for the current year.

**URL:** `/dashboard/revenue`

**Query Parameters:** None

**Business Logic:**
1. Total revenue: SUM of `total_price` from completed orders
2. Today's revenue: Last 24 hours completed orders
3. Monthly breakdown: 12 entries (January–December) for current year, each with month name and total

**Success Response (200):**
```json
{
    "success": true,
    "message": "Revenue data fetched successfully",
    "data": {
        "total_revenue": 152340.50,
        "todays_revenue": 2340.00,
        "monthly_breakdown": [
            { "month": "January", "total": 12500.00 },
            { "month": "February", "total": 14200.00 },
            { "month": "March", "total": 13100.00 },
            { "month": "April", "total": 15800.00 },
            { "month": "May", "total": 16200.00 },
            { "month": "June", "total": 14500.00 },
            { "month": "July", "total": 0 },
            { "month": "August", "total": 0 },
            { "month": "September", "total": 0 },
            { "month": "October", "total": 0 },
            { "month": "November", "total": 0 },
            { "month": "December", "total": 0 }
        ]
    }
}
```

---

### 3. GET /dashboard/order-stats — Order Status Distribution

**Purpose:** Order counts grouped by status for today, weekly (7 days), monthly (30 days), and yearly (365 days) time ranges.

**URL:** `/dashboard/order-stats`

**Query Parameters:** None

**Business Logic:**
- Groups orders by `status` column values: `pending`, `completed`, `delivered`, `cancelled`
- Returns 0 for statuses not present in DB (`processing`, `refunded`, `failed`, `local_facility`, `out_for_delivery`)

**Success Response (200):**
```json
{
    "success": true,
    "message": "Order statistics fetched successfully",
    "data": {
        "today": {
            "pending": 5,
            "processing": 0,
            "completed": 12,
            "cancelled": 1,
            "refunded": 0,
            "failed": 0,
            "local_facility": 0,
            "out_for_delivery": 0
        },
        "weekly": {
            "pending": 15,
            "processing": 0,
            "completed": 85,
            "cancelled": 3,
            "refunded": 0,
            "failed": 0,
            "local_facility": 0,
            "out_for_delivery": 0
        },
        "monthly": {
            "pending": 45,
            "processing": 0,
            "completed": 350,
            "cancelled": 8,
            "refunded": 0,
            "failed": 0,
            "local_facility": 0,
            "out_for_delivery": 0
        },
        "yearly": {
            "pending": 120,
            "processing": 0,
            "completed": 1500,
            "cancelled": 30,
            "refunded": 0,
            "failed": 0,
            "local_facility": 0,
            "out_for_delivery": 0
        }
    }
}
```

---

### 4. GET /dashboard/recent-orders — Recent Orders

**Purpose:** Fetch the latest orders with eager-loaded relations (products, user).

**URL:** `/dashboard/recent-orders`

**Query Parameters:**
| Field | Type | Default | Max | Description |
|-------|------|---------|-----|-------------|
| `limit` | int | 10 | 50 | Number of recent orders to return |

**Business Logic:**
1. Eager loads `products` and `user` relations
2. Limit capped at 50

**Success Response (200):**
```json
{
    "success": true,
    "message": "Recent orders fetched successfully",
    "data": [
        {
            "id": 1,
            "name": "Order #1",
            "status": "completed",
            "total_price": 250.00,
            "created_at": "2024-07-04T10:30:00.000000Z",
            "products": [
                {
                    "id": 15,
                    "name": "Product Name",
                    "pivot": { "product_quantity": 2 }
                }
            ],
            "user": {
                "id": 5,
                "name": "John Doe",
                "email": "john@example.com"
            }
        }
    ]
}
```

---

### 5. GET /dashboard/top-products — Top Selling Products

**Purpose:** Products ranked by `sold_quantity` descending.

**URL:** `/dashboard/top-products`

**Query Parameters:**
| Field | Type | Default | Max | Description |
|-------|------|---------|-----|-------------|
| `limit` | int | 10 | 50 | Number of top products to return |

**Business Logic:**
1. Filters products where `sold_quantity > 0`
2. Orders by `sold_quantity` descending
3. Limit capped at 50

**Success Response (200):**
```json
{
    "success": true,
    "message": "Top selling products fetched successfully",
    "data": [
        {
            "id": 15,
            "name": "Best Selling Product",
            "slug": "best-selling-product",
            "price": 125.00,
            "sold_quantity": 450
        },
        {
            "id": 22,
            "name": "Second Best Product",
            "slug": "second-best-product",
            "price": 75.00,
            "sold_quantity": 320
        }
    ]
}
```

---

### 6. GET /dashboard/category-stats — Category Distribution

**Purpose:** Category-wise product count distribution and sales distribution.

**URL:** `/dashboard/category-stats`

**Query Parameters:**
| Field | Type | Default | Description |
|-------|------|---------|-------------|
| `language` | string | `en` | Language filter for categories |

**Business Logic:**
1. **Product Distribution:** Count of products per category via `category_product` pivot
2. **Sales Distribution:** SUM of `product_quantity` from completed orders per category
3. Both queries limited to 15 categories, ordered descending

**Success Response (200):**
```json
{
    "success": true,
    "message": "Category statistics fetched successfully",
    "data": {
        "product_distribution": [
            {
                "category_id": 1,
                "category_name": "Fruits & Vegetables",
                "product_count": 45
            },
            {
                "category_id": 2,
                "category_name": "Dairy & Eggs",
                "product_count": 30
            }
        ],
        "sales_distribution": [
            {
                "category_id": 1,
                "category_name": "Fruits & Vegetables",
                "total_sales": 12500
            },
            {
                "category_id": 2,
                "category_name": "Dairy & Eggs",
                "total_sales": 8900
            }
        ]
    }
}
```

---

### 7. GET /dashboard/low-stock — Low Stock Products

**Purpose:** Products with quantity below 10 units.

**URL:** `/dashboard/low-stock`

**Query Parameters:**
| Field | Type | Default | Max | Description |
|-------|------|---------|-----|-------------|
| `limit` | int | 10 | 50 | Number of low stock products to return |

**Business Logic:**
1. Filters products with `quantity < 10`
2. Eager loads `type` relation
3. Limit capped at 50

**Success Response (200):**
```json
{
    "success": true,
    "message": "Low stock products fetched successfully",
    "data": [
        {
            "id": 15,
            "name": "Running Low Product",
            "slug": "running-low-product",
            "quantity": 3,
            "price": 25.00,
            "type": {
                "id": 1,
                "name": "Physical"
            }
        }
    ]
}
```

---

## Error Responses

| Status | Condition |
|--------|-----------|
| 401 | Unauthenticated |
| 409 | Database query error |
| 500 | Internal server error |

---

## Database Impact

| Endpoint | Tables | Query Type |
|----------|--------|------------|
| `/dashboard/overview` | `orders`, `refunds`, `products`, `users` | Multiple aggregate queries |
| `/dashboard/revenue` | `orders` | Aggregate with date grouping |
| `/dashboard/order-stats` | `orders` | Aggregate with status grouping + date filtering |
| `/dashboard/recent-orders` | `orders`, `order_products`, `products`, `users` | Select with eager loads |
| `/dashboard/top-products` | `products` | Select with sort + limit |
| `/dashboard/category-stats` | `categories`, `category_product`, `products`, `order_products`, `orders` | Aggregate with joins |
| `/dashboard/low-stock` | `products`, `types` | Select with filters |

---

## Dependencies

| Class | Type | File |
|-------|------|------|
| `DashboardController` | Controller | `app/Http/Controllers/Api/DashboardController.php` |
| `DashboardService` | Service | `app/Services/Dashboard/DashboardService.php` |
| `Permission` | Enum | `packages/marvel/src/Enums/Permission.php` |
| `Order` | Model | `packages/marvel/src/Database/Models/Order.php` |
| `Product` | Model | `packages/marvel/src/Database/Models/Product.php` |
| `Category` | Model | `packages/marvel/src/Database/Models/Category.php` |
| `User` | Model | `packages/marvel/src/Database/Models/User.php` |

---

## Route Definitions

```php
Route::prefix('dashboard')->group(function () {
    Route::get('overview', [DashboardController::class, 'overview']);
    Route::get('revenue', [DashboardController::class, 'revenue']);
    Route::get('order-stats', [DashboardController::class, 'orderStats']);
    Route::get('recent-orders', [DashboardController::class, 'recentOrders']);
    Route::get('top-products', [DashboardController::class, 'topProducts']);
    Route::get('category-stats', [DashboardController::class, 'categoryStats']);
    Route::get('low-stock', [DashboardController::class, 'lowStock']);
});
```

Source: `packages/marvel/src/Rest/Routes.php` (inside `auth:sanctum`, `verified` middleware group)

---

## Notes

- All dashboard queries run against real database data only
- Revenue calculated as SUM of `total_price` from orders with `status = completed`
- Empty states return `0` for numeric fields or empty arrays for collections
- All endpoints use `GET` method — no data mutation
- Database errors return 409 with a generic error message
