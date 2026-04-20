<?php

namespace Marvel\Database\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Marvel\Enums\DiscountType;
use Spatie\Translatable\HasTranslations;

class Coupon extends Model
{
    use SoftDeletes, HasTranslations;

    protected $translatable = ['name'];

    protected $table = 'coupons';

    public $guarded = [];

    // protected $appends = ['is_valid'];



    protected static function boot()
    {
        parent::boot();
        // Order by updated_at desc
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('updated_at', 'desc');
        });

        static::creating(function ($coupon) {
            do {
                $code = strtoupper(Str::random(10));
            } while (self::where('code', $code)->exists());

            $coupon->code = $coupon->name."_".$code;
        });
    }

    /**
     * @return HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'coupon_id');
    }

    /**
     * Get all usage records for this coupon
     *
     * @return HasMany
     */
    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * @return bool
     */
    public function getIsValidAttribute()
    {
        return Carbon::now()->between($this->active_from, $this->expire_at);
    }
    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }


    public function isValid(): bool
    {
        return (bool)$this->getRawOriginal('status') === true
            && $this->used < $this->limiter
            && $this->start_date <= today()
            && $this->end_date >= today();
    }

    public function typeByLang()
    {
        $map = [
            'ar' => [
                'fixed_rate' => 'خصم من السعر بالقيمة',
                'percentage' => 'خصم بالنسبة المئوية',
                'free_shipping'=> 'شحن مجاني',

            ],
            'en' => [
                'fixed_rate' => 'Fixed discount',
                'percentage' => 'Percentage discount',
                'free_shipping'=> 'Free shipping',
            ],
        ];

        $locale = app()->getLocale();
            return $map[$locale][$this->discount_type] ?? $this->discount_type;
    }

    public function calcPrice($price): float
    {
        if ($this->discount_type === DiscountType::PERCENTAGE) {
            return max(0, $price - ($price * ($this->discount / 100)));
        } elseif ($this->discount_type === DiscountType::FIXED_RATE) {
            return max(0, $price - $this->discount);
        }
        return $price;
    }
}
