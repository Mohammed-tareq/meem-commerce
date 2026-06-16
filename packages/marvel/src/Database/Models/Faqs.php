<?php

namespace Marvel\Database\Models;

// use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Marvel\Database\Models\Shop;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Faqs extends Model
{
    use HasTranslations, SoftDeletes;

    protected $table = 'faqs';

    public array $translatable = ['faq_title', 'faq_description'];


    public $fillable = [
        'user_id',
        'shop_id',
        'faq_title',
        'faq_description',
        'faq_type',
        'issued_by',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function scopeActive (Builder $query): Builder
    {
        return $query->where('status', 1);
    }
}
