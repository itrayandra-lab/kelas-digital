<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            'branding',
            'marketing',
            'social-media',
            'leadership',
            'productivity',
            'ai',
            'business',
            'strategy',
            'content',
            'digital',
            'entrepreneurship',
            'growth',
            'communication',
            'team-building',
            'innovation',
        ];

        foreach ($tags as $name) {
            Tag::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => Str::title(str_replace('-', ' ', $name)),
                    'slug' => Str::slug($name),
                ]
            );
        }
    }
}
