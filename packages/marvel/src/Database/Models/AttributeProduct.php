<?php

namespace Marvel\Database\Models;

use Illuminate\Database\Eloquent\Model;


class AttributeProduct extends Model
{
    protected $table = "attribute_product";
    protected $guarded = [];

    public function productVariant()
    {
        return $this->belongsTo(ProductVaraint::class);
    }

    public function attributeValue()
    {
        return $this->belongsTo(AttributeValue::class);
    }
}