<?php

namespace Marvel\Database\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Banner extends Model implements HasMedia
{
    use InteractsWithMedia, HasTranslations;
    protected $table = 'banners';

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
