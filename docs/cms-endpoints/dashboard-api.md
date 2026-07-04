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
| **Verified Email** | `email.verified` |
| **Allowed Roles** | `super_admin`, `store_owner`, `staff` |
| **Rate Limit** | None (internal analytics queries) |

### Access Scoping

- **Super Admin:** Sees platform-wide data (all shops, all products, all orders)
- **Store Owner:** Sees data scoped to their owned shops
- **Staff:** Sees data scoped to their assigned shop

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
    "message": "Error description",
    "errors": {}
}
```

---

## Endpoints

### 1. GET /dashboard/overview — Main Dashboard Summary

**Purpose:** Returns key KPI metrics for the dashboard overview cards in a single request.

**Method:** `GET`

**URL:** `/dashboard/overview`

**Authentication:** Required (`auth:sanctum`)
**Role:** `super_admin | store_owner | staff`

**Query Parameters:** None

**Business Logic:**
1. Total revenue: SUM of `paid_total` from child orders with `order_status = completed`, joined with parent orders for delivery fee + sales tax
2. Today's revenue: Same logic filtered to last 24 hours
3. Total refunds: SUM of `amount` from `refunds` table (shop-scoped for non-super-admin)
4. Total orders: COUNT of parent orders (`parent_id IS NULL`) for super admin, or COUNT of shop-scoped orders for others
5. Total products: COUNT of products (shop-scoped for non-super-admin)
6. Total customers: COUNT of users with `customer` permission
7. New customers: COUNT of customers created in last 30 days
8. Total shops: COUNT of all shops (super admin only), or owner's shops (store_owner/staff)
9. Total vendors: COUNT of users with `store_owner` permission (super admin only)

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
        "new_customers": 45,
        "total_shops": 12,
        "total_vendors": 8
    }
}
```

**Error Responses:**
| Status | Condition |
|--------|-----------|
| 401 | Unauthenticated |
| 403 | Missing required role |

---

### 2. GET /dashboard/revenue — Revenue Analytics

**Purpose:** Revenue breakdown including total all-time revenue, today's revenue, and monthly breakdown for the current year.

**Method:** `GET`

**URL:** `/dashboard/revenue`

**Authentication:** Required (`auth:sanctum`)
**Role:** `super_admin | store_owner | staff`

**Query Parameters:** None

**Business Logic:**
1. Total revenue: All-time completed orders revenue (same calculation as overview)
2. Today's revenue: Last 24 hours completed orders
3. Monthly breakdown: 12 entries (January–December) for current year, each with month name and total

**Super Admin Revenue Calculation:**
- Total = SUM(child_orders.paid_total) + SUM(DISTINCT parent_order.delivery_fee) + SUM(DISTINCT parent_order.sales_tax)
- Monthly uses parent orders for super admin

**Store Owner/Staff Revenue Calculation:**
- Total = SUM(child_orders.paid_total) where shop_id IN (owned shops)
- Monthly uses child orders for non-super-admin

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

**Error Responses:**
| Status | Condition |
|--------|-----------|
| 401 | Unauthenticated |
| 403 | Missing required role |

---

### 3. GET /dashboard/order-stats — Order Status Distribution

**Purpose:** Order counts grouped by status for today, weekly (7 days), monthly (30 days), and yearly (365 days) time ranges.

**Method:** `GET`

**URL:** `/dashboard/order-stats`

**Authentication:** Required (`auth:sanctum`)
**Role:** `super_admin | store_owner | staff`

**Query Parameters:** None

**Tracked Statuses:**
| Status | Description |
|--------|-------------|
| `pending` | Awaiting processing |
| `processing` | Being processed |
| `completed` | Successfully completed |
| `cancelled` | Order cancelled |
| `refunded` | Order refunded |
| `failed` | Payment/processing failed |
| `local_facility` | At local facility (shipping) |
| `out_for_delivery` | Out for delivery |

**Business Logic:**
- Super admin: Queries on parent orders (`parent_id IS NULL`)
- Store owner: Queries on child orders (`parent_id IS NOT NULL`) filtered by owned shop IDs
- Staff: Queries on child orders (`parent_id IS NOT NULL`) filtered by assigned shop_id

**Success Response (200):**
```json
{
    "success": true,
    "message": "Order statistics fetched successfully",
    "data": {
        "today": {
            "pending": 5,
            "processing": 3,
            "completed": 12,
            "cancelled": 1,
            "refunded": 0,
            "failed": 0,
            "local_facility": 2,
            "out_for_delivery": 4
        },
        "weekly": {
            "pending": 15,
            "processing": 22,
            "completed": 85,
            "cancelled": 3,
            "refunded": 1,
            "failed": 2,
            "local_facility": 10,
            "out_for_delivery": 18
        },
        "monthly": {
            "pending": 45,
            "processing": 60,
            "completed": 350,
            "cancelled": 8,
            "refunded": 4,
            "failed": 6,
            "local_facility": 25,
            "out_for_delivery": 40
        },
        "yearly": {
            "pending": 120,
            "processing": 200,
            "completed": 1500,
            "cancelled": 30,
            "refunded": 15,
            "failed": 20,
            "local_facility": 80,
            "out_for_delivery": 150
        }
    }
}
```

**Error Responses:**
| Status | Condition |
|--------|-----------|
| 401 | Unauthenticated |
| 403 | Missing required role |

---

### 4. GET /dashboard/recent-orders — Recent Orders

**Purpose:** Fetch the latest orders with eager-loaded relations (products, user, shop). Supports configurable limit.

**Method:** `GET`

**URL:** `/dashboard/recent-orders`

**Authentication:** Required (`auth:sanctum`)
**Role:** `super_admin | store_owner | staff`

**Query Parameters:**
| Field | Type | Default | Max | Description |
|-------|------|---------|-----|-------------|
| `limit` | int | 10 | 50 | Number of recent orders to return |

**Business Logic:**
1. Queries orders where `parent_id IS NULL` (top-level orders)
2. Eager loads `products`, `user`, `shop` relations
3. Super admin: No shop filter (all orders)
4. Store owner/Staff: Filters by owned/assigned shop IDs OR customer_id matching the user
5. Orders by `created_at` descending (implicit via `take()` — latest records)
6. Limit capped at 50

**Success Response (200):**
```json
{
    "success": true,
    "message": "Recent orders fetched successfully",
    "data": [
        {
            "id": 1,
            "tracking_number": "20240704-ABCD-1234",
            "order_status": "completed",
            "paid_total": 250.00,
            "amount": 250.00,
            "delivery_fee": 15.00,
            "sales_tax": 20.00,
            "created_at": "2024-07-04T10:30:00.000000Z",
            "products": [
                {
                    "id": 15,
                    "name": "Product Name",
                    "price": 125.00,
                    "pivot": { "order_quantity": 2 }
                }
            ],
            "user": {
                "id": 5,
                "name": "John Doe",
                "email": "john@example.com"
            },
            "shop": {
                "id": 1,
                "name": "Main Shop",
                "slug": "main-shop"
            }
        }
    ]
}
```

**Error Responses:**
| Status | Condition |
|--------|-----------|
| 401 | Unauthenticated |
| 403 | Missing required role |

---

### 5. GET /dashboard/top-products — Top Selling Products

**Purpose:** Products ranked by `sold_quantity` descending. Supports configurable limit.

**Method:** `GET`

**URL:** `/dashboard/top-products`

**Authentication:** Required (`auth:sanctum`)
**Role:** `super_admin | store_owner | staff`

**Query Parameters:**
| Field | Type | Default | Max | Description |
|-------|------|---------|-----|-------------|
| `limit` | int | 10 | 50 | Number of top products to return |

**Business Logic:**
1. Filters products where `sold_quantity > 0`
2. Orders by `sold_quantity` descending
3. Super admin: No shop filter (all products across all shops)
4. Store owner/Staff: Filters by owned/assigned shop IDs
5. Returns selected columns: `id`, `name`, `slug`, `price`, `sold_quantity`, `image`, `shop_id`
6. Limit capped at 50

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
            "sold_quantity": 450,
            "image": "https://cdn.example.com/storage/products/15/image.jpg",
            "shop_id": 1
        },
        {
            "id": 22,
            "name": "Second Best Product",
            "slug": "second-best-product",
            "price": 75.00,
            "sold_quantity": 320,
            "image": "https://cdn.example.com/storage/products/22/image.jpg",
            "shop_id": 1
        }
    ]
}
```

**Error Responses:**
| Status | Condition |
|--------|-----------|
| 401 | Unauthenticated |
| 403 | Missing required role |

---

### 6. GET /dashboard/category-stats — Category Distribution

**Purpose:** Category-wise product count distribution and sales distribution. Returns product count per category and total completed sales per category.

**Method:** `GET`

**URL:** `/dashboard/category-stats`

**Authentication:** Required (`auth:sanctum`)
**Role:** `super_admin | store_owner | staff`

**Query Parameters:**
| Field | Type | Default | Description |
|-------|------|---------|-------------|
| `language` | string | `en` | Language filter for categories |

**Business Logic:**
1. **Product Distribution:** Query on `category_product` pivot joined with `products` and `categories` tables, grouped by `categories.id` and `categories.name`, filtered by language
2. **Sales Distribution:** Query on `categories` table joined with `category_product` → `products` → `order_product` → `orders`, filtered by `order_status = completed` and `parent_id IS NULL`, grouped by `categories.id` and `categories.name`, filtered by language
3. Both queries limited to 15 categories, ordered descending
4. Super admin: No shop filter (all products/sales)
5. Store owner/Staff: Filters by owned/assigned shop IDs on the products join

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

**Error Responses:**
| Status | Condition |
|--------|-----------|
| 401 | Unauthenticated |
| 403 | Missing required role |

---

### 7. GET /dashboard/low-stock — Low Stock Products

**Purpose:** Products with quantity below 10 units. Supports shop filtering and configurable limit.

**Method:** `GET`

**URL:** `/dashboard/low-stock`

**Authentication:** Required (`auth:sanctum`)
**Role:** `super_admin | store_owner | staff`

**Query Parameters:**
| Field | Type | Default | Max | Description |
|-------|------|---------|-----|-------------|
| `limit` | int | 10 | 50 | Number of low stock products to return |
| `shop_id` | int | — | — | Filter by specific shop |

**Business Logic:**
1. Queries products with `quantity < 10`
2. Eager loads `type` and `shop` relations
3. Super admin: No implicit shop filter (all shops)
4. Store owner/Staff: Filters by owned/assigned shop IDs
5. If `shop_id` param provided, additionally filters by that specific shop
6. Limit capped at 50

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
            "image": "https://cdn.example.com/storage/products/15/image.jpg",
            "shop_id": 1,
            "type": {
                "id": 1,
                "name": "Physical"
            },
            "shop": {
                "id": 1,
                "name": "Main Shop",
                "slug": "main-shop"
            }
        }
    ]
}
```

**Error Responses:**
| Status | Condition |
|--------|-----------|
| 401 | Unauthenticated |
| 403 | Missing required role |

---

## Database Impact

| Endpoint | Tables | Query Type |
|----------|--------|------------|
| `/dashboard/overview` | `orders`, `refunds`, `products`, `users`, `shops` | Multiple aggregate queries |
| `/dashboard/revenue` | `orders` | Aggregate with date grouping |
| `/dashboard/order-stats` | `orders` | Aggregate with status grouping + date filtering |
| `/dashboard/recent-orders` | `orders`, `order_product`, `products`, `users`, `shops` | Select with joins |
| `/dashboard/top-products` | `products` | Select with sort + limit |
| `/dashboard/category-stats` | `categories`, `category_product`, `products`, `order_product`, `orders` | Aggregate with joins |
| `/dashboard/low-stock` | `products`, `types`, `shops` | Select with filters |

---

## Dependencies

| Class | Type | File |
|-------|------|------|
| `DashboardController` | Controller | `app/Http/Controllers/Api/DashboardController.php` |
| `DashboardService` | Service | `app/Services/Dashboard/DashboardService.php` |
| `OrderStatus` | Enum | `packages/marvel/src/Enums/OrderStatus.php` |
| `Permission` | Enum | `packages/marvel/src/Enums/Permission.php` |
| `Order` | Model | `packages/marvel/src/Database/Models/Order.php` |
| `Product` | Model | `packages/marvel/src/Database/Models/Product.php` |
| `Category` | Model | `packages/marvel/src/Database/Models/Category.php` |
| `Shop` | Model | `packages/marvel/src/Database/Models/Shop.php` |
| `User` | Model | `packages/marvel/src/Database/Models/User.php` |

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

Source: `packages/marvel/src/Rest/Routes.php` (inside super_admin middleware group)

---

## Notes

- All dashboard queries run against **real database data only** — no mock data, no caching (for now)
- Revenue calculations include `paid_total` from child orders + `delivery_fee` + `sales_tax` from parent orders (for super admin)
- For non-super-admin users, revenue only includes `paid_total` (no delivery fee or tax cut)
- Empty states return `0` for numeric fields or empty arrays for collections
- All endpoints use `GET` method — no data mutation
