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
        $excludedRoutes = [
            'general-category-index',
            'categories.index',
            'categories.update',
            'categories.store',
        ];
        return [
            'id'                   => $this->id,
            'name'                 => $this->getTranslation('name', app()->getLocale()),
            'slug'                 => $this->slug,
            'image'                => $this->getFirstMediaUrl('categories'),
            'products_count'       => $this->whenCounted('products'),
            'details'              => $this->getTranslation('details', app()->getLocale()),
            $this->mergeWhen(
                !request()->routeIs($excludedRoutes),
                [
                    'parent' => !$this->parent_id ? null :
                        $this->whenLoaded('parent', [
                            'id'   => $this->parent->id,
                            'name' => $this->parent->getTranslation('name', app()->getLocale()),
                            'slug' => $this->parent->slug,
                        ]),
                    'children' => $this->whenLoaded('children', function () {
                        return $this->children->map(function ($child) {
                            return [
                                'id'   => $child->id,
                                'name' => $child->getTranslation('name', app()->getLocale()),
                                'slug' => $child->slug,
                            ];
                        });
                    }),

                ]
            ),
            // 'products'                 => $this->whenLoaded('products', ProductResource::collection($this->products)),

        ];
    }
}