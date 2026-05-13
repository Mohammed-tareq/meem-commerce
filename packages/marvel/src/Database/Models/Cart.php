<?php

namespace Marvel\Database\Models;

use Illuminate\Database\Eloquent\Model;


class   Cart extends Model
{


    protected $table = 'carts';

    public $fillable = [
        'user_id',
        'coupon',
        'total_price',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
}
