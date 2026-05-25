<?php

namespace Marvel\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'governorate_id' => $this->governorate_id,
            'name' => $this->name,
            'name_translated' => method_exists($this, 'getTranslation')
                ? $this->getTranslation('name', app()->getLocale(), false)
                : $this->name,
            'governorate' => new GovernorateResource($this->whenLoaded('governorate')),
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
