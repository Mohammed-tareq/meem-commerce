<?php

namespace Marvel\Database\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;


class Slider extends Model implements HasMedia , Sortable
{
    use InteractsWithMedia , SortableTrait , SoftDeletes;
    protected $table = 'sliders';
     public $sortable = [
        'order_column_name' => 'order',
        'sort_when_creating' => true,
    ];

    public $guarded = [];



    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
