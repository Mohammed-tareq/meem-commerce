<?php

namespace App\Http\Resources\Section;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->title_visible? $this->getTranslation('title',app()->getLocale()) : null,
            'order' => $this->order,
            'endpoint' => $this->endpoint,
            'is_active' => (bool) $this->is_active,
        ];
    }
}
