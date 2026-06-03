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
    use HasTranslations, Sluggable, InteractsWithMedia;

    protected $table = 'brands';

    public array $translatable = ['name', 'details', 'slug'];

    protected $fillable = ['name', 'details', 'slug', 'status'];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

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
        return $this->belongsToMany(Product::class, 'brand_product');
    }

    protected static function booted()
    {
        static::saving(function ($brand) {

            $title = $brand->name ?? [];

            $brand->slug = [
                'en' => isset($title['en'])
                    ? Str::slug($title['en'])
                    : null,

                'ar' => isset($title['ar'])
                    ? str_replace(' ', '-', trim($title['ar']))
                    : null,
            ];
        });
    }
}
