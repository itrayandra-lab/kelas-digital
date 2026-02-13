<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CourseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Personal Branding',
            'Digital Marketing',
            'Business & Strategy',
            'Leadership',
            'Technology & AI',
            'Content & Social Media',
        ];

        foreach ($categories as $name) {
            \App\Models\CourseCategory::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'slug' => Str::slug($name),
                ]
            );
        }
    }
}

