<?php

namespace Marvel\Database\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Marvel\Enums\PromotionType;
use Spatie\Translatable\HasTranslations;

class Promotion extends Model
{
    use HasTranslations;
    public  array $translatable = ['name'];

    protected $table = 'promotions';

    public $fillable = ['name', 'type', 'value', 'max_discount_amount', 'code', 'min_order_amount', 'limiter', 'usage', 'start_at', 'end_at', 'status'];

    protected $casts = [
        'start_at' => 'date',
        'end_at' => 'date',
        'status' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('created_at', 'desc');
        });

        static::creating(function (self $promotion) {
            if (empty($promotion->code)) {
                $promotion->code = (string) Str::uuid();
            }
        });
    }

    public function typeByLang()
    {
        $map = [
            'ar' => [
                'fixed_rate' => 'خصم من السعر بالقيمة',
                'percentage' => 'خصم بالنسبة المئوية',
                'amount' => "خصم بالكميه",
            ],
            'en' => [
                'fixed_rate' => 'Fixed discount',
                'percentage' => 'Percentage discount',
                'amount' => 'Amount discount',
            ],
        ];

        $locale = app()->getLocale();
        return $map[$locale][$this->type] ?? $this->type;
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }


    public function scopeValid($query)
    {
        return $query
            ->where('status', true)
            ->where(function ($query) {
                $query->whereNull('limiter')
                    ->orWhereColumn('used', '<', 'limiter');
            })
            ->whereDate('start_date', '<=', today())
            ->whereDate('end_date', '>=', today());
    }


    public function isValid(): bool
    {
        $today = today();

        return $this->status
            && (!$this->start_date || $this->start_date->lte($today))
            && (!$this->end_date || $this->end_date->gte($today))
            && (is_null($this->limiter) || $this->used < $this->limiter);
    }

    public function discountAmount(float $price): float
    {
        if ($price === null|| $price <= 0) {
            return null;
        }

        $price = (float) $price;
        $value = (float) $this->value;
        $maxValue = $this->max_discount_amount ? (float) $this->max_discount_amount : null;

        if ($this->discount_type === PromotionType::PERCENTAGE) {
            $discount = $price * ($value / 100);

            $discount = $maxValue !== null
                ? min($discount, $maxValue)
                : $discount;

            return round(max(0, $price - $discount), 2);
        } elseif ($this->discount_type == PromotionType::FIXED) {
            return round(max(0, $price - $value), 2);
        } else {

            return round($price, 2);
        }
        if ($price <= 0) {
            return 0.0;
        }

        if ($this->type === PromotionType::PERCENTAGE) {
            return max(0.0, $price * ((float) $this->value / 100));
        }

        if ($this->type === PromotionType::FIXED || $this->type === PromotionType::AMOUNT) {
            return max(0.0, (float) $this->value);
        }

        return 0.0;
    }


    public function calcPrice(float $price): float
    {
        return max(0.0, $price - $this->discountAmount($price));
    }
}
