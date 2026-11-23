<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_classes_carry_their_learning_family_and_faqs_start_closed(): void
    {
        $category = CourseCategory::create(['name' => 'Technology & AI']);
        $course = Course::create([
            'title' => 'AI untuk Produktivitas Kerja', 'instructor' => 'Jane Doe', 'description' => 'Belajar AI.',
            'course_type' => 'free', 'price' => 0, 'thumbnail' => 'default-course.jpg', 'trailer_video_id' => 'dQw4w9WgXcQ', 'course_category_id' => $category->id,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('<span class="nc-family">amAIzing Class</span>', false)
            ->assertSee(route('course.show', $course->slug))
            ->assertSee('Sekolah Kosmetik Indonesia')
            ->assertSee('Apa itu RayAcademy dan bidang apa saja yang bisa dipelajari?')
            ->assertDontSee('aria-expanded="true"', false);
    }
}
