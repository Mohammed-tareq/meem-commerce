<?php

namespace Marvel\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Translatable\HasTranslations;

class SectionItem extends Model implements Sortable
{
    use SortableTrait, HasTranslations;

    protected $table = 'section_items';

    public $sortable = [
        'order_column_name' => 'order',
        'sort_when_creating' => true,
    ];

    protected $fillable = [
        'section_id',
        'entity_type',
        'entity_id',
        'action_type',
        'action_id',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];


    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function entity()
    {
        return $this->morphTo();
    }

    public function action()
    {
        return $this->morphTo();
    }
}
