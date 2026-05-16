<?php

namespace Marvel\Database\Models;

use Illuminate\Database\Eloquent\Model;

class   Transaction extends Model
{
    protected $table = 'transactions';

    public $fillable = [
        'order_id',
        'invoice_id',
        'payment_method',
        'user_id',
    ];


    /**
     * Get the product that owns the wishlist.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
