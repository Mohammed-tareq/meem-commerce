<?php

namespace App\Services\General;

use Marvel\Database\Models\Banner;

class BannerService
{

    public function getBanners($request)
    {
        $limit = $request->get('limit', 10);
        $start_date = $request->query('start_date');
        $end_date   = $request->query('end_date');
        $bannersId = $request->query('bannersId');
        $order = $request->query('order', 'desc');

        $query = Banner::active()
            ->when($start_date, function ($query) use ($start_date) {
                $query->where('created_at', '>=', $start_date);
            })
            ->when($end_date, function ($query) use ($end_date) {
                $query->where('created_at', '<=', $end_date);
            });

        if (!empty($bannersId)) {
            $ids = is_array($bannersId) ? $bannersId : explode(',', $bannersId);
            $ids = array_filter($ids, 'is_numeric');
            if (!empty($ids)) {
                $query->whereIn('id', $ids);
            }
        }

        return $query->orderBy('id', $order)->limit($limit)->get();
    }
    public function getBannerBySlug($slug)
    {
        $banner =  Banner::active()->search('slug', $slug, app()->getLocale())->first();
        if ($banner) {
            $banner->load('products');
        }
        return $banner;
    }

    
}
