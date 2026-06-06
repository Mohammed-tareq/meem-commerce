<?php

namespace Marvel\Database\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;
use Marvel\Services\Pricing\ProductPricingService;
use Illuminate\Support\Str;


class FlashSale extends Model implements HasMedia
{
    use HasTranslations, SoftDeletes, InteractsWithMedia;

    protected $table = 'flash_sales';

    public array $translatable = ["title", "description",'slug'];
    public $fillable = [
        'title',
        'slug',
        'description',
        'start_date',
        'end_date',
        'status',
        'type',
        'discount',
        'max_discount_amount'
    ];

    protected $casts = [
        'status' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Scope a query to only active flash sales.
     *





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
        return app(ProductPricingService::class)->calculateFlashSalePrice($this, $price);
    }

    /**
     * Determine if this flash sale is currently valid (active).
     *
     * @return bool
     */
    public function isValid(): bool
    {
        $today = today();

        return $this->status
            && (!$this->start_date || $this->start_date->lte($today))
            && (!$this->end_date || $this->end_date->gte($today));
    }


    public function scopeValid(Builder $query)
    {
        return $query->where('status', true)
            ->where(function ($query) {
                $query->whereNull('start_date')
                    ->orWhereDate('start_date', '<=', today());
            })
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', today());
            });
    }
    public function scopeInvalid(Builder $query)
    {
        return $query->where(function ($query) {
            $query->where('status', false)
                ->orWhere(function ($query) {
                    $query->whereNotNull('start_date')
                        ->whereDate('start_date', '>', today());
                })

                ->orWhere(function ($query) {
                    $query->whereNotNull('end_date')
                        ->whereDate('end_date', '<', today());
                });
        });
    }
    public function scopeSearch($query, $field, $term, $locale)
    {
        return  $query->where(function ($q) use ($field, $term, $locale) {
            $q->where($field . '->' . $locale, 'like', "%$term%")
                ->orWhere($field, 'like', "%$term%");
        });
    }

    protected static function booted()
    {
        static::saving(function ($flashSale) {

            $title = $flashSale->title ?? [];


            $flashSale->slug = [
                'en' => $flashSale->getTranslation('title', 'en', false)
                    ? Str::slug($flashSale->getTranslation('title', 'en'))
                    : null,

                'ar' => $flashSale->getTranslation('title', 'ar', false)
                    ? str_replace(' ', '-', trim($flashSale->getTranslation('title', 'ar')))
                    : null,
            ];
        });
    }
}