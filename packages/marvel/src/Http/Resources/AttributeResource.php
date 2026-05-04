<?php

namespace Marvel\Http\Resources;

use Illuminate\Http\Request;

class AttributeResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                   => $this->id,
            'name'                 => $this->getTranslation('name', app()->getLocale()),
            'slug'                 => $this->slug,
            $this->mergeWhen(request()->routeIs('attributes.show'), [
                'values'               => $this->whenLoaded('values', AttributeValueResource::collection($this->values))
            ])
        ];
    }
}
