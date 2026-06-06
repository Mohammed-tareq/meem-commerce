<?php

namespace App\Services\General;

use Marvel\Database\Models\Brand;

class BrandService
{

    public function getBrands($request)
    {
        $limit = $request->get('limit', 10);
        $start_date = $request->query('start_date');
        $end_date   = $request->query('end_date');

        return Brand::active()
            ->when($start_date, function ($query) use ($start_date) {
                $query->where('created_at', '>=', $start_date);
            })
            ->when($end_date, function ($query) use ($end_date) {
                $query->where('created_at', '<=', $end_date);
            })->orderByDesc('id')->limit($limit)->get();
    }
    public function getBrandBySlug($slug)
    {
        $brand =  Brand::active()->search('slug', $slug, app()->getLocale())->first();
        if ($brand) {
            $brand->load('products');
        }
        return $brand;
    }
    public function getBrandsProductsByQtySet($request)
    {
        $qty = $request->query('limit', 10);
        $qtyBrand = $request->query('limit_brand', 10);
        $start_date = $request->query('start_date', '');
        $end_date   = $request->query('end_date', '');

        $banners = Brand::active()
            ->when($start_date, function ($query) use ($start_date) {
                $query->where('created_at', '>=', $start_date);
            })
            ->when($end_date, function ($query) use ($end_date) {
                $query->where('created_at', '<=', $end_date);
            })
            ->with(['products' => function ($query) use ($qty) {
                $query->limit($qty);
            }])
            ->limit($qtyBrand)
            ->get();

        return $banners;
    }
}
