<?php

namespace Marvel\Database\Models;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Cviebrock\EloquentSluggable\Sluggable;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Marvel\Enums\DiscountType;
use Marvel\Traits\Excludable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Translatable\HasTranslations;
use Spatie\MediaLibrary\InteractsWithMedia;


class Product extends Model implements HasMedia
{
    use HasTranslations, SoftDeletes, Sluggable, Excludable, InteractsWithMedia;

    protected $table = 'products';
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'product_type',
        'sku',
        'quantity',
        'sold_quantity',
        'in_stock',
        'status',
        'height',
        'width',
        'length',
        'weight',
        'has_flash_sale',
        'has_discount',
        'banner_id',
        'discount_type',
        'discount_amount',
        'discount_status',
        'start_date',
        'end_date',
        'price_after_discount',
        'price_after_flash_sale',
        'discount_status',
        'shop_id'
    ];
    public array $translatable = ['name', 'description'];
    // protected $metaTable = 'products_meta'; //optional.
    // protected $disableFluentMeta = true;
    public $hideMeta = true;

    // protected $casts = [
    //     'image' => 'json',
    //     'gallery' => 'json',
    //     'video' => 'json',
    // ];

    protected $appends = [
        // 'ratings',
        // 'total_reviews',
        // 'rating_count',
        // 'my_review',
        // 'in_wishlist',
        // 'blocked_dates',
        // 'translated_languages',
        // 'sold'
    ];

    protected $casts = [
        'discount_status' => 'boolean',
        'has_discount' => 'boolean',
        'has_flash_sale' => 'boolean',
    ];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            // Only set SKU if not already provided
            if (empty($product->sku)) {
                $product->sku = 'PRD-' . Str::uuid();
            }
        });

        // Recalculate stored prices whenever a product is retrieved from DB
        static::retrieved(function ($product) {
            try {
                $discountPrice = $product->getDiscountedPrice();

                // Determine base price for flash sale calculation
                // $baseForFlash = $discountPrice ?? $product->price;

                $flashPrice = $product->getFlashSalePrice($product->price);

                $needsUpdate = false;
                $updateData = [];

                // Normalize null vs numeric
                $currentDiscount = $product->price_after_discount;
                $currentFlash = $product->price_after_flash_sale;

                if ($discountPrice === null) {
                    if ($currentDiscount !== null) {
                        $updateData['price_after_discount'] = null;
                        $needsUpdate = true;
                    }
                } else {
                    // store numeric value
                    if ($currentDiscount != $discountPrice) {
                        $updateData['price_after_discount'] = $discountPrice;
                        $needsUpdate = true;
                    }
                }

                if ($flashPrice === null) {
                    if ($currentFlash !== null) {
                        $updateData['price_after_flash_sale'] = null;
                        $needsUpdate = true;
                    }
                } else {
                    if ($currentFlash != $flashPrice) {
                        $updateData['price_after_flash_sale'] = $flashPrice;
                        $needsUpdate = true;
                    }
                }

                if ($needsUpdate && !empty($updateData)) {
                    // Use DB query to avoid triggering model events again
                    DB::table('products')->where('id', $product->id)->update($updateData);
                    // Also sync current model instance so callers see updated values
                    // foreach ($updateData as $k => $v) {
                    //     $product->$k = $v;
                    // }
                    $product->refresh();
                }
            } catch (\Exception $e) {
                // Fail silently to avoid breaking retrieval flow
                logger('Product price recalculation failed: ' . $e->getMessage());
            }
        });
    }

    // public function discount(): HasOne
    // {
    //     return $this->hasOne(Discount::class);
    // }

    // public function getFinalPriceAttribute()
    // {
    //     if (!$this->has_discount) {
    //         return $this->price;
    //     }

    //     return $this->discount->getPriceAfterDiscount($this);
    // }

    public function isDiscountActive(): bool
    {
        // If discount_status is explicitly set, allow it to override auto behavior.
        if (!is_null($this->discount_status)) {
            if ($this->discount_status === false) {
                return false;
            }
        }

        if (!$this->has_discount) {
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

    public function getActiveFlashSale()
    {
        if (!$this->has_flash_sale) {
            return null;
        }

        $this->disableInvalidFlashSales();

        $now = Carbon::now();

        return $this->flash_sales()
            ->where('sale_status', true)
            ->whereDate('start_date', '<=', $now)
            ->whereDate('end_date', '>=', $now)
            ->orderBy('start_date', 'desc')
            ->first();
    }

    public function isFlashSaleValid($flashSale = null): bool
    {
        $flashSale = $flashSale ?? $this->flash_sales()->orderBy('start_date', 'desc')->first();
        if (!$flashSale) {
            return false;
        }

        if (!$flashSale->sale_status) {
            return false;
        }

        $now = Carbon::now();

        if ($flashSale->start_date && $now->lt(Carbon::parse($flashSale->start_date))) {
            return false;
        }

        if ($flashSale->end_date && $now->gt(Carbon::parse($flashSale->end_date))) {
            return false;
        }

        return true;
    }

    public function disableInvalidFlashSales(): int
    {
        $count = 0;
        $flashSales = $this->flash_sales()->get();

        foreach ($flashSales as $flashSale) {
            if (!$this->isFlashSaleValid($flashSale) && $flashSale->sale_status) {
                $flashSale->sale_status = false;
                $flashSale->save();
                $count++;
            }
        }

        if ($count > 0) {
            $this->price_after_flash_sale = null;
            $this->save();
        }

        return $count;
    }

    public function getCurrentPrice()
    {
        $discountedPrice = $this->getDiscountedPrice();
        $basePrice = $discountedPrice ?? $this->price;
        $flashSalePrice = $this->getFlashSalePrice($basePrice);

        return $flashSalePrice ?? $basePrice;
    }

    public function getDiscountedPrice()
    {
        if (!$this->isDiscountActive()) {
            return null;
        }

        return $this->calculateDiscountedPrice($this->price);
    }

    public function getFlashSalePrice($basePrice = null)
    {
        $flashSale = $this->getActiveFlashSale();
        if (!$flashSale) {
            return null;
        }

        $price = $basePrice ?? $this->price;
        if ($price === null) {
            return null;
        }

        return $flashSale->calcPrice($price);
    }

    private function calculateDiscountedPrice($price)
    {
        if ($price === null) {
            return null;
        }

        $price = (float) $price;
        $discountType = $this->discount_type ?? DiscountType::PERCENTAGE;
        $discount_amount = (float) ($this->discount_amount ?? 0);

        if ($discountType === DiscountType::PERCENTAGE) {
            return round(max(0, $price - ($price * ($discount_amount / 100))), 2);
        }

        if ($discountType === DiscountType::FIXED_RATE) {
            return round(max(0, $price - $discount_amount), 2);
        }

        return round($price, 2);
    }


    function getBlockedDates()
    {
        $_blockedDates = $this->fetchBlockedDatesForAProduct();
        $_flatBlockedDates = [];
        foreach ($_blockedDates as $date) {
            $from = Carbon::parse($date->from);
            $to = Carbon::parse($date->to);
            $dateRange = CarbonPeriod::create($from, $to);
            $_blockedDates = $dateRange->toArray();
            unset($_blockedDates[count($_blockedDates) - 1]);
            $_flatBlockedDates = array_unique(array_merge($_flatBlockedDates, $_blockedDates));
        }
        return $_flatBlockedDates;
    }

    public function fetchBlockedDatesForAProduct()
    {
        return Availability::where('product_id', $this->id)->where('bookable_type', 'Marvel\Database\Models\Product')->whereDate('to', '>=', Carbon::now())->get();
    }

    /**
     * @return BelongsTo
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class, 'type_id');
    }

    /**
     * @return BelongsTo
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    /**
     * @return BelongsTo
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class, 'author_id');
    }

    /**
     * @return BelongsTo
     */
    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(Manufacturer::class, 'manufacturer_id');
    }

    /**
     * @return BelongsTo
     */
    public function shipping(): BelongsTo
    {
        return $this->belongsTo(Shipping::class, 'shipping_class_id');
    }

    /**
     * @return BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_product');
    }

    /**
     * @return BelongsToMany
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'product_tag');
    }

    /**
     * @return HasMany
     */
    // public function variation_options(): HasMany
    // {
    //     return $this->hasMany(Variation::class, 'product_id');
    // }

    /**
     * @return belongsToMany
     */
    public function orders(): belongsToMany
    {
        return $this->belongsToMany(Order::class)->withTimestamps();
    }

    /**
     * @return HasMany
     */
    public function variations(): HasMany
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }

    /**
     * @return HasMany
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'product_id');
    }

    /**
     * @return HasMany
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'product_id');
    }

    /**
     * @return HasMany
     */
    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class, 'product_id');
    }

    public function getRatingsAttribute()
    {
        return round($this->reviews()->avg('rating'), 2);
    }

    public function getTotalReviewsAttribute()
    {
        return $this->reviews()->count();
    }

    public function getRatingCountAttribute()
    {
        return $this->reviews()->orderBy('rating', 'DESC')->groupBy('rating')->select('rating', DB::raw('count(*) as total'))->get();
    }

    public function getMyReviewAttribute()
    {
        if (auth()->user() && !empty($this->reviews()->where('user_id', auth()->user()->id)->first())) {
            return $this->reviews()->where('user_id', auth()->user()->id)->get();
        }
        return null;
    }

    public function getInWishlistAttribute()
    {
        if (auth()->user() && !empty($this->wishlists()->where('user_id', auth()->user()->id)->first())) {
            return true;
        }
        return false;
    }

    public function digital_file()
    {
        return $this->morphOne(DigitalFile::class, 'fileable');
    }

    public function availabilities()
    {
        return $this->morphMany(Availability::class, 'bookable');
    }


    /**
     * @return BelongsToMany
     */
    public function dropoff_locations(): BelongsToMany
    {
        return $this->belongsToMany(Resource::class, 'dropoff_location_product', 'product_id', 'resource_id');
    }
    /**
     * @return BelongsToMany
     */
    public function pickup_locations(): BelongsToMany
    {
        return $this->belongsToMany(Resource::class, 'pickup_location_product', 'product_id', 'resource_id');
    }
    /**
     * @return BelongsToMany
     */
    public function deposits(): BelongsToMany
    {
        return $this->belongsToMany(Resource::class, 'deposit_product', 'product_id', 'resource_id');
    }
    /**
     * @return BelongsToMany
     */
    public function persons(): BelongsToMany
    {
        return $this->belongsToMany(Resource::class, 'person_product', 'product_id', 'resource_id');
    }
    /**
     * @return BelongsToMany
     */
    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Resource::class, 'feature_product', 'product_id', 'resource_id');
    }

    /**
     * @return int|mixed
     */
    // public function getSoldAttribute()
    // {
    //     return DB::table('order_product')
    //         ->join('orders', 'orders.id', '=', 'order_product.order_id')
    //         ->where('order_product.product_id', '=', $this->id)
    //         ->where('orders.parent_id', '=', null)
    //         ->sum('order_quantity');
    // }

    /**
     * @return BelongsToMany
     */
    public function flash_sales(): BelongsToMany
    {
        return $this->belongsToMany(FlashSale::class, 'flash_sale_products')->withPivot('flash_sale_id', 'product_id');
    }

    /**
     * flash_sale_requests
     *
     * @return BelongsToMany
     */
    public function flash_sale_requests(): BelongsToMany
    {
        return $this->belongsToMany(FlashSaleRequests::class, "flash_sale_requests_products");
    }

    public function loadRelated($slug, $limit = 10, $language = DEFAULT_LANGUAGE)
    {
        $relatedProducts = [];
        try {
            $product = $this->where('slug', $slug)->firstOrFail();
            $categories = $product->categories()->pluck('id');

            $relatedProducts = $this->where('language', $language)
                ->whereHas('categories', function ($query) use ($categories) {
                    $query->whereIn('categories.id', $categories);
                })->with('type')->limit($limit)->get();
        } catch (Exception $e) {
            logger($e->getMessage()); // logging the error
        }
        $this->setRelation('related_products', $relatedProducts);
        return $this;
    }


    public function scopeActive($query)
    {
        return $query->where('status', true)->where('quantity', '>', 0);
    }
}
