<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Marvel\Database\Models\Settings;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Settings::firstOr(function () {
            Settings::create([
                "site_name" => [
                    'ar' => 'اسم الموقع',
                    'en' => 'Site Name',
                ],
                "site_desc" => [
                    'ar' => 'وصف الموقع',
                    'en' => 'Site Description',
                ],
                "meta_desc" => [
                    'ar' => 'وصف الموقع للبحث',
                    'en' => 'Site Description for Search',
                ],
                "site_copy_right" => [
                    'ar' => 'حقوق الموقع',
                    'en' => 'Site Copyright',
                ],
                "logo" => 'logo.png',
                "favicon" => 'favicon.png',
                "site_email" => 'info@example.com',
                "email_support" => 'support@example.com',
                "facebook" => 'https://www.facebook.com/example',
                "instagram" => 'https://www.instagram.com/example',
                "linkedin" => 'https://www.linkedin.com/company/example',
                "promotion_video_url" => 'https://www.youtube.com/watch?v=example',
                'youtube' => 'https://www.youtube.com/channel/example',
                'phone' => '+2011111111111'
            ]);
        });
    }
}
