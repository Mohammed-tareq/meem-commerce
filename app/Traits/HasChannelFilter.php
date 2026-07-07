<?php

namespace App\Traits;

use App\Contexts\ChannelContext;
use Illuminate\Database\Eloquent\Builder;

trait HasChannelFilter
{
    private function applyChannelHomeFilter(Builder $query): void
    {
        if (!config('channel.enabled', true)) {
            return;
        }

        $context = app(ChannelContext::class);

        if ($context->isHome()) {
            $query->where('is_fast_shipping_available', false);
        }
    }
}
