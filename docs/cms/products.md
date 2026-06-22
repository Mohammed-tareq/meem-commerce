# Products API

## Overview

Endpoints for managing the product catalog. Supports simple and variable products with discount/flash sale pricing, categorization, tagging, media, and inventory tracking.

## Permissions

| Permission | Enum Constant | Endpoints |
|---|---|---|
| `view-products` | `VIEW_PRODUCTS` | `GET /products`, `GET /products/{id}` |
| `view-product` | `VIEW_PRODUCT` | — |
| `create-product` | `CREATE_PRODUCT` | `POST /products` |
| `update-product` | `UPDATE_PRODUCT` | `PUT /products/{id}` |
| `delete-product` | `DELETE_PRODUCT` | `DELETE /products/{id}` |

## Route Registration

```php
// Public (authenticated) — index + show only
Route::apiResource('products', ProductController::class, ['only' => ['index', 'show']]);

// Super Admin — store, update, destroy
Route::apiResource('products', ProductController::class, ['only' => ['store', 'update', 'destroy']])
     ->middleware(['role:super_admin', 'auth:sanctum', 'email.verified']);
```

---

## Endpoints

### 1. List Products

**`GET /products`**

**Permissions:** `view-products`

**Query Parameters**

| Parameter | Type | Required | Description |
|---|---|---|---|
| `limit` | integer | No | Items per page (default: 15) |
| `page` | integer | No | Page number |
| `with` | string | No | Relations to eager load (semicolon separated, e.g. `variations;categories;flash_sales`) |
| `order_by` | string | No | Column to sort by |
| `sort` | string | No | `asc` or `desc` |
| `search` | string | No | Search in product fields (via RequestCriteria) |
| `date_range` | string | No | Date range `YYYY-MM-DD//YYYY-MM-DD` for availability filtering |
| `flash_sale_builder` | mixed | No | Flash sale product processing |

**Business Rules**

- Unavailable products (from `date_range`) are automatically excluded via `getUnavailableProducts()`.
- Digital file access in `with` params throws `AuthorizationException`.
- Served through `ProductCollection` (wraps `ProductResource` with pagination links).

**Response Structure**

```json
{
    "success": true,
    "message": "Data fetched successfully",
    "data": {
        "data": [ ... ProductResource ... ],
        "links": {
            "current_page": 1,
            "from": 1,
            "to": 15,
            "last_page": 5,
            "path": "http://example.com/products",
            "per_page": 15,
            "total": 72,
            "next_page_url": "http://example.com/products?page=2",
            "prev_page_url": null,
            "last_page_url": "http://example.com/products?page=5",
            "first_page_url": "http://example.com/products?page=1"
        }
    }
}
```

---

### 2. Get Single Product

**`GET /products/{id}`**

**Permissions:** `view-products`

| Parameter | Type | Required | Description |
|---|---|---|---|
| `id` | integer | Yes | Product ID |

**Business Rules**

- Fetches product via `repository->where('id', $id)->firstOrFail()`.
- Loads related products (same categories, excluding current).
- Eager loads `variations`, `categories`, `shops`, `flash_sales`.

**Response:** Single `ProductResource`.

---

### 3. Create Product

**`POST /products`**

**Permissions:** `create-product`

**Request Body**

Sent as `multipart/form-data` (supports image uploads).

| Field | Type | Required | Rules |
|---|---|---|---|
| `name` | object | **Yes** | array; each locale: required, string, max:255, unique translation |
| `description` | object | **Yes** | array; each locale: required, string, max:10000 |
| `product_type` | string | **Yes** | `simple` or `variable` |
| `categories` | array | **Yes** | array of integer; each must exist in `categories` table |
| `categories.*` | integer | **Yes** | exists:categories,id |
| `price` | numeric | Conditional | sometimes; min:0; **required_if** product_type=simple |
| `quantity` | integer | No | sometimes; min:1 |
| `in_stock` | boolean | **Yes** | `1` or `0` |
| `has_discount` | boolean | **Yes** | `true`, `false`, `1`, or `0` |
| `has_flash_sale` | boolean | **Yes** | `true`, `false`, `1`, or `0` |
| `flash_sale_id` | integer | Conditional | **required_if** has_flash_sale=1; exists:flash_sales,id |
| `discount_type` | string | Conditional | **required_if** has_discount=1; `percentage` or `fixed_rate` |
| `discount_amount` | numeric | Conditional | **required_if** has_discount=1; min:1 |
| `discount_status` | boolean | Conditional | **required_if** has_discount=1; `1` or `0` |
| `start_date` | date | No | sometimes |
| `end_date` | date | No | sometimes; after_or_equal:start_date |
| `status` | boolean | No | sometimes; `1` or `0` |
| `pieces` | integer | No | sometimes; min:1 |
| `height` | numeric | No | nullable |
| `width` | numeric | No | nullable |
| `length` | numeric | No | nullable |
| `weight` | numeric | No | nullable |
| `is_fast_shipping_available` | boolean | No | nullable |
| `banner_id` | integer | No | sometimes; exists:banners,id |
| `images` | array | No | files (jpeg,png,jpg,gif, max:2048) — upload via multipart |
| `variants` | array | Conditional | sometimes; **required for variable products** — auto-detects product_type |
| `variants.*.price` | numeric | Conditional | required_with:variants; min:0 |
| `variants.*.quantity` | integer | Conditional | required_with:variants; min:0 |
| `variants.*.attribute_values` | array | Conditional | required_with:variants; array of attribute_value IDs |
| `variants.*.attribute_values.*` | integer | Conditional | exists:attribute_values,id |
| `variants.*.weight` | numeric | No | sometimes; min:0 |
| `variants.*.length` | numeric | No | sometimes; min:0 |
| `variants.*.width` | numeric | No | sometimes; min:0 |
| `variants.*.height` | numeric | No | sometimes; min:0 |

**Note:** `product_type` is overridden in `storeProduct()` — if `variants` array is non-empty, `product_type` is forced to `variable`, otherwise `simple`.

**Validation Rules Table**

| Rule | Logic |
|---|---|
| `name` | required, array |
| `name.*` | required, string, max:255, unique_translation:products |
| `description` | required, array |
| `description.*` | required, string, max:10000 |
| `product_type` | required, in: `simple`, `variable` |
| `categories` | required, array |
| `categories.*` | integer, exists:categories,id |
| `price` | sometimes, numeric, min:0, required_if:product_type=simple |
| `quantity` | sometimes, integer, min:1 |
| `in_stock` | required, in: `1`, `0` |
| `has_discount` | required, in: `true`, `false`, `1`, `0` |
| `has_flash_sale` | required, in: `true`, `false`, `1`, `0` |
| `flash_sale_id` | required_if:has_flash_sale=1, exists:flash_sales,id |
| `discount_type` | required_if:has_discount=1, in: `percentage`, `fixed_rate` |
| `discount_amount` | required_if:has_discount=1, numeric, min:1 |
| `discount_status` | required_if:has_discount=1, in: `1`, `0` |
| `start_date` | sometimes, date |
| `end_date` | sometimes, date, after_or_equal:start_date |
| `status` | sometimes, in: `1`, `0` |
| `pieces` | sometimes, integer, min:1 |
| `height` | nullable, numeric |
| `width` | nullable, numeric |
| `length` | nullable, numeric |
| `weight` | nullable, numeric |
| `is_fast_shipping_available` | nullable, boolean |
| `banner_id` | sometimes, exists:banners,id |
| `variants` | sometimes, array |
| `variants.*.price` | required_with:variants, numeric, min:0 |
| `variants.*.quantity` | required_with:variants, integer, min:0 |
| `variants.*.attribute_values` | required_with:variants, array |
| `variants.*.attribute_values.*` | integer, exists:attribute_values,id |
| `variants.*.weight` | sometimes, numeric, min:0 |
| `variants.*.length` | sometimes, numeric, min:0 |
| `variants.*.width` | sometimes, numeric, min:0 |
| `variants.*.height` | sometimes, numeric, min:0 |

**Business Rules**

- `product_type` is auto-set: `variable` if variants sent, `simple` otherwise.
- Pricing is calculated via `ProductPricingService`:
  - `price_after_discount` — if `has_discount`, applies discount type/amount.
  - `price_after_flash_sale` — if `has_flash_sale`, applies flash sale pricing.
- Auto-generates `slug` from name.
- Auto-generates `sku` if empty (format: `PRD-{uuid}`).
- Images uploaded via Spatie Media Library to `products` collection.
- Categories synced via `categories()` relationship.
- Flash sale synced via `flash_sales()` relationship.
- All operations wrapped in `DB::transaction()` — rollback on failure.

**Success Response (201)**

```json
{
    "success": true,
    "message": "Product created successfully",
    "data": { ... ProductResource ... }
}
```

**Error Responses**

| Code | Description |
|---|---|
| 201 | Product created successfully |
| 401 | Unauthenticated |
| 403 | Forbidden — missing `create-product` permission |
| 422 | Validation error |
| 500 | Something went wrong (with DB transaction rollback) |

---

### 4. Update Product

**`PUT /products/{id}`**

**Permissions:** `update-product`

**Request Body**

Same as Create but all fields are `sometimes` (optional). Only send fields that changed.

| Field | Change from Create |
|---|---|
| `name` | sometimes instead of required |
| `description` | sometimes instead of required |
| `product_type` | sometimes instead of required |
| `categories` | sometimes instead of required |
| `price` | sometimes (same as create) |
| `quantity` | sometimes (same) |
| `images` | sometimes (new in update — NOT in create) |
| `in_stock` | sometimes instead of required |
| `has_discount` | sometimes instead of required |
| `has_flash_sale` | sometimes instead of required |
| `status` | sometimes, in: ProductStatus enum values (different from create) |
| `shop_id` | sometimes, exists:shops,id **(new — only in update)** |
| `variants.*.id` | sometimes, exists:product_variants,id **(new — identifies variant to update)** |
| `variants.*.sale_price` | sometimes **(new — only in update)** |

**Unique fields to Update only:**

| Field | Rule |
|---|---|
| `shop_id` | sometimes, exists:shops,id |
| `images` | sometimes, array; each: sometimes, image, mimes:jpeg,png,jpg,gif, max:2048 |
| `images.*` | sometimes, image, mimes:jpeg,png,jpg,gif, max:2048 |
| `variants.*.id` | sometimes, exists:product_variants,id |
| `variants.*.sale_price` | sometimes, numeric, min:0 |
| `name.*` | unique_translation:products — ignores current product ID |

**Business Rules**

- Same pricing recalculation as Create (reads existing product values for unchanged fields).
- On update with variants: ALL existing variants are **deleted** and re-created (line: `ProductVariant::where('product_id', $product->id)->delete()`).
- Image update uses `updateImages()` (replaces existing media).
- Categories and flash sales re-synced.

**Success Response (200)**

```json
{
    "success": true,
    "message": "Product updated successfully",
    "data": { ... ProductResource ... }
}
```

**Error Responses**

| Code | Description |
|---|---|
| 200 | Product updated successfully |
| 401 | Unauthenticated |
| 403 | Forbidden |
| 422 | Validation error |
| 500 | Could not update the resource |

---

### 5. Delete Product

**`DELETE /products/{id}`**

**Permissions:** `delete-product`

**Success Response (200)**

```json
{
    "success": true,
    "message": "Product deleted successfully"
}
```

---

### 6. Toggle Fast Shipping

**`PUT /products/{id}/fast-shipping`**

**Permissions:** `update-product`

| Field | Type | Required | Rules |
|---|---|---|---|
| `is_fast_shipping_available` | boolean | **Yes** | required, boolean |

**Success Response (200)**

```json
{
    "success": true,
    "message": "Product updated successfully",
    "data": { ... ProductResource with loaded relations ... }
}
```

---

## ProductResource — Response Structure

Returned by all product endpoints:

| Field | Type | Description |
|---|---|---|
| `id` | integer | Primary key |
| `name` | string | Translated name (current locale) |
| `slug` | string | URL slug |
| `description` | array | Translated description `{en, ar}` |
| `price` | float | Base price |
| `current_price` | float | Computed current price (after all discounts) |
| `price_after_discount` | float | Price after regular discount |
| `price_after_flash_sale` | float | Price after flash sale |
| `discount_type` | string | `percentage` or `fixed_rate` |
| `discount_amount` | float | Discount value |
| `start_date` | date | Discount start |
| `end_date` | date | Discount end |
| `sku` | string | Stock keeping unit |
| `stock_quantity` | integer | Total stock |
| `reserved_quantity` | integer | Reserved stock |
| `available_stock` | integer | Computed available stock |
| `quantity` | integer | Current quantity |
| `sold_quantity` | integer | Units sold |
| `in_stock` | boolean | Stock availability |
| `status` | boolean | Active/inactive |
| `product_type` | string | `simple` or `variable` |
| `height` | float | Dimensions |
| `width` | float | Dimensions |
| `length` | float | Dimensions |
| `weight` | float | Weight |
| `has_flash_sale` | boolean | Has active flash sale |
| `has_discount` | boolean | Has discount enabled |
| `is_fast_shipping_available` | boolean | Fast shipping eligibility |
| `discount_valid` | boolean | (merged when has_discount) Whether discount is within date range |
| `banner_id` | integer | Associated banner |
| `created_at` | ISO 8601 | Creation timestamp |
| `categories` | array | `[{id, name, slug}]` (when loaded) |
| `flash_sales` | array | FlashSaleResource collection (when loaded) |
| `images` | array | Media URLs from `products` collection |
| `variants` | array | Variant details (when loaded via `variations`) |
| `related_products` | array | ProductResource collection (when loaded via `related_products`) |

### Variant Structure (within `variants`)

| Field | Type | Description |
|---|---|---|
| `id` | integer | Variant ID |
| `price` | float | Variant price |
| `current_price` | float | Computed current price |
| `stock_quantity` | integer | Total stock |
| `reserved_quantity` | integer | Reserved |
| `available_stock` | integer | Available |
| `quantity` | integer | Quantity |
| `height` | float | Dimensions |
| `width` | float | |
| `length` | float | |
| `weight` | float | |
| `attributes` | array | `[{attribute_name, value}]` |

---

## Model: Enums

| Enum | Values |
|---|---|
| `ProductType` | `simple`, `variable` |
| `ProductStatus` | `under_review`, `approved`, `rejected`, `publish`, `unpublish`, `draft` |
| `DiscountType` | `percentage`, `fixed_rate` |

## Model: Relationships

| Relation | Type | Model |
|---|---|---|
| `type()` | BelongsTo | Type |
| `shops()` | BelongsToMany | Shop (pivot: product_shop) |
| `author()` | BelongsTo | Author |
| `manufacturer()` | BelongsTo | Manufacturer |
| `shipping()` | BelongsTo | Shipping |
| `categories()` | BelongsToMany | Category (pivot: category_product) |
| `brands()` | BelongsToMany | Brand (pivot: brand_product) |
| `tags()` | BelongsToMany | Tag (pivot: product_tag) |
| `orders()` | BelongsToMany | Order (pivot: order_product) |
| `variations()` | HasMany | ProductVariant |
| `reviews()` | HasMany | Review |
| `questions()` | HasMany | Question |
| `wishlists()` | HasMany | Wishlist |
| `flash_sales()` | BelongsToMany | FlashSale (pivot: flash_sale_products) |
| `promotions()` | BelongsToMany | Promotion (pivot: promotion_product) |
| `coupons()` | BelongsToMany | Coupon (pivot: coupon_product) |
| `sliders()` | BelongsToMany | Slider (pivot: slider_product) |
| `digital_file()` | MorphOne | DigitalFile |
| `availabilities()` | MorphMany | Availability |
| `dropoff_locations()` | BelongsToMany | Resource (pivot: dropoff_location_product) |
| `pickup_locations()` | BelongsToMany | Resource (pivot: pickup_location_product) |
| `deposits()` | BelongsToMany | Resource (pivot: deposit_product) |
| `persons()` | BelongsToMany | Resource (pivot: person_product) |
| `features()` | BelongsToMany | Resource (pivot: feature_product) |

## Model: Appends (computed attributes)

| Attribute | Description |
|---|---|
| `current_price` | Final computed price via `ProductPricingService` |
| `price_after_discount` | Price after regular discount |
| `price_after_flash_sale` | Price after flash sale |
| `final_price` | Alias for current_price |

## Additional Endpoints (separate routes)

| Method | URL | Function | Description |
|---|---|---|---|
| GET | `/best-selling-products` | `bestSellingProducts()` | Products sorted by total sales (completed orders) |
| GET | `/popular-products` | `popularProducts()` | Products sorted by order count, filterable by shop/type/range |
| GET | `/draft-products` | `draftedProducts()` | Paginated draft products for current user's shops |
| GET | `/product-stock` | `productStock()` | Products with quantity < 10 (low stock) |
| GET | `/products/calculate-rental-price` | `calculateRentalPrice()` | Rental price calculation (requires `is_rental`) |
| GET | `/my-wishlists` | `myWishlists()` | Current user's wishlist products |
| PUT | `/products/{id}/fast-shipping` | `toggleFastShipping()` | Toggle fast shipping availability |

## Dependencies

| Component | Path |
|---|---|
| Controller | `packages/marvel/src/Http/Controllers/ProductController.php` |
| Create Request | `packages/marvel/src/Http/Requests/ProductCreateRequest.php` |
| Update Request | `packages/marvel/src/Http/Requests/ProductUpdateRequest.php` |
| Product Resource | `packages/marvel/src/Http/Resources/product/ProductResource.php` |
| Product Collection | `packages/marvel/src/Http/Resources/product/ProductCollection.php` |
| Get Single Resource | `packages/marvel/src/Http/Resources/product/GetSingleProductResource.php` |
| Related Resource | `packages/marvel/src/Http/Resources/product/RelatedProductResource.php` |
| Collection Mini | `packages/marvel/src/Http/Resources/product/ProductCollectionMini.php` |
| Variant Resource | `packages/marvel/src/Http/Resources/product/ProductVariantResource.php` |
| Repository | `packages/marvel/src/Database/Repositories/ProductRepository.php` |
| Model | `packages/marvel/src/Database/Models/Product.php` |
| Type Enum | `packages/marvel/src/Enums/ProductType.php` |
| Status Enum | `packages/marvel/src/Enums/ProductStatus.php` |
| Discount Enum | `packages/marvel/src/Enums/DiscountType.php` |
| Pricing Service | `packages/marvel/Services/Pricing/ProductPricingService.php` |
