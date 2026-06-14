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
            'image'                => [
                'desktop' => $this->getFirstMediaUrl('categories-desktop') ?: null,
                'mobile'  => $this->getFirstMediaUrl('categories-mobile') ?: null,
            ],
            'products_count'       => $this->whenCounted('products'),
            $this->mergeWhen($this->getTranslation('details', app()->getLocale()), [
                'details' => $this->getTranslation('details', app()->getLocale()),
            ]),
            $this->mergeWhen($this->relationLoaded('children') && $this->children->isNotEmpty(), [
                'children' => ChildrenCategoryResource::collection($this->children),
            ]),
        ];
    }
}
