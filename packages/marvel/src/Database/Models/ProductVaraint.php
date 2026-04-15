<?php

namespace Marvel\Database\Models;

use Illuminate\Database\Eloquent\Model;



class ProductVaraint extends Model
{
    protected $table = 'product_varaints';
    protected $guarded = [];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attributeProducts()
    {
        return $this->hasMany(AttributeProduct::class);
    }
}