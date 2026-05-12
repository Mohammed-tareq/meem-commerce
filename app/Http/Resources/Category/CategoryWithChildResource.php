<?php

namespace App\Http\Resources\Category;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryWithChildResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'name'                 => $this->getTranslation('name', app()->getLocale()),
            'slug'                 => $this->slug,
            'image'                => $this->getFirstMediaUrl('categories'),
            'products_count'       => $this->whenCounted('products'),
            'details'              => $this?->getTranslation('details', app()->getLocale()),
            'children' => CategoryWithChildResource::collection($this->whenLoaded('children')),
        ];
    }
}
