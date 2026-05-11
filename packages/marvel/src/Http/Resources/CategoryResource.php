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
            $this->mergeWhen(request()->routeIs('categories.show'), [
                // 'parent' => CategoryResource::collection($this?->whenLoaded('parent')),
                'children' => CategoryResource::collection($this->whenLoaded('children')),
            ]),
            $this->mergeWhen(request()->routeIs('home'), [
                // 'children' => $this->getChildren(),
                'parent' => $this->parent?->getTranslation('name', app()->getLocale()),
            ]),
        ];
    }


    private function getChildren()
    {
        return $this->whenLoaded('children', function () {
            return $this->children->map(function ($child) {
                return [
                    'id' => $child->id,
                    'name' => $child->getTranslation('name', app()->getLocale()),
                    'slug' => $child->slug,
                    'image' => $child->getFirstMediaUrl('categories'),
                    'children' => $child->relationLoaded('children')
                        ? $child->children->map(function ($grandchild) {
                            return [
                                'id' => $grandchild->id,
                                'name' => $grandchild->getTranslation('name', app()->getLocale()),
                                'slug' => $grandchild->slug,
                                'image' => $grandchild->getFirstMediaUrl('categories'),
                            ];
                        })
                        : [],
                ];
            });
        }, []);
    }
}