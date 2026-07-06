<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-pusher', function () {
    $user = \Marvel\Database\Models\User::where('type', 'admin')->first();

    if (!$user) {
        return response()->json(['error' => 'No admin user found'], 404);
    }

    broadcast(new \App\Events\AdminLoggedIn($user, request()->ip(), request()->userAgent()));

    return response()->json([
        'success' => true,
        'message' => 'Pusher test event broadcast to ' . $user->email,
        'event' => 'AdminLoggedIn',
        'channel' => 'private-admin.notifications',
        'channel_type' => 'PrivateChannel',
        'notification_channel' => 'private-users.' . $user->id,
    ]);
});


