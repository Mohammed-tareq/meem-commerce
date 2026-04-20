<?php

namespace Marvel\Database\Models;

use Illuminate\Database\Eloquent\Model;


class   Cart extends Model
{


    protected $table = 'carts';

    public $guarded = [];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
}
