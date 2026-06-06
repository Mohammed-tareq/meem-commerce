<?php

namespace App\Http\Resources\Pages;

use Illuminate\Http\Resources\Json\JsonResource;

class SectionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->title_visible ? $this->title : null,
            'endpoint' =>"general/" . $this->endpoint,
            'order' => $this->order,
        ];
    }
}
