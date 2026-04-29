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

    public $guarded = [];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'is_active' => 'boolean',
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
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        $now = Carbon::now();

        return $query
            ->where('is_active', true)
            ->where(function ($inner) use ($now) {
                $inner->whereNull('start_at')->orWhere('start_at', '<=', $now);
            })
            ->where(function ($inner) use ($now) {
                $inner->whereNull('end_at')->orWhere('end_at', '>=', $now);
            });
    }

    public function isValid(?float $orderAmount = null): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = Carbon::now();
        if ($this->start_at && $now->lt($this->start_at)) {
            return false;
        }
        if ($this->end_at && $now->gt($this->end_at)) {
            return false;
        }

        if ($orderAmount !== null && $this->min_order_amount !== null) {
            return $orderAmount >= (float) $this->min_order_amount;
        }

        return true;
    }

    public function discountAmount(float $price): float
    {
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
