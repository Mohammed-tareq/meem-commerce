<?php

namespace Marvel\Database\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Marvel\Enums\PaymentStatus;

class Order extends Model
{
    use SoftDeletes;


    protected $table = 'orders';

    public $fillable = [
        'user_id',
        'name',
        'user_phone',
        'user_email',
        'address',
        'notes',
        'shipping_method',
        'expected_delivery_at',
        'fast_shipping_fee',
        'price',
        'shipping_price',
        'total_price',
        'coupon',
        'coupon_discount',
        'coupon_discount_type',
        'coupon_discount_max_amount',
        'promotion_id',
        'promotion_code',
        'promotion_type',
        'promotion_discount',
        'status',
        'shop_id',
    ];

    protected $casts = [
        'address' => 'array',
        'expected_delivery_at' => 'datetime',
    ];


    protected $hidden = [
        'deleted_at'
    ];

    protected static function boot()
    {
        parent::boot();
        // Order by created_at desc
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('created_at', 'desc');
        });
    }

    // protected $with = ['customer', 'products.variation_options'];

    /**
     * @return belongsToMany
     */
    // public function products(): belongsToMany
    // {
    //     return $this->belongsToMany(Product::class)
    //         ->withPivot('order_quantity', 'unit_price', 'subtotal', 'variation_option_id')
    //         ->withTimestamps();
    // }

    /**
     * @return belongsTo
     */
    // public function coupon(): belongsTo
    // {
    //     return $this->belongsTo(Coupon::class, 'coupon_id');
    // }

    // /**
    //  * @return belongsTo
    //  */
    // public function customer(): belongsTo
    // {
    //     return $this->belongsTo(User::class, 'customer_id');
    // }

    // /**
    //  * @return BelongsTo
    //  */
    // public function shop(): BelongsTo
    // {
    //     return $this->belongsTo(Shop::class, 'shop_id');
    // }

    // /**
    //  * @return HasMany
    //  */
    // public function children()
    // {
    //     return $this->hasMany('Marvel\Database\Models\Order', 'parent_id', 'id');
    // }

    // /**
    //  * @return HasOne
    //  */
    // public function parent_order()
    // {
    //     return $this->hasOne('Marvel\Database\Models\Order', 'id', 'parent_id');
    // }

    // /**
    //  * @return HasOne
    //  */
    // public function refund()
    // {
    //     return $this->hasOne(Refund::class, 'order_id');
    // }
    // /**
    //  * @return HasOne
    //  */
    // public function wallet_point()
    // {
    //     return $this->hasOne(OrderWalletPoint::class, 'order_id');
    // }

    // /**
    //  * @return HasMany
    //  */
    // public function payment_intent()
    // {
    //     return $this->hasMany(PaymentIntent::class);
    // }

    // /**
    //  * @return HasMany
    //  */
    // public function reviews(): HasMany
    // {
    //     return $this->hasMany(Review::class);
    // }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('shipping_method', 'SCHEDULED');
    }

    public function scopeFast(Builder $query): Builder
    {
        return $query->where('shipping_method', 'FAST');
    }

    public function getOrderNumberAttribute(): string
    {
        return 'ORD-' . str_pad((string) $this->id, 8, '0', STR_PAD_LEFT);
    }

    public function getPaymentStatusAttribute(): string
    {
        return match ($this->status) {
            'completed', 'delivered' => PaymentStatus::SUCCESS,
            'cancelled' => PaymentStatus::FAILED,
            default => PaymentStatus::PENDING,
        };
    }
}
