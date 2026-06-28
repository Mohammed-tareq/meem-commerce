# Product Import/Export System

## Overview

The import/export system allows bulk product management via Excel files (`.xlsx`). The file contains **7 sheets**. Attributes are **auto-generated** from the `product_variants` sheet — there is no separate attributes sheet.

Products are linked across sheets by the `products.sku` column.

All importing logic reuses existing product creation/update business logic from the system. The importer must NOT directly calculate or save computed fields.

## Implementation Rules

Before implementing features:

1. Analyze existing project structure.
2. Read existing Product creation/update flow.
3. Find the real business logic responsible for:
   - Product price calculation
   - Discount calculation
   - Sale price calculation
   - Flash sale calculation
   - Final price calculation

The importer MUST reuse the same logic. Do NOT create duplicated calculation logic inside import classes.

## Required Package

This system uses `maatwebsite/excel` (Laravel Excel) with the following features:

| Feature | Used In |
|---------|---------|
| `WithMultipleSheets` | `ProductsImport`, `ProductsExport` |
| `WithChunkReading` | `ProductsSheetImport`, `ProductVariantsSheetImport`, `ImagesSheetImport` |
| Queued Import | `ImportProductsJob` (dispatched on `high` queue) |
| Export Classes | 7 sheet export classes, one per sheet |

---

## Endpoints

### Import

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/api/v1/products/import` | Yes | Upload Excel file to start import |
| GET | `/api/v1/products/import/{id}` | Yes | Check import progress/status |
| GET | `/api/v1/products/import/{id}/download-errors` | Yes | Download error report for failed rows |
| GET | `/api/v1/samples/product-import` | No | Download sample Excel template |

**POST `/api/v1/products/import`** accepts:
- `file` (required) — `.xlsx`, `.xls`, or `.ods` file (max 20MB)

### Export

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/v1/products/export` | No | Export all products as Excel |

**GET `/api/v1/products/export`** accepts optional filters:
- `status` — filter by product status (0 or 1)
- `product_type` — `simple` or `variable`
- `category_id` — filter by category
- `brand_id` — filter by brand

---

## Excel Sheets (7)

### 1. `products`

Primary product data. One row = one product. UPSERT by `sku`.

**Example:**

| sku       | name_en | price | product_type |
|-----------|---------|-------|--------------|
| PHONE-001 | iPhone  | 1000  | simple       |
| SHIRT-001 | Shirt   | 500   | variable     |

| Column | Type | Required | Description |
|--------|------|----------|-------------|
| `sku` | string | **Yes** | Unique. If exists → update; if new → create |
| `name_en` | string | **Yes** | English name (stored as JSON) |
| `name_ar` | string | No | Arabic name (stored as JSON) |
| `description_en` | string | No | English description |
| `description_ar` | string | No | Arabic description |
| `price` | float | **Yes** | Product base price |
| `product_type` | string | No | `simple` or `variable` (default: `simple`) |
| `quantity` | integer | No | Stock quantity |
| `status` | boolean | No | Boolean-parsed |
| `in_stock` | boolean | No | Stock availability |
| `has_discount` | boolean | No | Discount flag |
| `discount_type` | string | No | `percentage` or `fixed_rate` |
| `discount_amount` | float | No | Discount value |
| `start_date` | date | No | Discount start (e.g. `2026-07-01`) |
| `end_date` | date | No | Discount end |
| `height` | string | No | Product height |
| `width` | string | No | Product width |
| `length` | string | No | Product length |
| `weight` | string | No | Product weight |

**Export notes:** The `pieces` and `has_flash_sale` columns are NOT exported.

### 2. `product_variants`

One row = one variant. Attributes are auto-generated from this sheet.

**Example:**

| product_sku | price | sale_price | attributes                              |
|-------------|-------|------------|-----------------------------------------|
| SHIRT-001   | 200   | 150        | Color|اللون:Red|احمر-Size|المقاس:L|كبير |

| Column | Type | Required | Description |
|--------|------|----------|-------------|
| `product_sku` | string | **Yes** | Must match a `sku` in the `products` sheet |
| `price` | float | **Yes** | Used as matching field |
| `sale_price` | float | No | Used as matching field |
| `quantity` | integer | No | Variant stock |
| `height` | string | No | Used as matching field |
| `width` | string | No | Used as matching field |
| `length` | string | No | Used as matching field |
| `weight` | string | No | Used as matching field |
| `attributes` | string | No | Format: `EnglishName|ArabicName:EnglishValue|ArabicValue` (see below) |

**Variant matching** uses existing database fields: `product_id`, `price`, `sale_price`, `height`, `width`, `length`, `weight`. Empty/null fields are matched as `NULL` to prevent duplicate variants.

**Behavior:** Existing matches are **updated**, new rows are **created**. After all rows are processed, variants in the database not present in the Excel are **deleted** (the Excel becomes the complete set of variants for each product).

The `product_type` is automatically set to `variable`.

**Export notes:** The `variant_sku` and `in_stock` columns are NOT exported.

### 3. `images`

Each row represents one image. Multiple images for the same product use multiple rows. Images are attached via Spatie Media Library.

| Column | Type | Required | Description |
|--------|------|----------|-------------|
| `product_sku` | string | **Yes** | Must match a `sku` in the `products` sheet |
| `image` | string | No | Single image URL |

**Example:**

| product_sku | image                           |
|-------------|---------------------------------|
| PHONE-001   | https://site.com/image1.jpg     |
| PHONE-001   | https://site.com/image2.jpg     |

**Import:** Get product by SKU, download the image via `UrlImageHandler` (supports Google Drive URLs), validate MIME type and file size (max 5MB), then attach via `$product->addMedia()` → `toMediaCollection('products')`. Failed downloads are tracked in the import error report.

**Google Drive URLs** are automatically converted:
- `https://drive.google.com/file/d/FILE_ID/view` → `https://drive.google.com/uc?export=download&confirm=t&id=FILE_ID`
- If download fails, falls back to `https://drive.google.com/thumbnail?id=FILE_ID&sz=w1000`

**Export:** One row per image URL.

### 4. `categories`

| Column | Type | Required | Description |
|--------|------|----------|-------------|
| `product_sku` | string | **Yes** | Links to `products.sku` |
| `category_slug` | string | **Yes** | Existing category slug |

Replaces all existing category relations (sync).

### 5. `brands`

| Column | Type | Required | Description |
|--------|------|----------|-------------|
| `product_sku` | string | **Yes** | Links to `products.sku` |
| `brand_slug` | string | **Yes** | Existing brand slug |

Replaces all existing brand relations (sync).

### 6. `flash_sales`

| Column | Type | Required | Description |
|--------|------|----------|-------------|
| `product_sku` | string | **Yes** | Links to `products.sku` |
| `flash_sale_slug` | string | **Yes** | Existing flash sale slug |

Replaces all existing flash sale relations (sync).

### 7. `sliders`

| Column | Type | Required | Description |
|--------|------|----------|-------------|
| `product_sku` | string | **Yes** | Links to `products.sku` |
| `slider_slug` | string | **Yes** | Existing slider slug |

Replaces all existing slider relations (sync).

---

## Boolean Values

These columns accept case-insensitive values: `status`, `in_stock`, `has_discount`

| Input Value | Result |
|-------------|--------|
| `1`, `true`, `yes`, `publish`, `approved` | `true` |
| `0`, `false`, `no`, (empty), anything else | `false` |

---

## Attributes System

There is NO attributes sheet. All attributes come from `product_variants.attributes`.

Format:

```
EnglishName|ArabicName:EnglishValue|ArabicValue-EnglishName|ArabicName:EnglishValue|ArabicValue
```

**Example:**

```
Color|اللون:Red|احمر-Size|المقاس:L|كبير
```

**English-only format** (single language):

```
AttributeName:Value-AttributeName:Value
```

**Process:**

1. Split by `-` → `["Color|اللون:Red|احمر", "Size|المقاس:L|كبير"]`
2. Split each group by `:` → `["Color|اللون", "Red|احمر"]`
3. Split by `|` to extract language parts:
   - Name: `English = "Color"`, `Arabic = "اللون"`
   - Value: `English = "Red"`, `Arabic = "احمر"`
4. Search attribute:
   - If exists → use it
   - If not → create attribute
5. Search attribute value:
   - If exists → use it
   - If not → create attribute value
6. Create `attribute_product` relation

**Single-language fallback:** If no `|` separator is present, the entire string is treated as English. Ensures backward compatibility with `Name:Value` format.

**Any attribute name is supported** — e.g. `Color`, `Size`, `Material`, `Fabric`, `Style`, etc. Names and values are case-sensitive but slugs are normalized.

---

## Product Calculation Logic

The importer MUST NOT calculate product values manually.

All price calculations use the same service used in dashboard product creation (`ProductPricingService`).

```
Excel Data
    ↓
ProductPricingService
    ↓
Business Calculations
    ↓
Save Product
```

**Calculated fields** (never read from Excel):

| Field                 | Rule                                       |
|-----------------------|--------------------------------------------|
| `price_after_discount` | Computed from `price` + `discount_type` + `discount_amount` |
| `price_after_flash_sale` | Computed from `price` + active flash sale rules |

**Example:**

| Input | Value |
|-------|-------|
| `price` | 1000 |
| `discount_type` | percentage |
| `discount_amount` | 20 |

System calculates:

```
discount    = 200
final_price = 800
```

No duplicated calculation logic exists inside the import — the system delegates entirely to `ProductPricingService`, which is the same service called during admin panel product creation.

---

## Import Process

```
Upload Excel
    ↓
Validate
    ↓
Import Products (process rows one by one, upsert by SKU)
    ↓
Calculate Prices (via ProductPricingService)
    ↓
Import Variants (upsert by matching fields)
    ↓
Generate Attributes (auto-create missing attributes/values)
    ↓
Import Images (download + validate + attach via Media Library)
    ↓
Sync Relations (categories, brands, flash_sales, sliders)
    ↓
Finalize Variants (delete variants not in the Excel)
    ↓
Finish
```

---

## Image Download Details

Images are downloaded through `UrlImageHandler` which:

1. Normalizes Google Drive URLs to direct download format
2. Validates the URL is a public, non-private IP address
3. Downloads with 30-second timeout
4. Validates MIME type (jpeg, png, webp, gif, svg+xml only)
5. Validates file size (max 5MB)
6. Saves to `storage/app/temp/` before attaching via Spatie Media Library
7. If download fails for a Google Drive URL, automatically retries with the thumbnail endpoint

**Backward compatibility:** The import also supports the old pipe-delimited `images` column format (multiple URLs separated by `|`), falling back to it if the single `image` column is empty.

---

## Error Handling

Any failed row is stored with:

| Field | Description |
|-------|-------------|
| `sheet` | Source sheet name (`products`, `product_variants`, `images`, etc.) |
| `row` | Excel row number |
| `sku` | Product SKU |
| `error_message` | Error description |

**Errors tracked:**

| Source | Tracked? |
|--------|----------|
| Product row processing failure | Yes |
| Variant row processing failure | Yes |
| Variant product SKU not found | Yes |
| **Image download failure** | **Yes** |
| **Image invalid URL** | **Yes** |
| **Image product SKU not found** | **Yes** |
| Category/brand/flash sale/slider sync failure | Log only |

Download error report as Excel:

`GET /api/v1/products/import/{id}/download-errors`

---

## Database Relations

```
products

  1:N

product_variants


product_variants

  M:N (through attribute_product)

attribute_values


products

  M:N categories (via category_product)
  M:N brands     (via brand_product)
  M:N flash_sales (via flash_sale_products)
  M:N sliders    (via slider_product)
  1:N media      (Spatie Media Library)
```
