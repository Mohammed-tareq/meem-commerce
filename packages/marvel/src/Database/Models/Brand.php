<?php

namespace Marvel\Database\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;
use Illuminate\Support\Str;


class Brand extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia;

    protected $table = 'brands';

    public array $translatable = ['name', 'details', 'slug'];

    protected $fillable = ['name', 'details', 'slug', 'status'];



    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 0);
    }

    public function scopeSearch($query, $field, $term, $locale)
    {
        return $query->where(function ($q) use ($field, $term, $locale) {
            $q->where($field . '->' . $locale, 'like', "%$term%")
                ->orWhere($field, 'like', "%$term%");
        });
    }

    /**
     * @return BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'brand_product', 'brand_id', 'product_id');
    }

    protected static function booted()
    {
        static::saving(function ($brand) {


            $brand->slug = [
                'en' => $brand->getTranslation('name', 'en', false)
                    ? Str::slug($brand->getTranslation('name', 'en'))
                    : null,

                'ar' => $brand->getTranslation('name', 'ar', false)
                    ? str_replace(' ', '-', trim($brand->getTranslation('name', 'ar')))
                    : null,
            ];
        });
    }
}