<?php

use App\Http\Controllers\Api\General\BannerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\General\CategoryController;
use App\Http\Controllers\Api\General\CouponController;
use App\Http\Controllers\Api\General\FAQController;
use App\Http\Controllers\Api\General\FlashSaleController;
use App\Http\Controllers\Api\General\ProductController;
use App\Http\Controllers\Api\General\SearchController;
use App\Http\Controllers\Api\General\SettingController;
use App\Http\Controllers\Api\General\ShopController;
use App\Http\Controllers\Api\General\SliderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::prefix('general')->middleware(['throttle:general'])->group(function () {
    Route::get('products', [ProductController::class, 'index']);
    Route::get('shops', [ShopController::class, 'index']);
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('coupons', [CouponController::class, 'index']);
    Route::get('search', [SearchController::class, 'index']);
    Route::get('setting', [SettingController::class, 'index']);
    Route::get('sliders', [SliderController::class, 'index']);
    Route::get('banners', [BannerController::class, 'index']);
    Route::get('faqs', [FAQController::class, 'index']);
    Route::get('flash-sales', [FlashSaleController::class, 'index']);
});