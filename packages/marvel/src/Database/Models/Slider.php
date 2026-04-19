<?php

namespace Marvel\Database\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Slider extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $table = 'sliders';

    public $guarded = [];



    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
