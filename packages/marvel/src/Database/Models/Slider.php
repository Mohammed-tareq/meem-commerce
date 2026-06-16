<?php

namespace Marvel\Database\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Testing\Fluent\Concerns\Has;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Translatable\HasTranslations;
use Illuminate\Support\Str;

class Slider extends Model implements HasMedia, Sortable
{
    use InteractsWithMedia, SortableTrait, SoftDeletes, HasTranslations;
    protected $table = 'sliders';

    public array $translatable = ['title'];
    public $sortable = [
        'order_column_name' => 'order',
        'sort_when_creating' => true,
    ];

    public $fillable = [
        'title',
        'slug',
        'order',
        'status'
    ];

    protected static function booted()
    {
        static::saving(function ($slider) {
            $enTitle = $slider->getTranslation('title', 'en', false);
            $slider->slug = $enTitle ? Str::slug($enTitle) : null;
        });
    }



    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
