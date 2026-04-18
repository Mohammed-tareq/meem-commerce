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
            'parent'               => $this->whenLoaded('parent', CategoryResource::make($this->parent)),
            'products_count'       => $this->whenCounted('products'),
            'children'             => $this->whenLoaded('children', ChildrenCategoryResource::collection($this->children)),
            'details'              => $this?->getTranslation('details', app()->getLocale()),
            'shops'                => $this->whenLoaded('shops', ShopResource::collection($this->shops)),


            //            'language'             => $this->language,
            //            'translated_languages' => $this->translated_languages,
            // 'icon'                 => $this?->icon,
            // 'type_id'              => $this->type_id,
            // 'banner_image'         => $this?->banner_image,
            // 'type'                 => getResourceData($this->type, []) // if you need extra data then pass key in array by second parameter
        ];
    }
}
