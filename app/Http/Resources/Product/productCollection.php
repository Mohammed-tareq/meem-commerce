<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Request;
use App\Http\Resources\Product\ProductMiniResource;
use Marvel\Http\Resources\product\ProductCollection as MarvelProductCollection;

class ProductCollection extends MarvelProductCollection
{

    public function toArray($request)
    {
        return [
            "data" => ProductMiniResource::collection($this->collection),
            "links" => [
                "current_page"   => $this->currentPage(),
                "from"           => $this->firstItem(),
                "to"             => $this->lastItem(),
                "last_page"      => $this->lastPage(),
                "path"           => $request->url(),
                "per_page"       => $this->perPage(),
                "total"          => $this->total(),
                "next_page_url"  => $this->nextPageUrl(),
                "prev_page_url"  => $this->previousPageUrl(),
                "last_page_url"  => $this->url($this->lastPage()),
                "first_page_url" => $this->url(1),
            ]
        ];
    }
} 