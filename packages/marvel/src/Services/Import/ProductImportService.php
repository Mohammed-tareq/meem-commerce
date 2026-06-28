<?php

namespace Marvel\Services\Import;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Marvel\Database\Models\Attribute;
use Marvel\Database\Models\AttributeProduct;
use Marvel\Database\Models\AttributeValue;
use Marvel\Database\Models\Brand;
use Marvel\Database\Models\Category;
use Marvel\Database\Models\FlashSale;
use Marvel\Database\Models\Product;
use Marvel\Database\Models\ProductVariant;
use Marvel\Database\Models\Slider;
use Marvel\Enums\DiscountType;
use Marvel\Enums\ProductType;
use Marvel\Services\Import\ImageHandlers\UrlImageHandler;
use Marvel\Services\Pricing\ProductPricingService;

class ProductImportService
{
    protected ?UrlImageHandler $urlHandler = null;

    protected array $failedRows = [];

    protected int $successCount = 0;

    protected array $keptVariantIds = [];

    public function __construct()
    {
        $this->urlHandler = new UrlImageHandler();
    }

    public function getFailedRows(): array
    {
        return $this->failedRows;
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function processProductRow(array $row, int $rowIndex): void
    {
        try {
            DB::beginTransaction();

            $sku = $row['sku'] ?? null;
            $product = null;

            if (!empty($sku)) {
                $product = Product::where('sku', $sku)->first();
            }

            $data = $this->buildProductData($row);

            if ($product) {
                $data['slug'] = $product->slug;
                $product->update($data);
            } else {
                $data['slug'] = $this->generateSlug($row, $product->id ?? null);
                $product = Product::create($data);
            }

            if (!empty($sku) && $product->sku !== $sku) {
                $product->sku = $sku;
                $product->saveQuietly();
            }

            $pricing = app(ProductPricingService::class)->calculateProductPricingFromData(
                $product->toArray(),
                $product->getActiveFlashSale()
            );
            $product->update([
                'price_after_discount' => $pricing['price_after_discount'] ?? null,
                'price_after_flash_sale' => $pricing['price_after_flash_sale'] ?? null,
            ]);

            DB::commit();
            $this->successCount++;
        } catch (Exception $e) {
            DB::rollBack();
            $this->failedRows[] = [
                'sheet' => 'products',
                'row' => $rowIndex,
                'sku' => $row['sku'] ?? 'N/A',
                'error_message' => $e->getMessage(),
            ];
            Log::error("Product import row {$rowIndex} failed: " . $e->getMessage());
        }
    }

    public function processVariantRow(array $row, int $rowIndex): void
    {
        $productSku = $row['product_sku'] ?? null;
        if (empty($productSku)) {
            return;
        }

        $product = Product::where('sku', $productSku)->first();
        if (!$product) {
            $this->failedRows[] = [
                'sheet' => 'product_variants',
                'row' => $rowIndex,
                'sku' => $productSku,
                'error_message' => "Product with SKU '{$productSku}' not found",
            ];
            return;
        }

        try {
            DB::beginTransaction();

            $variant = $this->findVariantByFields($product->id, $row);

            $variantData = [
                'product_id' => $product->id,
                'sku' => $row['variant_sku'] ?? null,
                'price' => (float) ($row['price'] ?? 0),
                'sale_price' => isset($row['sale_price']) && $row['sale_price'] !== '' ? (float) $row['sale_price'] : null,
                'stock_quantity' => (int) ($row['quantity'] ?? 0),
                'quantity' => (int) ($row['quantity'] ?? 0),
                'in_stock' => $this->parseBoolean($row['in_stock'] ?? true),
                'height' => $row['height'] ?? null,
                'width' => $row['width'] ?? null,
                'length' => $row['length'] ?? null,
                'weight' => $row['weight'] ?? null,
            ];

            if ($variant) {
                $variant->update($variantData);
                $variant->attributeProducts()->delete();
            } else {
                $variant = ProductVariant::create($variantData);
            }

            $this->attachVariantAttributes($variant, $row);

            $this->keptVariantIds[$product->id][] = $variant->id;

            DB::commit();

            $product->product_type = ProductType::VARIABLE;
            $product->save();

            $this->successCount++;
        } catch (Exception $e) {
            DB::rollBack();
            $this->failedRows[] = [
                'sheet' => 'product_variants',
                'row' => $rowIndex,
                'sku' => $productSku,
                'error_message' => $e->getMessage(),
            ];
            Log::error("Variant import row {$rowIndex} for SKU {$productSku} failed: " . $e->getMessage());
        }
    }

    protected function findVariantByFields(int $productId, array $row): ?ProductVariant
    {
        $query = ProductVariant::where('product_id', $productId)
            ->where('price', (float) ($row['price'] ?? 0));

        foreach (['height', 'width', 'length', 'weight'] as $field) {
            if (isset($row[$field]) && $row[$field] !== '') {
                $query->where($field, $row[$field]);
            } else {
                $query->whereNull($field);
            }
        }

        if (isset($row['sale_price']) && $row['sale_price'] !== '') {
            $query->where('sale_price', (float) $row['sale_price']);
        } else {
            $query->whereNull('sale_price');
        }

        return $query->first();
    }

    public function finalizeVariants(): void
    {
        foreach ($this->keptVariantIds as $productId => $variantIds) {
            ProductVariant::where('product_id', $productId)
                ->whereNotIn('id', $variantIds)
                ->delete();
        }
    }

    protected function attachVariantAttributes(ProductVariant $variant, array $row): void
    {
        $attributesString = $row['attributes'] ?? '';

        if (empty(trim($attributesString))) {
            return;
        }

        $groups = explode('-', $attributesString);

        foreach ($groups as $group) {
            $group = trim($group);
            if (empty($group)) {
                continue;
            }

            $parts = explode(':', $group, 2);
            if (count($parts) !== 2) {
                continue;
            }

            $namePart = trim($parts[0]);
            $valuePart = trim($parts[1]);

            if (empty($namePart) || empty($valuePart)) {
                continue;
            }

            $nameLanguages = explode('|', $namePart, 2);
            $valueLanguages = explode('|', $valuePart, 2);

            $enName = trim($nameLanguages[0]);
            $arName = trim($nameLanguages[1] ?? '');
            $enValue = trim($valueLanguages[0]);
            $arValue = trim($valueLanguages[1] ?? '');

            if (empty($enName)) {
                continue;
            }

            $attribute = Attribute::where('name->en', $enName)
                ->when($arName, fn($q) => $q->where('name->ar', $arName))
                ->first();

            if (!$attribute) {
                $name = ['en' => $enName];
                if ($arName) {
                    $name['ar'] = $arName;
                }
                $attribute = Attribute::create(['name' => $name]);
            }

            $attributeValue = AttributeValue::where('attribute_id', $attribute->id)
                ->where('value->en', $enValue)
                ->when($arValue, fn($q) => $q->where('value->ar', $arValue))
                ->first();

            if (!$attributeValue) {
                $value = ['en' => $enValue];
                if ($arValue) {
                    $value['ar'] = $arValue;
                }
                $attributeValue = AttributeValue::create([
                    'attribute_id' => $attribute->id,
                    'value' => $value,
                ]);
            }

            AttributeProduct::firstOrCreate([
                'product_variant_id' => $variant->id,
                'attribute_value_id' => $attributeValue->id,
            ]);
        }
    }

    public function processProductImage(string $productSku, string $imageUrl): void
    {
        Log::info("Processing image for SKU {$productSku}: {$imageUrl}");

        $product = Product::where('sku', $productSku)->first();
        if (!$product) {
            Log::warning("Product SKU {$productSku} not found for image import");
            return;
        }

        $imageUrl = trim($imageUrl);
        if (empty($imageUrl)) {
            return;
        }

        try {
            if ($this->urlHandler && $this->urlHandler->isValidUrl($imageUrl)) {
                $downloaded = $this->urlHandler->download($imageUrl);
                if ($downloaded) {
                    $this->urlHandler->attachToModel($product, $downloaded, 'products');
                    $this->urlHandler->cleanup($downloaded);
                }
            } elseif (file_exists($imageUrl)) {
                $product->addMedia($imageUrl)
                    ->toMediaCollection('products');
                Log::info("Imported local image for SKU {$productSku}: {$imageUrl}");
            } else {
                Log::warning("Invalid image source skipped for SKU {$productSku}: {$imageUrl}");
            }
        } catch (Exception $e) {
            Log::warning("Image import for SKU {$productSku} failed: " . $e->getMessage());
        }
    }

    public function syncCategories(string $productSku, array $categorySlugs): void
    {
        $product = Product::where('sku', $productSku)->first();
        if (!$product) {
            Log::warning("syncCategories: Product SKU {$productSku} not found");
            return;
        }

        $categoryIds = Category::whereIn('slug', $categorySlugs)->pluck('id')->toArray();
        if (!empty($categoryIds)) {
            $product->categories()->sync($categoryIds);
            Log::info("syncCategories: Synced {$productSku} to categories: " . implode(',', $categoryIds));
        } else {
            Log::warning("syncCategories: No matching categories for slugs: " . implode(',', $categorySlugs));
        }
    }

    public function syncBrands(string $productSku, array $brandSlugs): void
    {
        $product = Product::where('sku', $productSku)->first();
        if (!$product) {
            Log::warning("syncBrands: Product SKU {$productSku} not found");
            return;
        }

        $brandIds = Brand::whereIn('slug', $brandSlugs)->pluck('id')->toArray();
        if (!empty($brandIds)) {
            $product->brands()->sync($brandIds);
            Log::info("syncBrands: Synced {$productSku} to brands: " . implode(',', $brandIds));
        } else {
            Log::warning("syncBrands: No matching brands for slugs: " . implode(',', $brandSlugs));
        }
    }

    public function syncFlashSales(string $productSku, array $flashSaleSlugs): void
    {
        $product = Product::where('sku', $productSku)->first();
        if (!$product) {
            Log::warning("syncFlashSales: Product SKU {$productSku} not found");
            return;
        }

        $flashSaleIds = FlashSale::whereIn('slug', $flashSaleSlugs)->pluck('id')->toArray();
        if (!empty($flashSaleIds)) {
            $product->flash_sales()->sync($flashSaleIds);
            Log::info("syncFlashSales: Synced {$productSku} to flash sales: " . implode(',', $flashSaleIds));
        } else {
            Log::warning("syncFlashSales: No matching flash sales for slugs: " . implode(',', $flashSaleSlugs));
        }
    }

    public function syncSliders(string $productSku, array $sliderSlugs): void
    {
        $product = Product::where('sku', $productSku)->first();
        if (!$product) {
            Log::warning("syncSliders: Product SKU {$productSku} not found");
            return;
        }

        $sliderIds = Slider::whereIn('slug', $sliderSlugs)->pluck('id')->toArray();
        if (!empty($sliderIds)) {
            $product->sliders()->sync($sliderIds);
            Log::info("syncSliders: Synced {$productSku} to sliders: " . implode(',', $sliderIds));
        } else {
            Log::warning("syncSliders: No matching sliders for slugs: " . implode(',', $sliderSlugs));
        }
    }

    protected function buildProductData(array $row): array
    {
        $data = [];

        $name = [];
        if (!empty($row['name_en'])) {
            $name['en'] = $row['name_en'];
        }
        if (!empty($row['name_ar'])) {
            $name['ar'] = $row['name_ar'];
        }
        if (!empty($name)) {
            $data['name'] = $name;
        }

        $description = [];
        if (!empty($row['description_en'])) {
            $description['en'] = $row['description_en'];
        }
        if (!empty($row['description_ar'])) {
            $description['ar'] = $row['description_ar'];
        }
        if (!empty($description)) {
            $data['description'] = $description;
        }

        if (isset($row['price'])) {
            $data['price'] = (float) $row['price'];
        }

        if (isset($row['product_type'])) {
            $data['product_type'] = in_array($row['product_type'], ProductType::getValues())
                ? $row['product_type']
                : ProductType::SIMPLE;
        }

        if (isset($row['quantity'])) {
            $data['stock_quantity'] = (int) $row['quantity'];
            $data['quantity'] = (int) $row['quantity'];
        }

        if (isset($row['status'])) {
            $data['status'] = $this->parseBoolean($row['status']);
        }

        if (isset($row['in_stock'])) {
            $data['in_stock'] = $this->parseBoolean($row['in_stock']);
        }

        if (isset($row['has_discount'])) {
            $data['has_discount'] = $this->parseBoolean($row['has_discount']);
        }

        if (isset($row['discount_type'])) {
            $data['discount_type'] = in_array($row['discount_type'], DiscountType::getValues())
                ? $row['discount_type']
                : DiscountType::PERCENTAGE;
        }

        if (isset($row['discount_amount'])) {
            $data['discount_amount'] = (float) $row['discount_amount'];
        }

        if (!empty($row['start_date'])) {
            $data['start_date'] = Carbon::parse($row['start_date'])->format('Y-m-d');
        }

        if (!empty($row['end_date'])) {
            $data['end_date'] = Carbon::parse($row['end_date'])->format('Y-m-d');
        }

        $dimensionFields = ['height', 'width', 'length', 'weight'];
        foreach ($dimensionFields as $field) {
            if (isset($row[$field]) && $row[$field] !== '') {
                $data[$field] = (string) $row[$field];
            }
        }

        if (isset($row['pieces'])) {
            $data['pieces'] = (int) $row['pieces'];
        }

        if (isset($row['has_flash_sale'])) {
            $data['has_flash_sale'] = $this->parseBoolean($row['has_flash_sale']);
        }

        return $data;
    }

    protected function generateSlug(array $row, ?int $existingId = null): string
    {
        $baseSlug = Str::slug($row['name_en'] ?? $row['sku'] ?? 'product-' . Str::random(6));
        $slug = $baseSlug;
        $count = 1;

        while (Product::where('slug', $slug)->when($existingId, fn($q, $id) => $q->where('id', '!=', $id))->exists()) {
            $slug = $baseSlug . '-' . $count++;
        }

        return $slug;
    }

    protected function parseBoolean($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        if (is_numeric($value)) {
            return (int) $value === 1;
        }
        if (is_string($value)) {
            return in_array(strtolower($value), ['1', 'true', 'yes', 'publish', 'approved']);
        }
        return false;
    }
}
