<?php

namespace Marvel\Database\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Marvel\Enums\DiscountType;
use Spatie\Translatable\HasTranslations;

class Coupon extends Model
{
    use  HasTranslations;

    protected $translatable = ['name'];

    protected $table = 'coupons';

    public $fillable = [
        'code',
        'name',
        'discount_type',
        'discount',
        'max_discount_amount',
        'start_date',
        'end_date',
        'limiter',
        'used',
        'status'
    ];

    // protected $appends = ['is_valid'];

    protected $casts = [
        'status' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

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

            $coupon->code = strtolower(preg_replace('/\s+/', '_', trim($coupon->name))) . "_" . $code;
        });
    }

    /**
     * @return HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'coupon_id');
    }




    public function isValid(): bool
    {

        $today = today();

        return $this->status
            && (!$this->start_date || $this->start_date->lte($today))
            && (!$this->end_date || $this->end_date->gte($today))
            && (is_null($this->limiter) || $this->used < $this->limiter);
    }
    public function scopeValid($query)
    {
        return $query
            ->where('status', true)
            ->where(function ($query) {
                $query->whereNull('limiter')
                    ->orWhereColumn('used', '<', 'limiter');
            })
            ->where(function ($query) {
                $query->whereNull('start_date')
                    ->orWhereDate('start_date', '<=', today());
            })
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', today());
            });
    }


    public function typeByLang()
    {
        $map = [
            'ar' => [
                'fixed_rate' => 'خصم من السعر بالقيمة',
                'percentage' => 'خصم بالنسبة المئوية',
                'free_shipping' => 'شحن مجاني',

            ],
            'en' => [
                'fixed_rate' => 'Fixed discount',
                'percentage' => 'Percentage discount',
                'free_shipping' => 'Free shipping',
            ],
        ];

        $locale = app()->getLocale();
        return $map[$locale][$this->discount_type] ?? $this->discount_type;
    }


    public function calcPrice($price)
    {
        if ($price === null) {
            return null;
        }

        $price = (float) $price;
        $discount = (float) $this->discount;
        $maxValue = $this->max_discount_amount ? (float) $this->max_discount_amount : null;

        if ($this->discount_type === DiscountType::PERCENTAGE) {
            $discount = $price * ($discount / 100);

            $discount = $maxValue !== null
                ? min($discount, $maxValue)
                : $discount;

            return round(max(0, $price - $discount), 2);
        } elseif ($this->discount_type == DiscountType::FIXED_RATE) {
            return round(max(0, $price - $discount), 2);
        } else {

            return round($price, 2);
        }
    }
}