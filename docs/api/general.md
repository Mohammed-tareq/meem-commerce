# General API Documentation

**Base URL:** `/api/v1/general`

**Common Response Format (success):**
```json
{
    "status": 200,
    "message": "translated_message",
    "success": true,
    "data": { ... }
}
```

**Common Response Format (error):**
```json
{
    "status": 404,
    "message": "translated_message",
    "success": false
}
```

> **Note:** The `data` key is omitted from the response when empty. All translated fields use `app()->getLocale()`. All monetary values return `null` if null/empty in DB, otherwise rounded to 2 decimals.

---

## 1. GET `/api/v1/general/categories-with-children`

### Navigation categories tree

**Auth:** None

**URL Example:**
```
/api/v1/general/categories-with-children
/api/v1/general/categories-with-children?level=2
```

**Query Parameters:**

| Param | Type | Required | Description |
|-------|------|----------|-------------|
| `level` | int | No | Max nesting depth (default: loads all 3 levels: parent > child > grandchild) |

**Response:** Array of `CategoryNavbarResource`

| Field | Type | Can Be Null | Notes |
|-------|------|-------------|-------|
| `id` | int | No | |
| `name` | string | No | Translated |
| `slug` | string | No | |
| `level` | int | No | 0 = parent, 1 = child, 2 = grandchild |
| `image.desktop` | string | Yes | null if no media uploaded |
| `image.mobile` | string | Yes | null if no media uploaded |
| `children` | array | No | Empty array `[]` if level >= 2 or no children |

**Example Response:**
```json
{
    "status": 200,
    "message": "Data fetched successfully",
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Electronics",
            "slug": "electronics",
            "level": 0,
            "image": {
                "desktop": "https://storage.url/categories/1/desktop.jpg",
                "mobile": "https://storage.url/categories/1/mobile.jpg"
            },
            "children": [
                {
                    "id": 2,
                    "name": "Mobiles",
                    "slug": "mobiles",
                    "level": 1,
                    "image": {
                        "desktop": "https://storage.url/categories/2/desktop.jpg",
                        "mobile": "https://storage.url/categories/2/mobile.jpg"
                    },
                    "children": []
                }
            ]
        }
    ]
}
```

**DB Tables:** `categories`

---

## 2. GET `/api/v1/general/categories`

### Paginated category list

**Auth:** None

**URL Example:**
```
/api/v1/general/categories
/api/v1/general/categories?search=phone&limit=20&page=2
/api/v1/general/categories?slug=electronics    ← redirects to single by slug
```

**Query Parameters:**

| Param | Type | Required | Description |
|-------|------|----------|-------------|
| `slug` | string | No | If provided, returns single category instead of list |
| `search` | string | No | Search by name (translated) |
| `parent` | int | No | Filter by parent category ID |
| `limit` | int | No | Per page (1-100, default 15) |
| `page` | int | No | Page number |

**Response:** Paginated `CategoryHomeResource`

| Field | Type | Can Be Null | Notes |
|-------|------|-------------|-------|
| `id` | int | No | |
| `name` | string | No | Translated |
| `slug` | string | No | |
| `image.desktop` | string | Yes | null if no media |
| `image.mobile` | string | Yes | null if no media |
| `products_count` | int | No | 0 if no products |
| `details` | string | Yes | **null** if no translation exists for current locale |

**DB Tables:** `categories`, `category_product` (pivot for count)

---

## 3. GET `/api/v1/general/categories/{slug}`

### Single category with children and products

**Auth:** None

**URL Example:**
```
/api/v1/general/categories/mobiles
```

**URL Parameters:**

| Param | Type | Required | Description |
|-------|------|----------|-------------|
| `slug` | string | Yes | Category slug |

**Response:** `CategoryWithChildResource`

| Field | Type | Can Be Null | Notes |
|-------|------|-------------|-------|
| `id` | int | No | |
| `name` | string | No | Translated |
| `slug` | string | No | |
| `image.desktop` | string | Yes | null if no media |
| `image.mobile` | string | Yes | null if no media |
| `products_count` | int | No | |
| `details` | string | Yes | **null** if no translation |
| `children` | array | No | **Omitted** if no children (key not present) |
| `products` | array | No | **Omitted** if no products (key not present) |

**Error:** 404 if slug not found.

**DB Tables:** `categories`, `category_product`, `products`

---

## 4. GET `/api/v1/general/products`

### Product listing with filtering, search, strategy-based display

**Auth:** None

**URL Example:**
```
/api/v1/general/products
/api/v1/general/products?type=new_arrivals&limit=10
/api/v1/general/products?category=electronics&price_min=10&price_max=500&order=desc&order_price=asc
/api/v1/general/products?search=iphone&brand=apple&rating_min=4
/api/v1/general/products?productsId=1,2,3
/api/v1/general/products?categoriesId=5,6&brandsId=2
/api/v1/general/products?type=best_product_sales&limit=5
```

**Query Parameters:**

| Param | Type | Required | Description |
|-------|------|----------|-------------|
| `type` | string | No | Strategy type. Default: `index`. Options: `index`, `best_product_sales`, `new_arrivals`, `all_product_discounts`, `product_discount_today_or_low_qty`, `flash_sales_product`, `flash_sales_end_today`, `product_for_parent_category`, `flash_sales_end_week`, `brands_product` |
| `search` | string | No | Full-text search (uses Meilisearch if configured, otherwise LIKE query on name/description) |
| `limit` | int | No | Per page (1-100, default 15) |
| `page` | int | No | Page number |
| `order` | string | No | `asc` or `desc` by id (default `desc`) |
| `order_price` | string | No | `asc` or `desc` by price |
| `category` | string | No | Filter by category slug |
| `brand` | string | No | Filter by brand slug |
| `price_min` | float | No | Minimum price |
| `price_max` | float | No | Maximum price |
| `productsId` | string | No | Comma-separated product IDs |
| `categoriesId` | string | No | Comma-separated category IDs |
| `brandsId` | string | No | Comma-separated brand IDs |
| `promotionsId` | string | No | Comma-separated promotion IDs |
| `flashSalesId` | string | No | Comma-separated flash sale IDs |
| `bannersId` | string | No | Comma-separated banner IDs |
| `couponsId` | string | No | Comma-separated coupon IDs |
| `slidersId` | string | No | Comma-separated slider IDs |
| `rating` | int | No | Exact rating filter |
| `rating_min` | int | No | Minimum rating |
| `rating_max` | int | No | Maximum rating |
| `height` | float | No | Filter by exact height |
| `width` | float | No | Filter by exact width |
| `length` | float | No | Filter by exact length |
| `weight` | float | No | Filter by exact weight |

**Response:** Paginated `ProductMiniResource` + filters + categories

**Each product in `data[]`:**

| Field | Type | Can Be Null | Notes |
|-------|------|-------------|-------|
| `id` | int | No | |
| `name` | string | No | Translated |
| `slug` | string | No | |
| `price` | float | Yes | **null** if no price in DB |
| `has_variants` | bool | No | `true` if product_type !== 'simple' |
| `current_price` | float | Yes | **null** if no discount/flash sale |
| `price_after_discount` | float | Yes | **null** if no discount |
| `price_after_flash_sale` | float | Yes | **null** if no flash sale |
| `has_discount` | bool | No | |
| `discount_type` | string | Yes | **null** if no discount |
| `discount_amount` | float | Yes | **null** if no discount |
| `height` | float | Yes | **null** if not set |
| `width` | float | Yes | **null** if not set |
| `length` | float | Yes | **null** if not set |
| `weight` | float | Yes | **null** if not set |
| `quantity` | int | No | 0 if out of stock |
| `discount_valid` | bool | No | `false` if discount expired |
| `ratings` | float | No | 0 if no reviews |
| `image.thumbnail` | string | Yes | **null** if no media |
| `image.original` | array | No | Empty array `[]` if no additional images |

**Top-level `filters`:** Dynamic filter object with brands, categories, price range, dimensions, ratings.

**Top-level `categories`:** Array of sub-categories that have products in the result set. Each: `{ id, name, slug, image: { desktop, mobile } }`

**DB Tables:** `products`, `reviews`, `categories`, `category_product`, `brands`, `flash_sales`, `flash_sale_products`, `attribute_product`, `product_variants`

---

## 5. GET `/api/v1/general/products/{slug}`

### Single product detail

**Auth:** None

**URL Example:**
```
/api/v1/general/products/iphone-15-pro
/api/v1/general/products/iphone-15-pro?limit=5
```

**URL Parameters:**

| Param | Type | Required | Description |
|-------|------|----------|-------------|
| `slug` | string | Yes | Product slug |

**Query Parameters:**

| Param | Type | Required | Description |
|-------|------|----------|-------------|
| `limit` | int | No | Number of related products (default 10) |

**Response:** `ProductResource`

| Field | Type | Can Be Null | Notes |
|-------|------|-------------|-------|
| `id` | int | No | |
| `name` | string | No | Translated |
| `slug` | string | No | |
| `description` | string | Yes | **null** if no translation |
| `price` | float | Yes | **null** if not set |
| `current_price` | float | Yes | **null** if no discount/flash sale |
| `price_after_discount` | float | Yes | **null** if `has_discount` is false |
| `price_after_flash_sale` | float | Yes | **null** if `has_flash_sale` is false |
| `discount_type` | string | Yes | **null** if no discount. Values: `percentage`, `fixed` |
| `discount_amount` | float | Yes | **null** if no discount |
| `start_date` | string | Yes | **null** if no discount date set |
| `end_date` | string | Yes | **null** if no discount date set |
| `sku` | string | Yes | **null** if no SKU |
| `quantity` | int | No | |
| `sold_quantity` | int | No | 0 if never sold |
| `in_stock` | bool | Yes | **null** if not set in DB |
| `status` | bool | No | |
| `product_type` | string | Yes | **null** if not set. Values: `simple`, `variable` |
| `height` | float | Yes | **null** if not set |
| `width` | float | Yes | **null** if not set |
| `length` | float | Yes | **null** if not set |
| `weight` | float | Yes | **null** if not set |
| `has_flash_sale` | bool | No | |
| `has_discount` | bool | No | |
| `discount_valid` | bool | No | Only present when `has_discount` is true |
| `images.thumbnail` | string | Yes | **null** if no media |
| `images.original` | array | No | Empty `[]` if no extra images |
| `variants` | array | No | **Empty array `[]`** if product type is `simple`. Array of variant objects if `variable` |
| `reviews` | array | No | Empty `[]` if no reviews |
| `related_products` | array | No | **Omitted** if not eager loaded. Array of `ProductMiniResource` |

**Variant object:**

| Field | Type | Can Be Null | Notes |
|-------|------|-------------|-------|
| `id` | int | No | |
| `price` | float | Yes | **null** if not set |
| `current_price` | float | Yes | **null** if no discount |
| `quantity` | int | No | |
| `height` | float | Yes | **null** |
| `width` | float | Yes | **null** |
| `length` | float | Yes | **null** |
| `weight` | float | Yes | **null** |
| `attributes` | array | No | Each: `{ attribute_name: "Color", value: "Red" }`. Empty if no attributes |

**Review object:**

| Field | Type | Can Be Null | Notes |
|-------|------|-------------|-------|
| `id` | int | No | |
| `rating` | int | No | 1-5 |
| `comment` | string | Yes | **null** if no comment |
| `user` | object | Yes | **null** if user relation not loaded |
| `images` | array | No | Empty `[]` if no images |

**Error:** 404 if slug not found.

**DB Tables:** `products`, `product_variants`, `attribute_product`, `attribute_values`, `attributes`, `reviews`, `media`

---

## 6. POST `/api/v1/general/products/{id}/reviews`

### Add product review

**Auth:** Required (`auth:sanctum`)

**URL Example:**
```
POST /api/v1/general/products/5/reviews
```

**URL Parameters:**

| Param | Type | Required | Description |
|-------|------|----------|-------------|
| `id` | int | Yes | Product ID |

**Request Body** (`multipart/form-data` or JSON):

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `rating` | int | Yes | 1 to 5 |
| `comment` | string | Yes | Review text |
| `images` | file[] | No | Array of image files (max TBD) |

**URL Example with params:**
```
POST /api/v1/general/products/5/reviews
Content-Type: application/json
Body: { "rating": 4, "comment": "Great product!" }
```

**Response:**
```json
{
    "status": 200,
    "message": "Review created successfully",
    "success": true
}
```

**Error:** 404 if product not found.

**DB Tables:** `reviews`, `media`

---

## 7. PUT `/api/v1/general/products/reviews/{id}`

### Update product review

**Auth:** Required (`auth:sanctum`) — Only the review author

**URL Example:**
```
PUT /api/v1/general/products/reviews/10
Content-Type: application/json
Body: { "rating": 5, "comment": "Updated review!" }
```

**URL Parameters:**

| Param | Type | Required | Description |
|-------|------|----------|-------------|
| `id` | int | Yes | Review ID |

**Request Body** (`multipart/form-data` or JSON):

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `rating` | int | No | 1 to 5 |
| `comment` | string | No | Review text |
| `images` | file[] | No | Array of image files |

**Response:**
```json
{
    "status": 200,
    "message": "Review updated successfully",
    "success": true
}
```

**Error:** 404 if review not found or not owned by user.

**DB Tables:** `reviews`, `media`

---

## 8. GET `/api/v1/general/orders`

### List authenticated user's orders

**Auth:** Required (`auth:sanctum`, `check-email`)

**URL Example:**
```
/api/v1/general/orders
/api/v1/general/orders?limit=20&page=1
```

**Query Parameters:**

| Param | Type | Required | Description |
|-------|------|----------|-------------|
| `limit` | int | No | Per page (1-100, default 15) |
| `page` | int | No | Page number |

**Response:** `OrderCollection` (paginated)

**OrderResource:**

| Field | Type | Can Be Null | Notes |
|-------|------|-------------|-------|
| `id` | int | No | |
| `order_number` | string | Yes | **null** if no order number set |
| `status` | string | No | e.g. `pending`, `completed`, `cancelled` |
| `subtotal` | float | Yes | **null** if no price |
| `discount` | float | No | 0 if no discount (coupon + promotion combined) |
| `total` | float | Yes | **null** if no total_price |
| `promotion` | object | Yes | **null** if no promotion applied |
| `created_at` | string | Yes | **null** if created_at not set. ISO8601 format |
| `order_items` | array | No | Empty `[]` if no items |

**promotion object (when not null):**
```json
{ "id": 1, "type": "fixed", "code": "PROMO10" }
```

**OrderItemResource:**

| Field | Type | Can Be Null | Notes |
|-------|------|-------------|-------|
| `id` | int | No | |
| `quantity` | int | No | |
| `unit_price` | float | Yes | **null** if not set |
| `total_price` | float | Yes | **null** if not set |
| `promotion_discount_amount` | float | Yes | **null** if none |
| `is_gift` | bool | No | |
| `promotion_id` | int | Yes | **null** if no promotion on item |
| `product` | object | No | Full `ProductMiniResource` if loaded, otherwise fallback `{ id, name, sku }` |
| `variant` | object | Yes | **null** if product_variant_id is null |

**DB Tables:** `orders`, `order_items`, `products`, `product_variants`, `transactions`, `coupons`, `promotions`

---

## 9. GET `/api/v1/general/checkout/promotions`

### Get eligible promotions for current cart

**Auth:** Required (`auth:sanctum`, `check-email`)

**URL Example:**
```
/api/v1/general/checkout/promotions
```

**No query parameters.**

**Response:** Array of eligible promotions with matched items, discount amounts, gift options (if applicable).

**Error:** 400 if no active cart found.

**DB Tables:** `carts`, `cart_items`, `promotions`, `products`, `product_variants`

---

## 10. POST `/api/v1/general/checkout`

### Create order (checkout with MyFatoorah payment)

**Auth:** Required (`auth:sanctum`, `check-email`)

**URL Example:**
```
POST /api/v1/general/checkout
Content-Type: application/json
Body: {
    "name": "Ahmed Ali",
    "user_phone": "01012345678",
    "user_email": "ahmed@example.com",
    "address": "Cairo, Egypt",
    "notes": "Leave at door",
    "selected_promotion_id": 1,
    "selected_gift_product_id": null
}
```

**Request Body:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `name` | string | Yes | Customer name |
| `user_phone` | string | Yes | Customer phone |
| `user_email` | string | Yes | Customer email |
| `address` | string | Yes | Shipping address |
| `notes` | string | No | Order notes |
| `selected_promotion_id` | int | No | Promotion ID to apply |
| `selected_gift_product_id` | int | No | Gift product variant ID (if promotion is gift type) |

**Flow:**
1. Validates cart exists with active reservation
2. Calculates invoice price (subtotal - promotion discount - coupon discount)
3. Creates MyFatoorah invoice
4. Creates order + order items in DB
5. Creates transaction record

**Response:**
```json
{
    "status": 200,
    "message": "Checkout successful",
    "success": true,
    "data": {
        "url": "https://myfatoorah.com/InvoicePay?InvoiceId=12345"
    }
}
```

**Errors:**
| Status | Message | Cause |
|--------|---------|-------|
| 400 | Cart not found | No active cart for user |
| 400 | (dynamic) | Cart reservation expired or invalid |
| 422 | (dynamic) | Invalid price calculation |
| 500 | Failed to create order | DB error |
| 500 | Error creating invoice | MyFatoorah API error |
| 500 | Error adding items to order | DB error |
| 500 | Error creating transaction | DB error |

**DB Tables:** `orders`, `order_items`, `transactions`, `carts`, `cart_items`, `coupon_usages`

---

## 11. GET `/api/v1/general/checkout/callback`

### MyFatoorah payment callback (success URL)

**Auth:** None

**URL Example:**
```
/api/v1/general/checkout/callback?paymentId=10000001
```

**Query Parameters:**

| Param | Type | Required | Description |
|-------|------|----------|-------------|
| `paymentId` | string | Yes | MyFatoorah payment ID |

**Flow:**
1. Verifies payment with MyFatoorah API
2. If `Paid` → updates order to `completed`, finalizes cart (deducts inventory)
3. If not `Paid` → updates order to `cancelled`, releases cart inventory

**Response:** HTTP redirect to:
- Success: `{frontend_url}/{lang}/payment/success?status=success&payment_id=X&order_id=Y`
- Failure: `{frontend_url}/{lang}/payment/failed?status=failed&message=ERROR&payment_id=X`

**DB Tables:** `orders`, `carts`, `cart_items`, `transactions`

---

## 12. GET `/api/v1/general/checkout/error`

### MyFatoorah payment error callback

**Auth:** None

**URL Example:**
```
/api/v1/general/checkout/error?paymentId=10000001
```

**Query Parameters:**

| Param | Type | Required | Description |
|-------|------|----------|-------------|
| `paymentId` | string | Yes | MyFatoorah payment ID |

**Flow:**
1. Checks payment status with MyFatoorah
2. If not paid → cancels order, releases cart

**Response:** Redirects to `{frontend_url}/{lang}/payment/failed?status=failed&error=MSG&payment_id=X`

**DB Tables:** `orders`, `carts`

---

## 13. GET `/api/v1/general/fast-shipping/status`

### Check if fast shipping is available and get settings

**Auth:** None

**URL Example:**
```
/api/v1/general/fast-shipping/status
```

**Response:** Object with:
- `enabled` (bool) — whether fast shipping is active globally
- `fee` (float) — delivery fee, **null** if not set
- `working_hours` — operating hours configuration
- `governorates` — eligible governorate IDs

**DB Tables:** Settings, `governorates`

---

## 14. GET `/api/v1/general/fast-shipping/products`

### Products available for fast shipping

**Auth:** None

**URL Example:**
```
/api/v1/general/fast-shipping/products
/api/v1/general/fast-shipping/products?search=phone&limit=20&page=2
```

**Query Parameters:**

| Param | Type | Required | Description |
|-------|------|----------|-------------|
| `search` | string | No | Search by name/description |
| `limit` | int | No | Per page (1-100, default 15) |
| `page` | int | No | Page number |

**Response:** Paginated products (not wrapped in resource, raw product data with categories, variations, avg rating, review count).

**DB Tables:** `products`, `categories`, `product_variants`, `reviews`

---

## 15. POST `/api/v1/general/checkout/fast`

### Fast shipping checkout

**Auth:** Required (`auth:sanctum`)

**URL Example:**
```
POST /api/v1/general/checkout/fast
Content-Type: application/json
Body: {
    "name": "Ahmed Ali",
    "user_phone": "01012345678",
    "user_email": "ahmed@example.com",
    "address": "Cairo, Egypt",
    "notes": "Near the mosque",
    "governorate_id": 1,
    "selected_promotion_id": 2,
    "selected_gift_product_id": null
}
```

**Request Body:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `name` | string | Yes | Customer name |
| `user_phone` | string | Yes | Customer phone |
| `user_email` | string | Yes | Customer email |
| `address` | string | Yes | Shipping address |
| `notes` | string | No | Order notes |
| `governorate_id` | int | Yes | Governorate ID for delivery validation |
| `selected_promotion_id` | int | No | Promotion ID |
| `selected_gift_product_id` | int | No | Gift product variant ID |

**Flow:**
1. Validates cart
2. Validates governorate eligibility for fast shipping
3. Calculates totals with promotion + coupon + fast shipping fee
4. Calculates ETA
5. Creates order with `shipping_method = 'fast'`
6. Creates MyFatoorah invoice
7. Creates transaction

**Response:**
```json
{
    "status": 200,
    "message": "Checkout successful",
    "success": true,
    "data": { "url": "https://myfatoorah.com/..." }
}
```

**Errors:** 400 (no cart), 422 (invalid governorate or validation), 500 (order/invoice/transaction).

**DB Tables:** `orders`, `order_items`, `transactions`, `carts`, `governorates`

---

## 16. GET `/api/v1/general/fast-shipping/orders`

### Fast shipping orders for authenticated user

**Auth:** Required (`auth:sanctum`)

**URL Example:**
```
/api/v1/general/fast-shipping/orders
/api/v1/general/fast-shipping/orders?limit=10&page=1
```

**Query Parameters:**

| Param | Type | Required | Description |
|-------|------|----------|-------------|
| `limit` | int | No | Per page (1-100, default 15) |
| `page` | int | No | Page number |

**Response:** Paginated orders with `shipping_method = 'fast'`, with order items, product media, variants, attributes.

**DB Tables:** `orders`, `order_items`, `products`, `product_variants`

---

## 17. GET `/api/v1/general/sliders`

### Slider list

**Auth:** None

**URL Example:**
```
/api/v1/general/sliders
/api/v1/general/sliders?slug=home-main    ← redirects to single
```

**Query Parameters:**

| Param | Type | Required | Description |
|-------|------|----------|-------------|
| `slug` | string | No | If provided, returns single slider |

**Response:** Collection of `SliderResource`

| Field | Type | Can Be Null | Notes |
|-------|------|-------------|-------|
| `id` | int | No | |
| `title` | string | Yes | **null** if no translation |
| `slug` | string | No | |
| `status` | bool | No | |
| `image.desktop` | string | Yes | **null** if no media |
| `image.mobile` | string | Yes | **null** if no media |
| `products` | array | No | **Empty `[]`** if not loaded or no products |

**DB Tables:** `sliders`, `slider_product`

---

## 18. GET `/api/v1/general/sliders/{slug}`

### Single slider

**Auth:** None

**URL Example:**
```
/api/v1/general/sliders/home-main
```

**URL Parameters:**

| Param | Type | Required | Description |
|-------|------|----------|-------------|
| `slug` | string | Yes | Slider slug |

**Response:** Single `SliderResource` (same structure as above)

**Error:** 404

---

## 19. GET `/api/v1/general/flash-sales`

### Flash sale list

**Auth:** None

**URL Example:**
```
/api/v1/general/flash-sales
/api/v1/general/flash-sales?slug=summer-sale
/api/v1/general/flash-sales?limit=5&page=1
```

**Query Parameters:**

| Param | Type | Required | Description |
|-------|------|----------|-------------|
| `slug` | string | No | If provided, returns single flash sale |
| `limit` | int | No | Per page |
| `page` | int | No | Page number |

**Response:** Paginated `FlashSaleResource`

| Field | Type | Can Be Null | Notes |
|-------|------|-------------|-------|
| `id` | int | No | |
| `name` | string | Yes | **null** if no translation |
| `discription` | string | Yes | **null** if no translation (note: typo in field name) |
| `slug` | string | No | |
| `start_date` | string | Yes | **null** if not set |
| `end_date` | string | Yes | **null** if not set |
| `image.desktop` | string | Yes | **null** if no media |
| `image.mobile` | string | Yes | **null** if no media |
| `products` | array | No | Empty `[]` if not loaded |

**DB Tables:** `flash_sales`, `flash_sale_products`

---

## 20. GET `/api/v1/general/flash-sales/{slug}`

### Single flash sale with products

**Auth:** None

**URL Example:**
```
/api/v1/general/flash-sales/summer-sale
```

**URL Parameters:**

| Param | Type | Required | Description |
|-------|------|----------|-------------|
| `slug` | string | Yes | Flash sale slug |

**Response:** Single `FlashSaleResource` (same structure, products loaded)

**Error:** 404

**DB Tables:** `flash_sales`, `flash_sale_products`, `products`

---

## 21. GET `/api/v1/general/promotions`

### Promotion list

**Auth:** None

**URL Example:**
```
/api/v1/general/promotions
/api/v1/general/promotions?slug=summer-offer
/api/v1/general/promotions?slug=summer-offer&with_product=true
```

**Query Parameters:**

| Param | Type | Required | Description |
|-------|------|----------|-------------|
| `slug` | string | No | If provided, returns single promotion |
| `with_product` | bool | No | When slug is provided, limits products to 1 |
| `limit` | int | No | Per page |
| `page` | int | No | Page number |

**Response:** Paginated `PromotionResource`

| Field | Type | Can Be Null | Notes |
|-------|------|-------------|-------|
| `id` | int | No | |
| `name` | string | Yes | **null** if no translation |
| `slug` | string | No | |
| `status` | bool | No | |
| `image.desktop` | string | Yes | **null** if no media |
| `image.mobile` | string | Yes | **null** if no media |
| `products` | array | No | **Omitted** if not loaded |

**DB Tables:** `promotions`

---

## 22. GET `/api/v1/general/promotions/{slug}`

### Single promotion

**Auth:** None

**URL Example:**
```
/api/v1/general/promotions/summer-offer
```

**URL Parameters:**

| Param | Type | Required | Description |
|-------|------|----------|-------------|
| `slug` | string | Yes | Promotion slug |

**Response:** Single `PromotionResource` (same structure)

**Error:** 404

---

## 23. GET `/api/v1/general/banners`

### Banner list

**Auth:** None

**URL Example:**
```
/api/v1/general/banners
/api/v1/general/banners?slug=home-top
```

**Query Parameters:**

| Param | Type | Required | Description |
|-------|------|----------|-------------|
| `slug` | string | No | If provided, returns single banner |

**Response:** Collection of `BannerResource`

| Field | Type | Can Be Null | Notes |
|-------|------|-------------|-------|
| `id` | int | No | |
| `title` | string | Yes | **null** if no translation |
| `slug` | string | No | |
| `description` | string | Yes | **null** if no translation |
| `status` | bool | No | |
| `image.desktop` | string | Yes | **null** if no media |
| `image.mobile` | string | Yes | **null** if no media |
| `products` | array | No | **Empty `[]`** if not loaded or no products |

**DB Tables:** `banners`, `banner_product`

---

## 24. GET `/api/v1/general/banners/{slug}`

### Single banner

**Auth:** None

**URL Example:**
```
/api/v1/general/banners/home-top
```

**URL Parameters:**

| Param | Type | Required | Description |
|-------|------|----------|-------------|
| `slug` | string | Yes | Banner slug |

**Response:** Single `BannerResource` (same structure)

**Error:** 404

---

## 25. GET `/api/v1/general/coupons`

### Coupon list

**Auth:** None

**URL Example:**
```
/api/v1/general/coupons
/api/v1/general/coupons?search=SUMMER&limit=5
/api/v1/general/coupons?start_date=2026-01-01&end_date=2026-12-31
/api/v1/general/coupons?couponsId=1,2,3&order=asc
```

**Query Parameters:**

| Param | Type | Required | Description |
|-------|------|----------|-------------|
| `search` | string | No | Search by name |
| `limit` | int | No | Max results (default 10) |
| `start_date` | string | No | Filter by created_at >= (Y-m-d) |
| `end_date` | string | No | Filter by created_at <= (Y-m-d) |
| `couponsId` | string | No | Comma-separated coupon IDs |
| `order` | string | No | `asc` or `desc` by id (default `desc`) |

**Response:** Collection of `CouponResource`

| Field | Type | Can Be Null | Notes |
|-------|------|-------------|-------|
| `id` | int | No | |
| `name` | string | Yes | **null** if no translation |
| `slug` | string | No | |
| `image.desktop` | string | Yes | **null** if no media |
| `image.mobile` | string | Yes | **null** if no media |
| `borderColor` | string | Yes | **null** if `border_color` is null in DB |
| `borderless` | bool | No | `false` if `borderless` is null in DB |

**DB Tables:** `coupons`

---

## 26. POST `/api/v1/general/coupons/apply`

### Apply coupon code to cart

**Auth:** Required (`auth:sanctum`)

**URL Example:**
```
POST /api/v1/general/coupons/apply
Content-Type: application/json
Body: { "code": "SUMMER2026" }
```

**Request Body:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `code` | string | Yes | Coupon code |

**Flow:**
1. Finds valid coupon by code
2. Checks if user already used this coupon
3. Records coupon usage
4. Calculates discounted total
5. Updates cart with coupon

**Response:**
```json
{
    "status": 200,
    "message": "Coupon applied successfully",
    "success": true,
    "data": {
        "total_price": 180.00,
        "coupon_discount": 20.00
    }
}
```

| data Field | Type | Can Be Null | Notes |
|------------|------|-------------|-------|
| `total_price` | float | No | Price after coupon discount |
| `coupon_discount` | float | No | Discount amount applied |

**Error:** 400 if invalid code, already used, no cart, or usage limit reached.

**DB Tables:** `coupons`, `coupon_usages`, `carts`

---

## 27. GET `/api/v1/general/brands`

### Brand list

**Auth:** None

**URL Example:**
```
/api/v1/general/brands
/api/v1/general/brands?slug=apple
```

**Query Parameters:**

| Param | Type | Required | Description |
|-------|------|----------|-------------|
| `slug` | string | No | If provided, returns single brand |

**Response:** Collection of `BrandResource`

| Field | Type | Can Be Null | Notes |
|-------|------|-------------|-------|
| `id` | int | No | |
| `name` | string | Yes | **null** if no translation |
| `slug` | string | No | |
| `image.desktop` | string | Yes | **null** if no media |
| `image.mobile` | string | Yes | **null** if no media |
| `status` | bool | No | |
| `products` | array | No | **Omitted** if relation not loaded |

**DB Tables:** `brands`

---

## 28. GET `/api/v1/general/brands-with-products`

### Brands with their products

**Auth:** None

**URL Example:**
```
/api/v1/general/brands-with-products
/api/v1/general/brands-with-products?limit=10
```

**Query Parameters:**

| Param | Type | Required | Description |
|-------|------|----------|-------------|
| `limit` | int | No | Limit per brand (default service default) |

**Response:** Array of `ProductMiniResource` — products grouped/flat by brand.

**DB Tables:** `brands`, `products`

---

## 29. GET `/api/v1/general/brands/{slug}`

### Single brand

**Auth:** None

**URL Example:**
```
/api/v1/general/brands/apple
```

**URL Parameters:**

| Param | Type | Required | Description |
|-------|------|----------|-------------|
| `slug` | string | Yes | Brand slug |

**Response:** Single `BrandResource` (same structure, with products loaded)

**Error:** 404

---

## 30. GET `/api/v1/general/faqs`

### FAQ list

**Auth:** None

**URL Example:**
```
/api/v1/general/faqs
```

**Response:** Collection of `FaqResource`

| Field | Type | Can Be Null | Notes |
|-------|------|-------------|-------|
| `id` | int | No | |
| `faq_title` | string | Yes | **null** if no translation |
| `faq_description` | string | Yes | **null** if no translation |

**DB Tables:** `faqs`

---

## 31. GET `/api/v1/general/content-pages`

### Content pages with sections

**Auth:** None

**URL Example:**
```
/api/v1/general/content-pages
/api/v1/general/content-pages?page=1
```

**Query Parameters:**

| Param | Type | Required | Description |
|-------|------|----------|-------------|
| `page` | int | No | Page number (paginated 15 per page) |

**Response:** Paginated `ContentPageResource`

| Field | Type | Can Be Null | Notes |
|-------|------|-------------|-------|
| `id` | int | No | |
| `title` | string | No | |
| `slug` | string | No | |
| `is_active` | bool | No | |
| `sections` | array | No | Only active sections. Empty `[]` if none |

**SectionResource:**

| Field | Type | Can Be Null | Notes |
|-------|------|-------------|-------|
| `id` | int | No | |
| `type` | string | No | e.g. `best_product_sales` |
| `title` | string | Yes | **null** if `title_visible` is false |
| `is_active` | bool | No | |
| `endpoint` | string | No | e.g. `general/best_product_sales?limit=10` |
| `order` | int | No | |
| `setting` | object | No | `{ "front": {...}, "back": {...} }` or `null` |

**DB Tables:** `content_pages`, `sections`, `section_types`, `section_type_settings`

---

## 32. GET `/api/v1/general/content-pages/{slug}`

### Single content page

**Auth:** None

**URL Example:**
```
/api/v1/general/content-pages/about-us
```

**URL Parameters:**

| Param | Type | Required | Description |
|-------|------|----------|-------------|
| `slug` | string | Yes | Page slug |

**Response:** Single `ContentPageResource` (same structure)

**Error:** 404

---

## 33. GET `/api/v1/general/settings`

### Site settings

**Auth:** None

**URL Example:**
```
/api/v1/general/settings
```

**Response:** `SettingResource`

| Field | Type | Can Be Null | Notes |
|-------|------|-------------|-------|
| `site_name` | string | Yes | **null** if no translation |
| `site_desc` | string | Yes | **null** if no translation |
| `meta_desc` | string | Yes | **null** if no translation |
| `site_copy_right` | string | Yes | **null** if no translation |
| `logo` | string | Yes | **null** if no media |
| `favicon` | string | Yes | **null** if no media |
| `site_email` | string | Yes | **null** if not set in DB |
| `email_support` | string | Yes | **null** if not set |
| `facebook` | string | Yes | **null** if not set |
| `instagram` | string | Yes | **null** if not set |
| `linkedin` | string | Yes | **null** if not set |
| `promotion_video_url` | string | Yes | **null** if not set |
| `youtube` | string | Yes | **null** if not set |
| `phone` | string | Yes | **null** if not set |

**DB Tables:** `settings` (first row)

---

## 34. GET `/api/v1/general/search`

### Search (global)

**Auth:** None

**URL Example:**
```
/api/v1/general/search?search=phone
```

**Query Parameters:**

| Param | Type | Required | Description |
|-------|------|----------|-------------|
| `search` | string | No | Search term |

> **⚠ NOTE:** The `SearchService@search()` method body is currently **empty**. All search logic is commented out. This endpoint will return an empty `data` response.

---

## Non-Prefixed Routes

---

## 35. GET `/api/v1/enum-types`

### Get all enum values

**Auth:** None

**URL Example:**
```
/api/v1/enum-types
```

**Response:**
```json
{
    "discount-type": ["percentage", "fixed"],
    "coupon-type": ["percentage", "fixed"],
    "product-type": ["simple", "variable"],
    "promotion-type": ["percentage", "fixed_rate", "gift"],
    "promotion-mount-type": ["order", "product"],
    "flash-sale-type": ["percentage", "fixed"]
}
```

---

## 36. GET `/api/v1/product-type`

### Get product strategy type values

**Auth:** None

**URL Example:**
```
/api/v1/product-type
```

**Response:**
```json
[
    "best_product_sales",
    "brands_product",
    "new_arrivals",
    "all_product_discounts",
    "product_discount_today_or_low_qty",
    "flash_sales_product",
    "flash_sales_end_today",
    "product_for_parent_category",
    "flash_sales_end_week"
]
```

---

## 37. GET `/api/v1/check-card-payment`

### Get test card data (MyFatoorah sandbox)

**Auth:** None

**URL Example:**
```
/api/v1/check-card-payment
```

**Response:**
```json
{
    "CardNumber": "2223000000000007",
    "CardExpiryMonthand year": "01/39",
    "CardCVV": "100"
}
```

---

## 38. GET `/api/v1/test-mail`

### Send test email

**Auth:** None

**URL Example:**
```
/api/v1/test-mail
```

Sends a raw test email to `mohtareq1999m@email.com` with subject "Test Email".

**Response:** `"sent"`

---

## Middleware Summary

| Middleware | Endpoints |
|------------|-----------|
| `api` | All routes |
| `check-lang` | All `/general/*` routes |
| `auth:sanctum` | products/{id}/reviews (POST), products/reviews/{id} (PUT), orders, checkout/promotions, checkout, checkout/fast, fast-shipping/orders, coupons/apply |
| `check-email` | orders, checkout/promotions, checkout |
| `throttle:general` | Not currently applied (commented out) |

---

## Global Nullable Field Summary

The following fields are **always null** in responses when the corresponding DB column is null:

### Media-related (null when no image uploaded):
- `image.desktop` / `image.mobile` on: categories, sliders, banners, promotions, coupons, brands, flash-sales
- `images.thumbnail` on products
- `logo`, `favicon` on settings

### Translation-related (null when no translation for current locale):
- `name`, `description`, `details`, `title`, `faq_title`, `faq_description`, `site_name`, `site_desc`, `site_copy_right`

### Pricing-related (null when product has no discount/flash sale):
- `price_after_discount` — null if `has_discount = false`
- `price_after_flash_sale` — null if `has_flash_sale = false`
- `current_price` — null if neither discount nor flash sale
- `discount_type` / `discount_amount` — null if no discount
- `discount_valid` — key omitted entirely if `has_discount = false`

### Dimension-related (null when not set):
- `height`, `width`, `length`, `weight` — can be null on both products and variants

### Order-related:
- `order_number` — null before order number is generated
- `promotion` — null if no promotion applied
- `variant` — null for order items without a variant
- `promotion_id` / `promotion_discount_amount` — null if no promotion
- `borderColor` — null if `border_color` column is null

### Cart-related:
- `total_price` — null if coupon was never applied to cart
