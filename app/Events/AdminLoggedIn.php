<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdminLoggedIn
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public $admin,
        public string $ip,
        public string $userAgent,
    ) {}
}
