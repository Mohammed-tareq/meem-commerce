<?php

use App\Http\Controllers\Api\General\BannerController;
use App\Http\Controllers\Api\General\BrandController;
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
use App\Http\Controllers\Api\General\SliderController;
use App\Http\Controllers\Api\General\ContentPageController;
use App\Http\Controllers\Api\General\PromotionController;
use Illuminate\Support\Facades\Mail;
//'throttle:general'

Route::prefix('general')->middleware(['api', 'check-lang'])->group(function () {

    //========================= home=========================//
    Route::controller(HomeController::class)->group(function () {
        // Route::get('home', 'index')->name('home');
        Route::get('navbar', 'navData')->name('navbar');
    });

    //======================== categories=========================//
    Route::controller(CategoryController::class)->group(function () {
        Route::get('categories', 'index')->name('general-category-index');
        Route::get('categories/{slug}', 'getCategoryBySlug')->name('general-category-show');
    });
    //======================== products=========================//
    Route::controller(ProductController::class)->group(function () {
        Route::get('products', 'index');
        Route::get('products/{slug}', 'getProductBySlug')->name('general-product-show');
        //========================= product reviews =========================//
        Route::post('products/{id}/reviews', 'addProductReview')->middleware('auth:sanctum');
        Route::put('products/reviews/{id}', 'updateProductReview')->middleware('auth:sanctum');
    });


    //========================= order=========================//
    Route::controller(OrderController::class)->group(function () {
        Route::get('orders', 'index')->middleware(['auth:sanctum', 'check-email']);
        //========================= checkout =========================//
        Route::get('checkout/promotions', 'eligiblePromotions')->middleware(['auth:sanctum', 'check-email']);
        Route::post('checkout', 'checkout')->middleware(['auth:sanctum', 'check-email']);
        Route::get('checkout/callback', 'checkoutCallback')->name('api.checkout.callback');
        Route::get('checkout/error', 'checkoutErrorCallback')->name('api.checkout.errorCallback');
    });





    //========================= sliders=========================//
    Route::controller(SliderController::class)->group(function () {
        Route::get('sliders', 'index');
        Route::get('sliders/{slug}', 'getSliderBySlug');
    });

    //========================= flash-sales=========================//
    Route::controller(FlashSaleController::class)->group(function () {
        Route::get('flash-sales', 'index')->name('general-flash-sale-index');
        Route::get('flash-sales/{slug}', 'getFlashSaleBySlug')->name('general-flash-sale-show');
    });

    //======================== categories=========================//
    Route::controller(CategoryController::class)->group(function () {
        Route::get('categories', 'index')->name('general-category-index');
        Route::get('categories/{slug}', 'getCategoryBySlug')->name('general-category-show');
    });

    //======================== promotions=========================//
    Route::controller(PromotionController::class)->group(function () {
        Route::get('promotions', 'index')->name('general-promotion-index');
        Route::get('promotions/{slug}', 'getPromotionBySlug')->name('general-promotion-show');
    });

    //========================= banners=========================//
    Route::controller(BannerController::class)->group(function () {
        Route::get('banners', 'index')->name('general-banner-index');
        Route::get('banners/{slug}', 'getBannerBySlug')->name('general-banner-show');
    });

    //========================= coupons=========================//
    Route::controller(CouponController::class)->group(function () {
        Route::get('coupons', 'index')->name('general-coupon-index');
        Route::post('coupons/apply', 'applyCoupon')->middleware('auth:sanctum');
    });

    //========================= brands=========================//
    Route::controller(BrandController::class)->group(function () {
        Route::get('brands', 'index')->name('general-brand-index');
        Route::get('brands-with-products', 'getBrandsProductsByQtySet')->name('general-brand-with-products');
        Route::get('brands/{slug}', 'getBrandBySlug')->name('general-brand-show');
    });


    //========================= faqs=========================//
    Route::get('faqs', [FAQController::class, 'index']);




    //========================= content-pages=========================//
    Route::controller(ContentPageController::class)->group(function () {
        Route::get('content-pages', 'index')->name('general-content-page-index');
        Route::get('content-pages/{slug}', 'show')->name('general-content-page-show');
    });


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



Route::get('product-type', function () {
    return [

        'index',
        'best_product_sales',
        'brands_product',
        'new_arrivals',
        'all_product_discounts',
        'product_discount_today_or_low_qty',
        'flash_sales_product',
        'flash_sales_end_today',
        'product_for_parent_category',
        'flash_sales_end_week',
    ];
});
Route::get('check-card-payment', function () {
    return [
        'CardNumber' => '2223000000000007',
        'CardExpiryMonthand year' => '01/39',
        'CardCVV' => '100',
    ];
});
Route::get('/test-mail', function () {

    Mail::raw('Brevo Test Mail', function ($message) {

        $message->to('mohtareq1999m@email.com')
                ->subject('Test Email');
    });

    return 'sent';
});