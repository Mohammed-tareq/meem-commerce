<?php

namespace Marvel\Http\Resources;

class ProductMiniResource extends Resource
{
    public function toArray($request): array
    {
        return [
            'id'                     => $this->id,
            'name'                   => $this->getTranslation('name', app()->getLocale()),
            'slug'                   => $this->slug,
            'price'                  => $this->roundMoney($this->price),
            'current_price'          => $this->roundMoney($this->current_price),
            'price_after_discount'    => $this->roundMoney($this->price_after_discount),
            'discount_type'          => $this->discount_type,
            'discount_amount'        => $this->discount_amount,
            'quantity'               => $this->quantity,
            'discount_valid'           =>  (bool) $this->isDiscountActive(),
        ];
    }




    private function roundMoney($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        return round((float) $value, 2);
    }
}
