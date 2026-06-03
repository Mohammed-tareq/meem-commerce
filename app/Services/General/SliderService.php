<?php

namespace App\Services\General;

use Marvel\Database\Models\Slider;

class SliderService
{

    public function getSliders($request)
    {
        $start_date = $request->query('start_date');
        $end_date   = $request->query('end_date');
        $limit = $request->get('limit', 10);

        $sliders = Slider::active()
            ->when($start_date, function ($query) use ($start_date) {
                $query->where('created_at', '>=', $start_date);
            })
            ->when($end_date, function ($query) use ($end_date) {
                $query->where('created_at', '<=', $end_date);
            })
            ->limit($limit)
            ->get();

            return $sliders;
    }
}
