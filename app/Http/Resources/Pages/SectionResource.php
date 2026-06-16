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
            'endpoint' => $this->buildEndpoint(),
            'order' => $this->order,
            'with_product' => $this->with_product,
            'setting' => $this->setting,
        ];
    }
    private function buildEndpoint(): string
    {
        $params = [
            ...($this->setting['back'] ?? [])
        ];

        if ($this->with_product) {
            $params['with_product'] = 'true';
        }

        return 'general/' . $this->type . '?' . http_build_query($params);
    }
}
