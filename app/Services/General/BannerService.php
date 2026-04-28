<?php

namespace App\Services\General;

use Marvel\Database\Models\Banner;

class BannerService
{

    public function getBanners()
    {
        return Banner::with('products')->active()->get();
    }
}
