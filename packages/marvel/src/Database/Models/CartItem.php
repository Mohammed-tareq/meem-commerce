<?php

namespace Marvel\Database\Models;

use Illuminate\Database\Eloquent\Model;


class   CartItem extends Model
{


    protected $table = 'cart_items';

    public $guarded = [];


    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}