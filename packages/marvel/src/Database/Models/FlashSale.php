<?php

namespace Marvel\Database\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Marvel\Enums\FlashSaleType;
use Carbon\Carbon;

class FlashSale extends Model
{
    use HasTranslations, SoftDeletes, Sluggable;

    protected $table = 'flash_sales';

    public array $translatable = ["title", "description"];
    public $guarded = [];

    // protected $casts = [
    //     'cover_image'  => 'json',
    //     'sale_builder' => 'json',
    //     'image'        => 'json'
    // ];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
            ]
        ];
    }



    /**
     * @return BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'flash_sale_products')->withPivot('flash_sale_id', 'product_id');
    }

    /**
     * @return HasMany
     */
    public function flashSaleRequests(): HasMany
    {
        return $this->hasMany(FlashSaleRequests::class);
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
        return $map[$locale][$this->type] ?? $this->type;
    }

    public function calcPrice($price)
    {
        if ($this->type == FlashSaleType::PERCENTAGE) {
            return max(0, $price - ($price * ($this->value / 100)));
        } elseif ($this->type == FlashSaleType::FIXED_RATE) {
            return max(0, $price - $this->value);
        } else {
            return $price;
        }
    }

    /**
     * Determine if this flash sale is currently valid (active).
     *
     * @return bool
     */
    public function isValid(): bool
    {
        if (!$this->sale_status) {
            return false;
        }

        $now = Carbon::now();

        if ($this->start_date && $now->lt(Carbon::parse($this->start_date))) {
            return false;
        }

        if ($this->end_date && $now->gt(Carbon::parse($this->end_date))) {
            return false;
        }

        return true;
    }

    /**
     * Scope a query to only active flash sales.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive(Builder $query)
    {
        $now = Carbon::now();
        return $query->where('sale_status', true)
            ->whereDate('start_date', '<=', $now)
            ->whereDate('end_date', '>=', $now);
    }
}