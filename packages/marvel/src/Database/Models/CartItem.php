<?php

namespace Marvel\Database\Models;

use Illuminate\Database\Eloquent\Model;


class   CartItem extends Model
{


    protected $table = 'cart_items';

    public $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'reserved_quantity',
        'price',
        'total_price',
        'product_variant_id',
        'attributes',
        'is_gift',
        'promotion_id',
        'shipping_method',
    ];
    protected $casts = [
        'attributes' => 'array',
        'is_gift' => 'boolean',
    ];






    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }
}
