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
            'parent_id'            => $this->parent_id,
            'level'                => $this->level,
            'image'                => $this->getFirstMediaUrl('categories'),
            'products_count'       => $this->whenCounted('products'),
            'details'              => $this?->getTranslation('details', app()->getLocale()),
            $this->mergeWhen(request()->routeIs('categories.show'), [
                'children' => CategoryResource::collection($this->whenLoaded('children')),
            ]),
            $this->mergeWhen(request()->routeIs('home'), [
                'parent' => $this->parent?->getTranslation('name', app()->getLocale()),
            ]),
            $this->mergeWhen(request()->routeIs('general-category-show'), [
                'product' => $this->parent?->getTranslation('name', app()->getLocale()),
            ]),

        ];
    }
}
