<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseTypeTest extends TestCase
{
    use RefreshDatabase;

    private function createCategory(): CourseCategory
    {
        return CourseCategory::create([
            'name' => 'Teknologi',
            'slug' => 'teknologi',
            'description' => 'Kelas teknologi',
        ]);
    }

    private function createPaidCourse(CourseCategory $category): Course
    {
        return Course::create([
            'title' => 'Laravel Mastery',
            'instructor' => 'John Doe',
            'description' => 'Belajar Laravel dari dasar.',
            'course_type' => 'paid',
            'price' => 299000,
            'thumbnail' => 'default-course.jpg',
            'trailer_video_id' => 'abc123',
            'course_category_id' => $category->id,
            'level' => 'Beginner',
        ]);
    }

    private function createFreeClass(CourseCategory $category): Course
    {
        return Course::create([
            'title' => 'Data Analysis Short Class',
            'instructor' => 'Jane Doe',
            'description' => 'Kelas gratis data analysis.',
            'course_type' => 'free',
            'price' => 0,
            'thumbnail' => 'default-course.jpg',
            'trailer_video_id' => '',
            'course_category_id' => $category->id,
            'level' => 'Beginner',
            'benefits' => "✅ Gratis E-Certificate\n✅ Live Class dan Q&A",
            'topics_preview' => "• Pengenalan Data Analysis\n• Tools yang digunakan",
            'schedule_start' => now()->addDays(7),
            'schedule_end' => now()->addDays(7)->addHours(2),
            'meeting_platform' => 'Zoom',
        ]);
    }

    private function createUser(): User
    {
        return User::factory()->create(['username' => fake()->unique()->userName()]);
    }

    public function test_paid_course_show_page_displays_video_player(): void
    {
        $category = $this->createCategory();
        $course = $this->createPaidCourse($category);

        $response = $this->get(route('course.show', $course->slug));

        $response->assertStatus(200);
        $response->assertSee('youtube.com/embed', false);
        $response->assertSee('Login untuk Gabung');
        $response->assertSee('Rp');
    }

    public function test_free_class_show_page_displays_registration_info(): void
    {
        $category = $this->createCategory();
        $course = $this->createFreeClass($category);

        $response = $this->get(route('course.show', $course->slug));

        $response->assertStatus(200);
        $response->assertSee('Daftar Sekarang!');
        $response->assertSee('Zoom');
        $response->assertSee('Gratis E-Certificate');
        $response->assertSee('Pengenalan Data Analysis');
        $response->assertDontSee('Gabung Kelas Ini');
    }

    public function test_free_class_enrollment_auto_completes(): void
    {
        $category = $this->createCategory();
        $course = $this->createFreeClass($category);
        $user = $this->createUser();

        $this->actingAs($user)
            ->post(route('course.enroll', $course->slug));

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        $this->assertNotNull($enrollment);
        $this->assertEquals('completed', $enrollment->payment_status);
        $this->assertEquals('free', $enrollment->payment_method);
        $this->assertEquals('active', $enrollment->status);
    }

    public function test_paid_course_enrollment_stays_pending(): void
    {
        $category = $this->createCategory();
        $course = $this->createPaidCourse($category);
        $user = $this->createUser();

        $this->actingAs($user)
            ->post(route('course.enroll', $course->slug));

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        $this->assertNotNull($enrollment);
        $this->assertEquals('pending', $enrollment->payment_status);
        $this->assertEquals('manual_transfer', $enrollment->payment_method);
    }

    public function test_free_class_shows_registered_state_after_enrollment(): void
    {
        $category = $this->createCategory();
        $course = $this->createFreeClass($category);
        $user = $this->createUser();

        Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'active',
            'enrolled_at' => now(),
            'payment_status' => 'completed',
            'payment_method' => 'free',
        ]);

        $response = $this->actingAs($user)
            ->get(route('course.show', $course->slug));

        $response->assertStatus(200);
        $response->assertSee('Anda Sudah Terdaftar');
        $response->assertDontSee('Daftar Sekarang!');
    }

    public function test_course_index_shows_correct_badge_for_free_class(): void
    {
        $category = $this->createCategory();
        $this->createFreeClass($category);
        $this->createPaidCourse($category);

        $response = $this->get(route('course.index'));

        $response->assertStatus(200);
        $response->assertSee('Kelas Gratis');
        $response->assertSee('Premium');
    }

    public function test_course_model_helper_methods(): void
    {
        $category = $this->createCategory();
        $paid = $this->createPaidCourse($category);
        $free = $this->createFreeClass($category);

        $this->assertTrue($paid->isPaidCourse());
        $this->assertFalse($paid->isFreeClass());
        $this->assertTrue($free->isFreeClass());
        $this->assertFalse($free->isPaidCourse());
    }
}
