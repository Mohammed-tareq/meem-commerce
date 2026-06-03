<?php

namespace Marvel\Database\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Illuminate\Support\Str;

class Banner extends Model implements HasMedia, Sortable
{
    use InteractsWithMedia, HasTranslations, SortableTrait, SoftDeletes;
    protected $table = 'banners';
    public $sortable = [
        'order_column_name' => 'order',
        'sort_when_creating' => true,
    ];

    public $fillable = [
        'title',
        'slug',
        'description',
        'status',
        'order',
    ];
    public $translatable = ['title', 'description', 'slug'];


    protected static function booted()
    {
        static::saving(function ($banner) {

            $title = $banner->title ?? [];

            $banner->slug = [
                'en' => isset($title['en'])
                    ? Str::slug($title['en'])
                    : null,

                'ar' => isset($title['ar'])
                    ? str_replace(' ', '-', trim($title['ar']))
                    : null,
            ];
        });
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'banner_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
    public function scopeSearch($query, $field, $term, $locale)
    {
        return  $query->where(function ($q) use ($field, $term, $locale) {
            $q->where($field . '->' . $locale, 'like', "%$term%")
                ->orWhere($field, 'like', "%$term%");
        });
    }
}