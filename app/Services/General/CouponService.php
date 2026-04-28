<?php

namespace App\Services\General;

use Marvel\Database\Models\Coupon;

class CouponService
{
    public function getCoupons($request)
    {
        $name = $request->get("search", false);
        $coupons = Coupon::active()->valid()->when($name, function ($query) use ($name) {
            $query->where('name', 'like', '%' . $name . '%');
        })->get();
        return $coupons;
    }
}
