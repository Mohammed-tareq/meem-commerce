<?php

use Marvel\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware(['api', 'check-lang'])->group(function () {
    Route::controller(NotificationController::class)->group(function () {
        Route::get('notifications', 'index');
        Route::get('notifications/unread', 'unread');
        Route::patch('notifications/{id}/read', 'markAsRead');
        Route::patch('notifications/read-all', 'markAllAsRead');
        Route::delete('notifications/{id}', 'destroy');
        Route::delete('notifications', 'destroyAll');
    });
});
