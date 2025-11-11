<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Contact Information
            [
                'key' => 'contact_email',
                'value' => 'info@kelasdigital.com',
                'type' => 'string',
            ],
            [
                'key' => 'contact_phone',
                'value' => '+62 123 456 7890',
                'type' => 'string',
            ],
            [
                'key' => 'contact_address',
                'value' => 'Bandung, Jawa Barat, Indonesia',
                'type' => 'string',
            ],

            // Social Media URLs
            [
                'key' => 'social_facebook',
                'value' => 'https://www.facebook.com/beautyversitydotid',
                'type' => 'string',
            ],
            [
                'key' => 'social_twitter',
                'value' => 'https://x.com/beautyversityid',
                'type' => 'string',
            ],
            [
                'key' => 'social_instagram',
                'value' => 'https://www.instagram.com/beautyversity_id',
                'type' => 'string',
            ],
            [
                'key' => 'social_youtube',
                'value' => 'https://www.youtube.com/@beautyversitydotid',
                'type' => 'string',
            ],
            [
                'key' => 'social_tiktok',
                'value' => null,
                'type' => 'string',
            ],
            [
                'key' => 'social_whatsapp',
                'value' => null,
                'type' => 'string',
            ],
            [
                'key' => 'social_linkedin',
                'value' => null,
                'type' => 'string',
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
