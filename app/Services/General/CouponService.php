<?php

namespace App\Services\General;

use Illuminate\Support\Facades\DB;
use Marvel\Database\Models\Coupon;
use Marvel\Services\Pricing\ProductPricingService;

class CouponService
{
    public function getCoupons($request)
    {
        $name = $request->get("search", false);
        $coupons = Coupon::valid()->with('media')->when($name, function ($query) use ($name) {
            $query->search('name', $name, app()->getLocale());
        })->get();
        return $coupons;
    }

    public function calcPrice(Coupon $coupon, $price)
    {
        return $coupon->calcPrice($price);
    }

    public function calcPriceByCode(string $code, $price): ?float
    {
        $coupon = Coupon::valid()->where('code', $code)->first();

        if (!$coupon) {
            return null;
        }

        return $coupon->calcPrice($price);
    }

    public function findByCode(string $code): ?Coupon
    {
        return Coupon::valid()->where('code', $code)->first();
    }

    public function addCouponToCart($code)
    {
        return DB::transaction(function () use ($code) {
            $coupon = $this->findByCode($code);

            if (!$coupon) {
                return null;
            }

            $user = auth()->user();

            if (!$user || !$user->cart) {
                return null;
            }

            $cart = $user->cart;

            $couponUsage = $this->CheckCouponUsage($coupon->id);

            if ($couponUsage) {
                return null;
            }

            if (!$this->recordCouponUsage($coupon)) {
                return null;
            }


            $result = $this->updateCartTotalPrice($cart, $coupon);
            return $result;
        });
    }
    private function CheckCouponUsage($couponId)
    {
        $user = auth()->user();

        if (!$user) {
            return null;
        }

        $couponUsage = $user->couponUsages()->where('coupon_id', $couponId)->first();

        if ($couponUsage) {
            return null;
        }
        return $couponUsage;
    }

    private function recordCouponUsage($coupon)
    {
        $user = auth()->user();

        $couponUsage = $user->couponUsages()->firstOrCreate([
            'coupon_id' => $coupon->id,
        ]);

        if (!$couponUsage->wasRecentlyCreated) {
            return false;
        }

        $coupon->increment('used');

        return true;
    }

    private function updateCartTotalPrice($cart, $coupon)
    {
        $totalPriceForCart = app(ProductPricingService::class)->calculateCouponPrice($coupon, $cart->total_price);
        $cart->forceFill([
            'coupon' => $coupon->code,
        ])->save();

        return [
            'total_price' => $totalPriceForCart,
            'coupon_discount' => round((float) $cart->total_price - (float) $totalPriceForCart, 2),
        ];
    }
}
