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
            'details'              => $this->getTranslation('details', app()->getLocale()),
            $this->mergeWhen(request()->routeIs('categories.show'), [
                'parent' => CategoryResource::collection($this->whenLoaded('parent')),
                'children' => CategoryResource::collection($this->whenLoaded('children')),
            ]),
        ];
    }
}