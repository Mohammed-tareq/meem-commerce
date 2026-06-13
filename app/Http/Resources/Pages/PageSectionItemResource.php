<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageSectionItemResource extends JsonResource
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

            'name' => $this->entity->name,

            'image' => $this->entity->image,

            'entity_type' => $this->entity_type,

            'action' => [
                'type' => $this->action_type,
                'id' => $this->action_id,
            ],
        ];
    }
}
