<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Personal Branding',
            'Digital Marketing',
            'Business Strategy',
            'Leadership & Management',
            'Technology & AI',
            'Content Creation',
            'Entrepreneurship',
            'Productivity & Growth',
        ];

        foreach ($categories as $name) {
            \App\Models\ArticleCategory::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'slug' => Str::slug($name),
                ]
            );
        }
    }
}

