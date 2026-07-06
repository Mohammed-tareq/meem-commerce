<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Notifications\NewOrderNotification;
use Illuminate\Support\Facades\Notification;

class SendNewOrderNotification
{
    public function handle(OrderCreated $event): void
    {
        $admins = $this->getAdminUsers();

        if ($admins->isNotEmpty()) {
            Notification::send($admins, new NewOrderNotification($event->order));
        }
    }

    private function getAdminUsers()
    {
        $adminModel = config('auth.providers.users.model');

        return $adminModel::where('type', 'admin')
            ->where('is_active', true)
            ->get();
    }
}
