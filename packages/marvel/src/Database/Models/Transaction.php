<?php

namespace Marvel\Database\Models;

use Illuminate\Database\Eloquent\Model;

class   Transaction extends Model
{
    protected $table = 'transactions';

    public $guarded = [];


    /**
     * Get the product that owns the wishlist.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
