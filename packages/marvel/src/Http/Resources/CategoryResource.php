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
    public function toArray($request)
    {

        return [
            'id'                   => $this->id,
            'name'                 => $this->getTranslations('name',app()->getLocale()),
            'slug'                 => $this->slug,
//            'language'             => $this->language,
//            'translated_languages' => $this->translated_languages,
            'parent'               => ['name' => $this->parentCategory->getTranslations('name',app()->getLocale()) ?? null],
            'children'             => ChildrenCategoryResource::collection($this->children),
            'products_count'       => $this->products_count,
            'details'              => $this->getTranslations('details',app()->getLocale()),
            'image'                => $this->image,
            'icon'                 => $this->icon,
            // 'type_id'              => $this->type_id,
            'banner_image'         => $this->banner_image,
            // 'type'                 => getResourceData($this->type, []) // if you need extra data then pass key in array by second parameter
        ];
    }
}
