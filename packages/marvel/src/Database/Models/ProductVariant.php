<?php

namespace Marvel\Database\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\ProductVariantFactory;



class ProductVariant extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return ProductVariantFactory::new();
    }

    protected $table = 'product_variants';
    protected $fillable = ['price', 'sale_price', 'quantity', 'height', 'width', 'length', 'weight', 'product_id'];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attributeProducts()
    {
        return $this->hasMany(AttributeProduct::class);
    }
}
