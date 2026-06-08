<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
         return [
            "data" => ProductMiniResource::collection($this->collection),
            "links" => [
                "current_page" => $this->currentPage(),
                "from" => $this->firstItem(),
                "to" => $this->lastItem(),
                "last_page" => $this->lastPage(),
                "path" => $request->url(),
                "per_page" => $this->perPage(),
                "total" => $this->total(),
                "next_page_url" => $this->nextPageUrl(),
                "prev_page_url" => $this->previousPageUrl(),
                "last_page_url" => $this->url($this->lastPage()),
                "first_page_url" => $this->url(1),
            ]
        ];
    }
}
