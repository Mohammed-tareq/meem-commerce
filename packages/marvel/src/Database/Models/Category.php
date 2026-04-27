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
    public array $translatable = ['name','details'];

    public $guarded = [];

    // protected $casts = [
    //     'image' => 'json',
    //     'banner_image' => 'json',
    // ];

    // protected $appends = ['parent_id'];

   
   

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


    /**
     * @return BelongsTo
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class, 'type_id');
    }

    public function shops()
    {
        return $this->belongsToMany(Shop::class,'category_shop');
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
        return $this->hasMany('Marvel\Database\Models\Category', 'parent', 'id')->with('children')->withCount('products');
    }

   
    // public function subCategories()
    // {
    //     return $this->hasMany('Marvel\Database\Models\Category', 'parent', 'id')->with('subCategories', 'parent')->withCount('products');
    // }

    
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
    
}
