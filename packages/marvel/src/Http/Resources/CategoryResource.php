<?php

namespace Marvel\Http\Resources;

use Illuminate\Http\Request;


class CategoryResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray(Request $request)
    {
        return [
            'id'                   => $this->id,
            'name'                 => $this->getTranslation('name', app()->getLocale()),
            'slug'                 => $this->slug,
            'image'                => $this->getFirstMediaUrl('categories'),
            'products_count'       => $this->whenCounted('products'),
            'details'              => $this?->getTranslation('details', app()->getLocale()),
            $this->mergeWhen(!request()->routeIs('general-category-index'), [
                'parent'               => $this->whenLoaded('parent', CategoryResource::make($this->parent)),
                'children'             => $this->whenLoaded('children', ChildrenCategoryResource::collection($this->children)),
                'products'                 => $this->whenLoaded('products', ProductResource::collection($this->products)),
            ]),

        ];
    }
}
