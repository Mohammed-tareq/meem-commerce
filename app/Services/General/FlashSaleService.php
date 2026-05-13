<?php

namespace App\Services\General;

use Marvel\Database\Models\FlashSale;

class FlashSaleService
{

public function paginateFlashSales($request)
    {
        $limit = $request->get('limit', 10);
        $query = FlashSale::query()->valid();
        return $query->orderByDesc('id')->paginate($limit);
    }

    public function getFlashSaleById($id)
    {
        $FlashSale = FlashSale::find($id);
        $FlashSale->load('products');
        return $FlashSale;
    }
}
