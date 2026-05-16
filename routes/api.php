<?php

use App\Http\Controllers\Api\General\BannerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\General\CategoryController;
use App\Http\Controllers\Api\General\CouponController;
use App\Http\Controllers\Api\General\FAQController;
use App\Http\Controllers\Api\General\HomeController;
use App\Http\Controllers\Api\General\FlashSaleController;
use App\Http\Controllers\Api\General\OrderController;
use App\Http\Controllers\Api\General\ProductController;
use App\Http\Controllers\Api\General\SearchController;
use App\Http\Controllers\Api\General\SettingController;
use App\Http\Controllers\Api\General\ShopController;
use App\Http\Controllers\Api\General\SliderController;
use Illuminate\Support\Facades\Http;

Route::prefix('general')->middleware(['api', 'throttle:general', 'check-lang'])->group(function () {

    //========================= home=========================//
    Route::get('home', [HomeController::class, 'index'])->name('home');

    //======================== shops=========================//
    Route::controller(ShopController::class)->group(function () {
        Route::get('shops', 'index')->name('general-shop-index');
        Route::get('shops/{id}', 'getShopById')->name('general-shop-show');
    });
    //======================== categories=========================//
    Route::controller(CategoryController::class)->group(function () {
        Route::get('categories', 'index')->name('general-category-index');
        Route::get('categories/{id}', 'getCategoryById')->name('general-category-show');
    });
    //======================== products=========================//
    Route::controller(ProductController::class)->group(function () {
        Route::get('products', 'index');
        Route::get('products/{id}', 'getProductById');
        //========================= product reviews =========================//
        Route::post('products/{id}/reviews', 'addProductReview')->middleware('auth:sanctum');
        Route::put('products/reviews/{id}', 'updateProductReview')->middleware('auth:sanctum');
    });

    //========================= coupons=========================//
    Route::controller(CouponController::class)->group(function () {
        Route::get('coupons', 'index')->name('general-coupon-index');
        Route::post('coupons/apply', 'applyCoupon')->middleware('auth:sanctum');
    });
    //========================= order=========================//
    Route::controller(OrderController::class)->group(function () {
        Route::get('orders', 'index')->middleware('auth:sanctum');;
        //========================= checkout =========================//
        Route::post('checkout', 'checkout')->middleware('auth:sanctum');
        Route::get('checkout/callback', 'checkoutCallback')->name('api.checkout.callback');
        Route::get('checkout/error', 'checkoutErrorCallback')->name('api.checkout.errorCallback');
    });



    //========================= faqs=========================//
    Route::get('faqs', [FAQController::class, 'index']);

    //========================= flash-sales=========================//
    Route::controller(FlashSaleController::class)->group(function () {
        Route::get('flash-sales', 'index')->name('general-flash-sale-index');
        Route::get('flash-sales/{id}', 'getFlashSaleById')->name('general-flash-sale-show');
    });

    //========================= banners=========================//
    Route::controller(BannerController::class)->group(function () {
        Route::get('banners', 'index')->name('general-banner-index');
        Route::get('banners/{id}', 'getBannerById')->name('general-banner-show');
    });



    //========================= sliders=========================//
    Route::get('sliders', [SliderController::class, 'index']);

    //======================== settings=========================//
    Route::controller(SettingController::class)->group(function () {
        Route::get('settings', [SettingController::class, 'index']);
    });
    Route::get('search', [SearchController::class, 'index']);
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
        ],
        200
    );
});


Route::get('check-card-payment', function () {
    return [
        'CardNumber' => '2223000000000007',
        'CardExpiryMonthand year' => '01/39',
        'CardCVV' => '100',
    ];
});