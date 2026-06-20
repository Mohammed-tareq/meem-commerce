<?php

namespace App\Http\Resources\Pages;

use Illuminate\Http\Resources\Json\JsonResource;
use Marvel\Database\Models\SectionType;

class SectionResource extends JsonResource
{
    // private ?array $resolvedSettings = null;

    // private function getSettings(): array
    // {
    //     if ($this->resolvedSettings !== null) {
    //         return $this->resolvedSettings;
    //     }

    //     $sectionType = SectionType::where('type', $this->type)->first();
    //     if (!$sectionType) {
    //         return $this->resolvedSettings = ['front' => [], 'back' => []];
    //     }

    //     $front = $sectionType->settings()->where('setting_key', 'front')->first()?->value ?? [];
    //     $back = $sectionType->settings()->where('setting_key', 'back')->first()?->value ?? [];

    //     return $this->resolvedSettings = ['front' => $front, 'back' => $back];
    // }

    public function toArray($request)
    {
        $settings = $this->getSettings();
        return [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->title_visible ? $this->title : null,
            'endpoint' => $this->buildEndpoint($settings),
            'order' => $this->order,
            // 'with_product' => $this->with_product,
            'setting' => $this->setting,
        ];
    }

    private function buildEndpoint(array $settings): string
    {
        $params = $settings['back'] ?? [];

        return 'general/' . $this->type . '?' . http_build_query(
            array_filter($params)
        );
    }
}
