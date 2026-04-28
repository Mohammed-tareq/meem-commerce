<?php

namespace App\Services\General;

use Marvel\Database\Models\Slider;

class SliderService
{

    public function getSliders()
    {
        $sliders = Slider::active()->get();
        return $sliders;
    }
}
