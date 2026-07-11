<?php

namespace App\Providers;

use App\Events\AdminLoggedIn;
use App\Events\ContactMessageReceived;
use App\Events\OrderCancelled;
use App\Events\OrderCreated;
use App\Events\OrderStatusChanged;
use App\Events\PaymentFailed;
use App\Events\PaymentSucceeded;
use App\Events\UserRolesUpdated;
use App\Listeners\LogUserRolesUpdated;
use App\Listeners\RestoreProductInventory;
use App\Listeners\SendAdminLoginNotification;
use App\Listeners\SendContactMessageNotification;
use App\Listeners\SendNewOrderNotification;
use App\Listeners\SendOrderCancelledNotification;
use App\Listeners\SendOrderStatusChangedNotification;
use App\Listeners\SendPaymentFailedNotification;
use App\Listeners\SendPaymentSucceededNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        UserRolesUpdated::class => [
            LogUserRolesUpdated::class,
        ],
        OrderCreated::class => [
            SendNewOrderNotification::class,
        ],
        ContactMessageReceived::class => [
            SendContactMessageNotification::class,
        ],
        AdminLoggedIn::class => [
            SendAdminLoginNotification::class,
        ],
        OrderCancelled::class => [
            RestoreProductInventory::class,
            SendOrderCancelledNotification::class,
        ],
        OrderStatusChanged::class => [
            SendOrderStatusChangedNotification::class,
        ],
        PaymentSucceeded::class => [
            SendPaymentSucceededNotification::class,
        ],
        PaymentFailed::class => [
            SendPaymentFailedNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
