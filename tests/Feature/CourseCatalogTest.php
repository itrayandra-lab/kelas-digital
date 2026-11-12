<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Enrollment;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseCatalogTest extends TestCase
{
    use RefreshDatabase;

    private function course(CourseCategory $category, string $title, string $type, string $level = 'Beginner'): Course
    {
        return Course::create([
            'title' => $title, 'instructor' => 'Tim', 'description' => "Tentang {$title}.",
            'course_type' => $type, 'price' => $type === 'free' ? 0 : 49000, 'thumbnail' => 'default-course.jpg', 'trailer_video_id' => '',
            'course_category_id' => $category->id, 'level' => $level,
        ]);
    }

    public function test_catalog_filters_by_category_search_type_and_level(): void
    {
        $marketing = CourseCategory::create(['name' => 'Digital Marketing', 'description' => '-']);
        $tech = CourseCategory::create(['name' => 'Technology', 'description' => '-']);
        $this->course($marketing, 'Iklan Instagram', 'paid');
        $this->course($marketing, 'SEO Dasar', 'free', 'Intermediate');
        $this->course($tech, 'Belajar Laravel', 'paid');

        $this->get(route('course.index', ['category' => 'digital-marketing']))
            ->assertOk()->assertSee('Belajar Digital Marketing')->assertSee('Iklan Instagram')->assertDontSee('Belajar Laravel');
        $this->get(route('course.index', ['q' => 'laravel']))->assertSee('Belajar Laravel')->assertDontSee('SEO Dasar');
        $this->get(route('course.index', ['type' => 'free']))->assertSee('SEO Dasar')->assertDontSee('Iklan Instagram');
        $this->get(route('course.index', ['level' => 'Intermediate']))->assertSee('SEO Dasar')->assertDontSee('Belajar Laravel');
        $this->get(route('course.index', ['q' => 'tidak-ada']))->assertSee('Kelas yang kamu cari belum ada');
        // Array values are ignored instead of crashing the page.
        $this->get('/courses?category[]=x&type[]=free')->assertOk()->assertSee('Belajar Laravel');
    }

    public function test_dashboard_lists_courses_waiting_for_payment(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $category = CourseCategory::create(['name' => 'Bisnis', 'description' => '-']);
        $course = $this->course($category, 'Manajemen Waktu', 'paid');
        $user = User::factory()->create(['username' => 'peserta'])->assignRole('student');
        Enrollment::create([
            'user_id' => $user->id, 'course_id' => $course->id, 'status' => 'pending',
            'enrolled_at' => now(), 'payment_status' => 'pending', 'payment_method' => 'midtrans',
        ]);

        $this->actingAs($user)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Menunggu pembayaran (1)')
            ->assertSee('Selesaikan Pembayaran');
    }
}
