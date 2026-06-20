<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryIds = CourseCategory::pluck('id', 'slug');

        // Create Ray Academy courses
        Course::updateOrCreate(
            ['slug' => 'personal-branding-fundamentals'],
            [
                'title' => 'Personal Branding Fundamentals: Build Your Authentic Identity',
                'instructor' => 'Ria R. Christiana SE, MBA',
                'description' => 'Bangun personal brand yang autentik dan powerful. Pelajari cara mengidentifikasi unique value proposition, membangun narasi yang kuat, dan mengkomunikasikan brand Anda secara konsisten di berbagai platform. Cocok untuk profesional, entrepreneur, dan siapa saja yang ingin meningkatkan visibility.',
                'price' => 350000,
                'thumbnail' => 'default-course.jpg',
                'trailer_video_id' => 'u-kE8gRENqI',
                'course_category_id' => $categoryIds['personal-branding'] ?? null,
                'level' => 'Beginner',
                'is_featured' => true,
            ]
        );

        Course::updateOrCreate(
            ['slug' => 'digital-marketing-strategy-data-driven'],
            [
                'title' => 'Digital Marketing Strategy: From Data to Action',
                'instructor' => 'Wendra Wilendra M.MT',
                'description' => 'Master strategi digital marketing berbasis data. Dari analytics, SEO, social media marketing, hingga conversion optimization. Pelajari cara membuat kampanye yang terukur dan menghasilkan ROI tinggi. Termasuk studi kasus real dan tools praktis.',
                'price' => 450000,
                'thumbnail' => 'default-course.jpg',
                'trailer_video_id' => 'dQw4w9WgXcQ',
                'course_category_id' => $categoryIds['digital-marketing'] ?? null,
                'level' => 'Intermediate',
                'is_featured' => true,
            ]
        );

        Course::updateOrCreate(
            ['slug' => 'psikologi-komunikasi-bisnis'],
            [
                'title' => 'Psikologi Komunikasi untuk Profesional dan Leader',
                'instructor' => 'Sukmayanti Ranadireksa, M.Psi',
                'description' => 'Pahami psikologi di balik komunikasi efektif. Pelajari cara membaca body language, mengelola konflik, memberikan feedback konstruktif, dan membangun rapport. Essential untuk leader, manager, dan siapa saja yang bekerja dengan tim.',
                'price' => 400000,
                'thumbnail' => 'default-course.jpg',
                'trailer_video_id' => 'dQw4w9WgXcQ',
                'course_category_id' => $categoryIds['leadership'] ?? null,
                'level' => 'Intermediate',
                'is_featured' => true,
            ]
        );

        Course::updateOrCreate(
            ['slug' => 'brand-identity-design-thinking'],
            [
                'title' => 'Brand Identity Design: From Concept to Execution',
                'instructor' => 'Ria R. Christiana SE, MBA',
                'description' => 'Rancang brand identity yang memorable dan konsisten. Dari logo, color palette, typography, hingga brand guidelines. Pelajari design thinking process dan cara menerjemahkan brand personality ke visual identity yang kuat.',
                'price' => 500000,
                'thumbnail' => 'default-course.jpg',
                'trailer_video_id' => 'dQw4w9WgXcQ',
                'course_category_id' => $categoryIds['business-strategy'] ?? null,
                'level' => 'Advanced',
            ]
        );

        Course::updateOrCreate(
            ['slug' => 'content-strategy-social-media'],
            [
                'title' => 'Content Strategy & Social Media Management Mastery',
                'instructor' => 'Wendra Wilendra M.MT',
                'description' => 'Kuasai seni dan sains content creation untuk social media. Dari content planning, copywriting, visual design, hingga analytics. Pelajari cara membuat konten yang engaging, viral-worthy, dan menghasilkan conversion.',
                'price' => 380000,
                'thumbnail' => 'default-course.jpg',
                'trailer_video_id' => 'dQw4w9WgXcQ',
                'course_category_id' => $categoryIds['content-social-media'] ?? null,
                'level' => 'Intermediate',
                'is_featured' => true,
            ]
        );

        Course::updateOrCreate(
            ['slug' => 'leadership-team-management'],
            [
                'title' => 'Leadership Excellence: Building High-Performance Teams',
                'instructor' => 'Sukmayanti Ranadireksa, M.Psi',
                'description' => 'Kembangkan leadership skills untuk memimpin tim yang produktif dan engaged. Pelajari cara memotivasi, mendelegasi, mengelola performa, dan menciptakan budaya kerja yang positif. Dengan pendekatan psikologi organisasi.',
                'price' => 550000,
                'thumbnail' => 'default-course.jpg',
                'trailer_video_id' => 'dQw4w9WgXcQ',
                'course_category_id' => $categoryIds['leadership'] ?? null,
                'level' => 'Advanced',
            ]
        );

        Course::updateOrCreate(
            ['slug' => 'ai-for-business-productivity'],
            [
                'title' => 'AI for Business: Boost Productivity & Innovation',
                'instructor' => 'Wendra Wilendra M.MT',
                'description' => 'Manfaatkan AI untuk meningkatkan efisiensi bisnis. Dari ChatGPT untuk content creation, automation tools, hingga AI analytics. Pelajari cara mengintegrasikan AI dalam workflow tanpa coding. Praktis dan langsung applicable.',
                'price' => 420000,
                'thumbnail' => 'default-course.jpg',
                'trailer_video_id' => 'dQw4w9WgXcQ',
                'course_category_id' => $categoryIds['technology-ai'] ?? null,
                'level' => 'Beginner',
            ]
        );

        Course::updateOrCreate(
            ['slug' => 'parenting-digital-era'],
            [
                'title' => 'Smart Parenting di Era Digital: Panduan Lengkap',
                'instructor' => 'dr. Frecillia Regina, Sp.A',
                'description' => 'Panduan komprehensif untuk orang tua modern. Pelajari cara mengajarkan literasi digital, mengelola screen time, melindungi anak dari cyberbullying, dan membangun komunikasi yang sehat. Berbasis riset dan praktik terbaik.',
                'price' => 300000,
                'thumbnail' => 'default-course.jpg',
                'trailer_video_id' => 'dQw4w9WgXcQ',
                'course_category_id' => $categoryIds['leadership'] ?? null,
                'level' => 'Beginner',
            ]
        );
    }
}
