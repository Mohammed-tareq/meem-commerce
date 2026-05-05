<?php

namespace Marvel\Database\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Cviebrock\EloquentSluggable\Sluggable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Category extends Model implements HasMedia
{
    use HasTranslations, Sluggable, InteractsWithMedia;


    protected $table = 'categories';
    public array $translatable = ['name', 'details'];

    public $fillable = ['name', 'details', 'slug', 'parent_id', 'status'];



    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }


    public  function scopeActive($query)
    {
        return $query->where('status', 1);
    }
    public  function scopeInactive($query)
    {
        return $query->where('status', 0);
    }

    public function scopeSearch($query, $field, $term, $locale)
    {
       return  $query->where(function ($q) use ($field, $term, $locale) {
            $q->where($field . '->' . $locale, 'like', "%$term%")
                ->orWhere($field, 'like', "%$term%");
        });
    }

    public function shops()
    {
        return $this->belongsToMany(Shop::class, 'category_shop');
    }
    /**
     * @return BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'category_product');
    }


    public function children()
    {
        return $this->hasMany('Marvel\Database\Models\Category', 'parent_id', 'id')->with('children')->withCount('products');
    }


    /**
     * @return BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo('Marvel\Database\Models\Category', 'parent_id', 'id');
    }
}
