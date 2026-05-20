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
        $level = (int) ($this->level ?? 0);

        return [
            'id' => $this->id,
            'name' => $this->getTranslation('name', app()->getLocale()),
            'slug' => $this->slug,
            'level' => $level,
            'image' => [
                'desktop' => $this->getFirstMediaUrl('categories-desktop'),
                'mobile' => $this->getFirstMediaUrl('categories-mobile'),
            ],
            'children' => $level >= 3
                ? []
                : ($this->relationLoaded('children')
                    ? $this->children->map(function ($child) {
                        return self::make($child);
                    })->values()
                    : []),
        ];
    }
}
