<?php

namespace Marvel\Services\Pricing;

use Carbon\Carbon;
use Marvel\Database\Models\Coupon;
use Marvel\Database\Models\FlashSale;
use Marvel\Database\Models\Product;
use Marvel\Database\Models\ProductVariant;
use Marvel\Enums\DiscountType;
use Marvel\Enums\FlashSaleType;

class ProductPricingService
{
    public function calculateProductPricing(Product $product, ?FlashSale $flashSale = null): array
    {
        return $this->runSafely(function () use ($product, $flashSale): array {
            $basePrice = $this->normalizeMoney($product->price);
            $resolvedFlashSale = $flashSale ?? $this->resolveActiveFlashSale($product);
            $flashSalePrice = $this->calculateFlashSalePrice($resolvedFlashSale, $basePrice);
            $discountPrice = $flashSalePrice === null && $this->isDiscountActive($product)
                ? $this->calculateDiscountedPrice($basePrice, $product->discount_type ?? DiscountType::PERCENTAGE, $product->discount_amount ?? 0)
                : null;

            return [
                'base_price' => $basePrice,
                'price_after_discount' => $discountPrice,
                'price_after_flash_sale' => $flashSalePrice,
                'final_price' => $flashSalePrice ?? $discountPrice ?? $basePrice,
            ];
        }, [
            'base_price' => $this->normalizeMoney($product->price),
            'price_after_discount' => null,
            'price_after_flash_sale' => null,
            'final_price' => $this->normalizeMoney($product->price),
        ]);
    }

    public function calculateProductPricingFromData(array $data, ?FlashSale $flashSale = null): array
    {
        return $this->runSafely(function () use ($data, $flashSale): array {
            $basePrice = $this->normalizeMoney($data['price'] ?? null);
            $flashSalePrice = $this->calculateFlashSalePrice($flashSale, $basePrice);
            $discountPrice = $flashSalePrice === null && $this->isDiscountActiveFromData($data)
                ? $this->calculateDiscountedPrice($basePrice, $data['discount_type'] ?? DiscountType::PERCENTAGE, $data['discount_amount'] ?? 0)
                : null;

            return [
                'base_price' => $basePrice,
                'price_after_discount' => $discountPrice,
                'price_after_flash_sale' => $flashSalePrice,
                'final_price' => $flashSalePrice ?? $discountPrice ?? $basePrice,
            ];
        }, [
            'base_price' => $this->normalizeMoney($data['price'] ?? null),
            'price_after_discount' => null,
            'price_after_flash_sale' => null,
            'final_price' => $this->normalizeMoney($data['price'] ?? null),
        ]);
    }

    public function calculateVariantSalePrice(Product $product, ProductVariant |array $variation, ?FlashSale $flashSale = null): ?float
    {
        return $this->runSafely(function () use ($product, $variation, $flashSale): ?float {
            $basePrice = $this->normalizeMoney($variation instanceof ProductVariant ? $variation->price : ($variation['price'] ?? null));

            if ($basePrice === null) {
                return null;
            }

            $pricing = $this->calculateVariantPricingFromBase(
                $basePrice,
                $product->has_discount,
                $product->discount_type ?? DiscountType::PERCENTAGE,
                $product->discount_amount ?? 0,
                $this->isDiscountActive($product),
                $flashSale ?? $this->resolveActiveFlashSale($product)
            );

            return $pricing['final_price'];
        }, null);
    }

    public function calculateVariantPricingFromData(array $variant, array $productData, ?FlashSale $flashSale = null): array
    {
        return $this->runSafely(function () use ($variant, $productData, $flashSale): array {
            $basePrice = $this->normalizeMoney($variant['price'] ?? null);

            if ($basePrice === null) {
                return [
                    'base_price' => null,
                    'price_after_discount' => null,
                    'price_after_flash_sale' => null,
                    'final_price' => null,
                ];
            }

            return $this->calculateVariantPricingFromBase(
                $basePrice,
                !empty($productData['has_discount']),
                $productData['discount_type'] ?? DiscountType::PERCENTAGE,
                $productData['discount_amount'] ?? 0,
                $this->isDiscountActiveFromData($productData),
                $flashSale
            );
        }, [
            'base_price' => null,
            'price_after_discount' => null,
            'price_after_flash_sale' => null,
            'final_price' => null,
        ]);
    }

    public function calculateProductCurrentPrice(Product $product): ?float
    {
        return $this->runSafely(fn(): ?float => $this->calculateProductPricing($product)['final_price'], $this->normalizeMoney($product->price));
    }

    public function calculateCouponPrice(Coupon $coupon, $basePrice): ?float
    {
        return $this->runSafely(function () use ($coupon, $basePrice): ?float {
            $normalizedBasePrice = $this->normalizeMoney($basePrice);

            if ($normalizedBasePrice === null) {
                return null;
            }

            return $coupon->calcPrice($normalizedBasePrice);
        }, null);
    }

    public function calculateCouponPriceByCode(string $code, $basePrice): ?float
    {
        return $this->runSafely(function () use ($code, $basePrice): ?float {
            $coupon = Coupon::valid()->where('code', $code)->first();

            if (!$coupon) {
                return null;
            }

            return $this->calculateCouponPrice($coupon, $basePrice);
        }, null);
    }

    public function calculateVariantCurrentPrice(Product $product, ProductVariant|array $variation, ?FlashSale $flashSale = null): ?float
    {
        return $this->runSafely(fn(): ?float => $this->calculateVariantSalePrice($product, $variation, $flashSale), null);
    }

    public function resolveActiveFlashSale(Product $product, ?int $flashSaleId = null): ?FlashSale
    {
        return $this->runSafely(function () use ($product, $flashSaleId): ?FlashSale {
            if ($flashSaleId) {
                return FlashSale::query()
                    ->whereKey($flashSaleId)
                    ->valid()
                    ->first();
            }

            return $product->flash_sales()
                ->valid()
                ->orderBy('start_date', 'desc')
                ->first();
        }, null);
    }

    public function calculateDiscountedPrice($price, $discountType, $amount): ?float
    {
        return $this->runSafely(function () use ($price, $discountType, $amount): ?float {
            $normalizedPrice = $this->normalizeMoney($price);

            if ($normalizedPrice === null) {
                return null;
            }

            $discountType = $discountType ?: DiscountType::PERCENTAGE;
            $amount = max(0, (float) $amount);
            $priceUnits = $this->toUnits($normalizedPrice);

            if ($discountType === DiscountType::PERCENTAGE) {
                $amount = min($amount, 100);
                $discountUnits = (int) round($priceUnits * ($amount / 100));

                return $this->toUnits(max(0, $priceUnits - $discountUnits));
            }

            if ($discountType === DiscountType::FIXED_RATE || $discountType === 'fixed') {
                $discountUnits = $this->toUnits($amount);

                return $this->toUnits(max(0, $priceUnits - $discountUnits));
            }

            return $this->toUnits($priceUnits);
        }, null);
    }

    public function calculateFlashSalePrice(?FlashSale $flashSale, $basePrice): ?float
    {
        return $this->runSafely(function () use ($flashSale, $basePrice): ?float {
            $normalizedBasePrice = $this->normalizeMoney($basePrice);

            if ($flashSale === null || $normalizedBasePrice === null || !$this->isFlashSaleActive($flashSale)) {
                return null;
            }

            $baseUnits = $this->toUnits($normalizedBasePrice);
            $discountUnits = $this->resolveFlashSaleDiscountUnits($flashSale, $baseUnits);

            if ($discountUnits === null) {
                return $this->toUnits($baseUnits);
            }

            return $this->toUnits(max(0, $baseUnits - $discountUnits));
        }, null);
    }

    private function calculateVariantPricingFromBase(
        float $basePrice,
        $hasDiscount,
        $discountType,
        $discountAmount,
        bool $discountActive,
        ?FlashSale $flashSale
    ): array {
        $flashSalePrice = $this->calculateFlashSalePrice($flashSale, $basePrice);
        $discountPrice = $flashSalePrice === null && $discountActive && $hasDiscount
            ? $this->calculateDiscountedPrice($basePrice, $discountType, $discountAmount)
            : null;

        return [
            'base_price' => $basePrice,
            'price_after_discount' => $discountPrice,
            'price_after_flash_sale' => $flashSalePrice,
            'final_price' => $flashSalePrice ?? $discountPrice ?? $basePrice,
        ];
    }

    private function resolveFlashSaleDiscountUnits(FlashSale $flashSale, float $baseUnits): ?float
    {
        $discountUnits = max(0, $this->toUnits($flashSale->discount ?? 0));
        $maxDiscountUnits = $flashSale->max_discount_amount !== null
            ? max(0, $this->toUnits($flashSale->max_discount_amount))
            : null;

        if ($flashSale->type === FlashSaleType::PERCENTAGE) {
            $percentDiscountUnits = round($baseUnits * ($discountUnits / 100));

            return $maxDiscountUnits  === null
                ? $percentDiscountUnits
                : min($percentDiscountUnits, $maxDiscountUnits);
        }

        if ($flashSale->type === FlashSaleType::FIXED_RATE) {
            return max(0, $baseUnits - $discountUnits);
        }

        if ($flashSale->type === FlashSaleType::FINAL_PRICE) {
            return $discountUnits;
        }

        return null;
    }

    private function isFlashSaleActive(?FlashSale $flashSale): bool
    {
        if (!$flashSale || !$flashSale->status) {
            return false;
        }

        $today = Carbon::today();

        if ($flashSale->start_date && Carbon::parse($flashSale->start_date)->gt($today)) {
            return false;
        }

        if ($flashSale->end_date && Carbon::parse($flashSale->end_date)->lt($today)) {
            return false;
        }

        return true;
    }

    private function isDiscountActive($product): bool
    {
        if (!$product || empty($product->has_discount)) {
            return false;
        }

        if (isset($product->discount_status) && $product->discount_status === false) {
            return false;
        }

        $today = Carbon::now();

        if (!empty($product->start_date) && Carbon::parse($product->start_date)->gt($today)) {
            return false;
        }

        if (!empty($product->end_date) && Carbon::parse($product->end_date)->lt($today)) {
            return false;
        }

        return true;
    }

    private function isDiscountActiveFromData(array $data): bool
    {
        if (empty($data['has_discount'])) {
            return false;
        }

        if (array_key_exists('discount_status', $data) && $data['discount_status'] === false) {
            return false;
        }

        $today = Carbon::now();

        if (!empty($data['start_date']) && Carbon::parse($data['start_date'])->gt($today)) {
            return false;
        }

        if (!empty($data['end_date']) && Carbon::parse($data['end_date'])->lt($today)) {
            return false;
        }

        return true;
    }

    private function normalizeMoney($amount): ?float
    {
        if ($amount === null || $amount === '') {
            return null;
        }

        return round((float) $amount, 2);
    }

    private function runSafely(callable $callback, $fallback)
    {
        try {
            return $callback();
        } catch (\Throwable $throwable) {
            report($throwable);

            return $fallback;
        }
    }

    private function toUnits($amount): float
    {
        return (float) $amount;
    }
}
