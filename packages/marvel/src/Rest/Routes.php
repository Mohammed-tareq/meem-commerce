<?php

use Illuminate\Support\Facades\Route;

use Marvel\Http\Controllers\AttributeController;
use Marvel\Http\Controllers\AttributeValueController;
use Marvel\Http\Controllers\BannerController;
use Marvel\Http\Controllers\BrandController;
use Marvel\Http\Controllers\ContactController;
use Marvel\Http\Controllers\CartController;
use Marvel\Http\Controllers\CategoryController;
use Marvel\Http\Controllers\CityController;
use Marvel\Http\Controllers\CouponController;
use Marvel\Http\Controllers\CmsPageController;
use Marvel\Http\Controllers\FaqsController;
use Marvel\Http\Controllers\FlashSaleController;
use Marvel\Http\Controllers\Order\OrderController;
use Marvel\Http\Controllers\ProductController;
use Marvel\Http\Controllers\ProductImportController;
use Marvel\Http\Controllers\PromotionController;
use Marvel\Http\Controllers\ReviewController;
use Marvel\Http\Controllers\RoleAndPermissionController;
use Marvel\Http\Controllers\SettingsController;
use Marvel\Http\Controllers\SliderController;
use Marvel\Http\Controllers\SectionController;
use Marvel\Http\Controllers\SectionTypeController;
use Marvel\Http\Controllers\UserController;
use Marvel\Http\Controllers\WishlistController;
use Marvel\Http\Controllers\ContentPageController;
use Marvel\Http\Controllers\CountryController;
use Marvel\Http\Controllers\FastShippingController;
use Marvel\Http\Controllers\GovernorateController;
use Marvel\Http\Controllers\ProductExportController;




/**
 * Authentication Routes - Rate Limited (10/min per IP)
 * Protects against brute force and credential stuffing
 */
Route::middleware(['throttle:auth'])->group(function () {
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/token', [UserController::class, 'token']);
    Route::post('/admin-login', [UserController::class, 'adminToken']);
    Route::post('/social-login-token', [UserController::class, 'socialLogin']);
});
Route::get('me', [UserController::class, 'me'])->middleware('auth:sanctum');
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware(['throttle:sensitive'])->group(function () {
    Route::post('/forget-password', [UserController::class, 'forgetPassword']);
    Route::post('/verify-forget-password-token', [UserController::class, 'verifyForgetPasswordToken']);
    Route::post('/reset-password', [UserController::class, 'resetPassword']);
    Route::post('/contact-us', [ContactController::class, 'store']);
});


Route::middleware(['throttle:otp'])->group(function () {
    Route::post('/send-otp-code', [UserController::class, 'sendUserOtp']);
    Route::post('/otp-login', [UserController::class, 'otpLogin']);
});



Route::group(
    ['middleware' => ['auth:sanctum', 'email.verified']],
    function () {
        //==================================== user change password =============================================//
        Route::post('/change-password', [UserController::class, 'changePassword']);

        //==================================== admin users controllers =============================================//
        Route::post('admin-users/add', [UserController::class, 'adminAddUsers']);
        Route::put('admin-users/update-activation', [UserController::class, 'adminUpdateActivationUsers']);
        Route::delete('admin-users/delete/{id}', [UserController::class, 'adminDeleteUsers']);
        Route::put('admin-users/restore/{id}', [UserController::class, 'adminRestoreUser']);
        Route::delete('admin-users/delete-forever/{id}', [UserController::class, 'adminDeleteUsersForever']);

        //==================================== fast shipping settings =============================================//
        Route::get('fast-shipping/settings', [FastShippingController::class, 'getSettings']);
        Route::put('fast-shipping/settings', [FastShippingController::class, 'updateSettings']);

        //==================================== products controllers =============================================//
        Route::get('products/export', [ProductExportController::class, 'export'])->name('admin.products.export');
        Route::post('products/import', [ProductImportController::class, 'import'])->name('admin.products.import');
        Route::get('products/import/{id}', [ProductImportController::class, 'status'])->name('admin.products.import.status');
        Route::get('products/import/{id}/download-errors', [ProductImportController::class, 'downloadErrors'])->name('admin.products.import.download-errors');
        Route::delete('products/all', [ProductController::class, 'destroyAll']);
        Route::post('products/bulk-delete', [ProductController::class, 'destroyBulk']);
        Route::put('products/{id}/fast-shipping', [ProductController::class, 'toggleFastShipping']);
        Route::apiResource('products', ProductController::class);
        Route::post('reviews/{id}/toggle-approve', [ReviewController::class, 'toggleApproveReview']);
        Route::apiResource('reviews', ReviewController::class);

        //==================================== categories controllers =============================================//
        Route::put('categories/feature', [CategoryController::class, 'addOrRemoveCategoryFromFeature']);
        Route::apiResource('categories', CategoryController::class);
        Route::get('categories-parent', [CategoryController::class, 'fetchOnlyParent']);
        //==================================== brands controllers =============================================//
        Route::put('brands/reorder', [BrandController::class, 'reorder']);
        Route::apiResource('brands', BrandController::class);
        
        //==================================== contacts controllers =============================================//
        Route::delete('contacts/delete-all', [ContactController::class, 'deleteAll']);
        Route::delete('contacts/delete-all-read', [ContactController::class, 'deleteAllReadContacts']);
        Route::apiResource('contacts', ContactController::class)->except(['update']);
        Route::post('contacts/{id}/replay', [ContactController::class, 'sendReplay']);

        //==================================== coupons controllers =============================================//
        Route::post('coupons/add-to-cart', [CouponController::class, 'addCouponToCart']);
        Route::apiResource('coupons', CouponController::class);

        //==================================== promotions controllers =============================================//
        Route::apiResource('promotions', PromotionController::class);

        //==================================== attributes and values  controllers =============================================//
        Route::apiResource('attributes', AttributeController::class);
        Route::apiResource('attribute-values', AttributeValueController::class);

        //==================================== settings  controllers =============================================//
        Route::apiResource('settings', SettingsController::class);

        //==================================== banner  controllers =============================================//
        Route::post('banner/change-status', [BannerController::class, 'changeStatus']);
        Route::post('banner/reorder', [BannerController::class, 'reorder']);
        Route::apiResource('banners', BannerController::class);

        //==================================== sliders  controllers =============================================//
        Route::patch('sliders/change-status', [SliderController::class, 'changeStatus']);
        Route::put('sliders/reorder', [SliderController::class, 'reorder']);
        Route::apiResource('sliders', SliderController::class);

        //==================================== order  controllers =============================================//
        Route::get('orders', [OrderController::class, 'index']);
        Route::get('orders/{id}', [OrderController::class, 'show']);

        //==================================== faqs  controllers =============================================//
        Route::put('faqs/reorder', [FaqsController::class, 'reorder']);
        Route::apiResource('faqs', FaqsController::class);
        
        //==================================== flash sales  controllers =============================================//
        Route::put('flash-sale/reorder', [FlashSaleController::class, 'reorder']);
        Route::get('product-flash-sale-info', [FlashSaleController::class, 'getFlashSaleInfoByProductID']);
        Route::apiResource('flash-sale', FlashSaleController::class);
        
        
        //==================================== countries  controllers =============================================//
        Route::apiResource('countries', CountryController::class);
        Route::get('countries/{id}/governorates', [CountryController::class, 'governorates']);
        Route::post('countries/change-status', [CountryController::class, 'bulkStatus']);
        
        //==================================== governorte  controllers =============================================//
        Route::apiResource('governorates', GovernorateController::class);
        Route::get('governorates/{id}/cities', [GovernorateController::class, 'cities']);
        Route::post('governorates/change-status', [GovernorateController::class, 'bulkStatus']);
        Route::put('governorates/{id}/fast-shipping', [GovernorateController::class, 'toggleFastShipping']);
        
        //==================================== cities  controllers =============================================//
        Route::apiResource('cities', CityController::class);
        
        //==================================== cms  controllers =============================================//
        Route::post('cms-pages', [CmsPageController::class, 'store']);
        Route::put('cms-pages/{id}', [CmsPageController::class, 'update']);
        Route::delete('cms-pages/{id}', [CmsPageController::class, 'destroy']);
        Route::post('content-pages/{content_page}/attach-sections', [ContentPageController::class, 'attachSections']);
        Route::patch('content-pages/{content_page}/toggle-active', [ContentPageController::class, 'toggleActive']);
        Route::apiResource('content-pages', ContentPageController::class);
        //==================================== sections for cms   controllers =============================================//
        Route::post('sections/reorder', [SectionController::class, 'reorder']);
        Route::get('sections/types', [SectionController::class, 'getTypeSection']);
        Route::patch('sections/{section}/toggle-active', [SectionController::class, 'toggleStatus']);
        Route::apiResource('sections', SectionController::class);
        Route::apiResource('section-types', SectionTypeController::class);

        //==================================== sections type  controllers =============================================//
        Route::get('section-types/{type}/settings', [SectionTypeController::class, 'settings']);
        
        //==================================== wishlist  controllers =============================================//
        Route::post('wishlists/toggle', [WishlistController::class, 'toggle']);
        Route::apiResource('wishlists', WishlistController::class);
        Route::get('wishlists/in_wishlist/{product_id}', [WishlistController::class, 'in_wishlist']);
        Route::get('my-wishlists', [ProductController::class, 'myWishlists']);
        
        //==================================== roles and permissions  controllers =============================================//
        Route::get('/roles', [RoleAndPermissionController::class, 'getAllRoles']);
        Route::get('/roles/{id}', [RoleAndPermissionController::class, 'showRole']);
        Route::post('/roles', [RoleAndPermissionController::class, 'addRole']);
        Route::put('/roles/{id}', [RoleAndPermissionController::class, 'updateRole']);
        Route::delete('/roles/{id}', [RoleAndPermissionController::class, 'destroyRole']);
        Route::post('/users/{userId}/assign-role', [RoleAndPermissionController::class, 'assignRole']);
        Route::post('/users/{userId}/remove-role', [RoleAndPermissionController::class, 'removeRoleFromUser']);

        Route::get('/permissions', [RoleAndPermissionController::class, 'getAllPermissions']);
        Route::post('/roles/{roleId}/permissions', [RoleAndPermissionController::class, 'assignPermissionToRole']);
        Route::post('/users/{userId}/permissions', [RoleAndPermissionController::class, 'givePermission']);
        Route::put('/users/{userId}/permissions', [RoleAndPermissionController::class, 'syncPermissions']);
        Route::delete('/users/{userId}/permissions', [RoleAndPermissionController::class, 'removePermission']);
    }
);

Route::middleware(['auth:sanctum', "throttle:cart"])->group(function () {
    Route::get('cart', [CartController::class, 'index']);
    Route::post('cart', [CartController::class, 'store']);
    Route::post('cart/bulk-items', [CartController::class, 'pluckItemsToCart']);
    Route::put('cart/update-item', [CartController::class, 'update']);
    Route::delete('cart/delete-item/{itemId}', [CartController::class, 'deleteItemFromCart']);
    Route::delete('cart/delete-items', [CartController::class, 'destroy']);
});

// Route::group([
//     'middleware' => [
//         'auth:sanctum',
//         'verified',
//     ]
// ], function () {

//     // Route::post('approve-coupon', [CouponController::class, 'approveCoupon']);
//     // Route::post('disapprove-coupon', [CouponController::class, 'disApproveCoupon']);
// });



// Route::group(['middleware' => ['auth:sanctum', 'email.verified']], function () {
//     // Route::post('/update-email', [UserController::class, 'updateUserEmail']);
//     // Route::put('users/{id}', [UserController::class, 'update']);

//     // Route::post('/change-password', [UserController::class, 'changePassword']);
//     // Route::apiResource('notify-logs', NotifyLogsController::class);
//     // Route::post('notify-log-seen', [NotifyLogsController::class, 'readNotifyLogs']);
//     // Route::post('notify-log-read-all', [NotifyLogsController::class, 'readAllNotifyLogs']);

// });