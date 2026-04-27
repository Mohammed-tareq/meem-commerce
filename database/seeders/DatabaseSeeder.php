<?php

namespace Database\Seeders;

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

        $user->assignRole("super_admin");

       
        $this->call([
            ShopSeeder::class,
            CategorySeeder::class,
            AttributeSeeder::class,
            BannerSeeder::class,
            FaqSeeder::class,
            FlashSaleSeeder::class,
            ProductSeeder::class,
            CartSeeder::class,
            CouponSeeder::class,
        ]);
    }
}
