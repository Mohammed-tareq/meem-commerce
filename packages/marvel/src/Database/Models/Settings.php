<?php

namespace Marvel\Database\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Settings extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia;

    protected $table = 'settings';

    public $translatable = [
        'site_name',
        'site_desc',
        'meta_desc',
        'site_copy_right',
    ];

    public $guarded = [];

    protected $casts = [
        'options'   => 'array',
    ];

    
}
