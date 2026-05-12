<?php

namespace App\Http\Resources\Category;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryWithChildNameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $firstChild = $this->children->first();
        $childrenNames = $firstChild
            ? $firstChild->children->map(function ($child) {
                return [
                    'id' => $child->id,
                    'name' => $child->name,
                ];
            })->values()->toArray()
            : [];

        return [
            'id'   => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'child' => $firstChild ? [
                'id'             => $firstChild->id,
                'name'           => $firstChild->name,
                'slug'           => $firstChild->slug ?? null,
                'children_names' => $childrenNames,
            ] : null,
        ];
    }
}
