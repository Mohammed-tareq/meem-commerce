<?php

namespace Database\Seeders;

use Database\Seeders\ContactSeeder;
use Database\Seeders\ProductVariantSeeder;
use Database\Seeders\ReviewSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Marvel\Database\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $seedDemoData = filter_var(
            env('SEED_DEMO_DATA', app()->environment('local')),
            FILTER_VALIDATE_BOOLEAN
        );

        // Keep permission roles and default admin user in sync across environments.
        $this->call([
            PermissionSeeder::class,
            SettingSeeder::class,
        ]);

        $user = User::firstOrCreate([
            'email' => 'admin@demo.com',
        ], [
            'name' => 'Shop Owner',
            'password' => Hash::make('password'),
            'is_active' => true,
            'shop_id' => null,
            'email_verified_at' => now(),
        ]);
        $userEdit = User::firstOrCreate([
            'email' => 'editor@cms.com',
        ], [
            'name' => 'Shop Owner',
            'password' => Hash::make('password'),
            'is_active' => true,
            'shop_id' => null,
            'email_verified_at' => now(),
        ]);

        $customer = User::firstOrCreate([
            'email' => 'test@g.com',
        ], [
            'name' => 'Test Customer',
            'password' => Hash::make('password'),
            'is_active' => true,
            'shop_id' => null,
            'email_verified_at' => now(),
        ]);

        $user->assignRole("super_admin");
        $userEdit->assignRole("editor");

        // if ($seedDemoData) {
        //     User::factory(10000)->create();
        // }

        $this->call([
            // ShopSeeder::class,
            CategorySeeder::class,
            AttributeSeeder::class,
            BannerSeeder::class,
            SliderSeeder::class,
            FaqSeeder::class,
            FlashSaleSeeder::class,
            BrandSeeder::class,
            ContactSeeder::class,
            ProductSeeder::class,
            ReviewSeeder::class,
            ProductVariantSeeder::class,
            BrandProductSeeder::class,
            CartSeeder::class,
            CouponSeeder::class,
            LocationSeeder::class,
            // PromotionSeeder::class,
            // ShopRelationsSeeder::class,
            WishlistSeeder::class,
        ]);
    }
}
