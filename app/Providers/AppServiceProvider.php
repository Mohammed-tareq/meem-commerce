<?php

namespace App\Providers;

use App\Contexts\ChannelContext;
use App\Models\Scopes\FastShippingScope;
use App\Observers\BrandObserver;
use App\Observers\CategoryObserver;
use App\Observers\CouponObserver;
use App\Observers\FlashSaleObserver;
use App\Observers\ProductObserver;
use App\Observers\PromotionObserver;
use App\Observers\RoleObserver;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Marvel\Database\Models\Brand;
use Marvel\Database\Models\Category;
use Marvel\Database\Models\Coupon;
use Marvel\Database\Models\FlashSale;
use Marvel\Database\Models\Product;
use Marvel\Database\Models\Promotion;
use Marvel\Database\Models\Role;
use Marvel\Database\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->scoped(ChannelContext::class, function () {
            return new ChannelContext();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        if (!\App::environment('local')) {
            $this->app['request']->server->set('HTTPS', true);
        }

        ini_set('serialize_precision', -1);

        if (DB::connection()->getDriverName() === 'sqlite') {
            DB::connection()->getPdo()->sqliteCreateFunction('REGEXP_REPLACE', function ($subject, $pattern, $replacement) {
                return preg_replace('/' . $pattern . '/', $replacement, $subject ?? '');
            }, 3);
        }

        Product::observe(ProductObserver::class);
        Category::observe(CategoryObserver::class);
        Brand::observe(BrandObserver::class);
        Coupon::observe(CouponObserver::class);
        FlashSale::observe(FlashSaleObserver::class);
        Promotion::observe(PromotionObserver::class);
        Role::observe(RoleObserver::class);
        User::observe(UserObserver::class);

        Product::addGlobalScope(new FastShippingScope());
    }
}
