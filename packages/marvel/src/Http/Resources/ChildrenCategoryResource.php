<?php

namespace Marvel\Http\Resources;

use Illuminate\Http\Request;

class ChildrenCategoryResource extends Resource
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
            'name'                 => $this->getTranslation('name',app()->getLocale()),
            'slug'                 => $this->slug,
            'products_count'       => $this->whenCounted('products'),
            'image'                => $this->getMedia('categories')?->map(function ($media) {
                                        return [
                                            'id' => $media->id,
                                            'url' => $media->getUrl(),
                                        ];
                                    }),
            // 'language'             => $this->language,
            // 'translated_languages' => $this->translated_languages,
        ];
    }
}
