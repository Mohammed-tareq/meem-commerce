<?php

namespace App\Services\General;

use Marvel\Database\Models\Banner;

class BannerService
{

    public function getBanners()
    {
        return Banner::active()->get();
    }
    public function getBannerById($id)
    {
        $banner =  Banner::active()->find($id);
        return $banner->load('products');
    }

}