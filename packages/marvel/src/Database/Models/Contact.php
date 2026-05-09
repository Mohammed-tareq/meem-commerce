<?php

namespace Marvel\Database\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $table = 'contacts';

    protected $fillable = [
        'email',
        'subject',
        'message',
        'is_read',
        'is_replay',
    ];

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeReplay($query)
    {
        return $query->where('is_replay', true);
    }
}
