<?php

namespace App\Services\General;

use Marvel\Database\Models\Promotion;

class PromotionDataService
{

    public function paginatePromotion($request)
    {
        $limit = $request->get('limit', 10);
        $start_date = $request->query('start_date');
        $end_date   = $request->query('end_date');

        $query = Promotion::query()->valid()->when($start_date, function ($query) use ($start_date) {
            $query->where('created_at', '>=', $start_date);
            })
            ->when($end_date, function ($query) use ($end_date) {
                $query->where('created_at', '<=', $end_date);
            });
        return $query->orderByDesc('id')->paginate($limit);
    }

    public function getPromotionBySlug($slug)
    {
        $Promotion = Promotion::search('slug', $slug, app()->getLocale())->first();
        if ($Promotion) {
            $Promotion->load('products');
        }
        return $Promotion;
    }
}
