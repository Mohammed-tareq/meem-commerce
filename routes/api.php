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

    //======================== shops=========================//
    Route::controller(ShopController::class)->group(function () {
        Route::get('shops', 'index')->name('general-shop-index');
            Route::get('shops/{id}', 'getShopById');
    });
    //======================== categories=========================//
    Route::controller(CategoryController::class)->group(function () {
        Route::get('categories', 'index')->name('general-category-index');
        Route::get('categories/{id}', 'getCategoryById');
    });
    //======================== products=========================//
    Route::controller(ProductController::class)->group(function () {
        Route::get('products', 'index');
        Route::get('products/{id}', 'getProductById');
    });
    Route::get('coupons', [CouponController::class, 'index']);
    Route::get('search', [SearchController::class, 'index']);
    Route::get('settings', [SettingController::class, 'index']);
    Route::get('sliders', [SliderController::class, 'index']);
    Route::get('banners', [BannerController::class, 'index']);
    Route::get('faqs', [FAQController::class, 'index']);
    Route::get('flash-sales', [FlashSaleController::class, 'index']);
});



















Route::get('/enum-types', function () {
    return response()->json(
        [
            'discount-type' => \Marvel\Enums\DiscountType::getValues(),
            'coupon-type' => \Marvel\Enums\CouponType::getValues(),
            'product-type' => \Marvel\Enums\ProductType::getValues(),
            'promotion-type' => \Marvel\Enums\PromotionType::getValues(),
            'promotion-mount-type' => \Marvel\Enums\PromotionMountType::getValues(),
            'flash-sale-type' => \Marvel\Enums\FlashSaleType::getValues(),
        ], 200);
});