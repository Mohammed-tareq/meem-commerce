<?php

namespace Marvel\Database\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Banner extends Model implements HasMedia, Sortable
{
    use InteractsWithMedia, HasTranslations, SortableTrait ,SoftDeletes;
    protected $table = 'banners';
    public $sortable = [
        'order_column_name' => 'order',
        'sort_when_creating' => true,
    ];

    public $guarded = [];
    public $translatable = ['title', 'description'];

    // protected $casts = [
    //     'image'   => 'json',
    // ];



    // /**
    //  * @return BelongsTo
    //  */
    // public function type(): BelongsTo
    // {
    //     return $this->belongsTo(Type::class, 'type_id');
    // }

    public function products()
    {
        return $this->hasMany(Product::class, 'banner_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
