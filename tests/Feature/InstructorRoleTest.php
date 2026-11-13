<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstructorRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_instructors_manage_courses_curriculum_and_tests_only(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $category = CourseCategory::create(['name' => 'Marketing', 'description' => '-']);
        $course = Course::create([
            'title' => 'Kelas Instruktur', 'instructor' => 'Orang lain', 'description' => 'Kelas.', 'course_type' => 'paid', 'price' => 50000,
            'thumbnail' => 'default-course.jpg', 'trailer_video_id' => '', 'course_category_id' => $category->id, 'level' => 'Beginner',
        ]);
        $first = CourseSection::create(['course_id' => $course->id, 'title' => 'Modul 01', 'order' => 1]);
        $second = CourseSection::create(['course_id' => $course->id, 'title' => 'Modul 02', 'order' => 2]);
        Lesson::create(['course_id' => $course->id, 'section_id' => $first->id, 'title' => 'Materi 1', 'type' => 'text', 'content' => '<p>a</p>', 'order' => 1]);
        $later = Lesson::create(['course_id' => $course->id, 'section_id' => $second->id, 'title' => 'Materi 2', 'type' => 'text', 'content' => '<p>b</p>', 'order' => 1]);

        $this->actingAs(User::factory()->create(['username' => 'guru'])->assignRole('instructor'));

        // Admin panel: courses and their curriculum (where essays are marked) are open.
        $this->get(route('admin.dashboard'))->assertOk();
        $this->get(route('admin.courses.index'))->assertOk()->assertSee('Kelas Instruktur');
        $this->get(route('admin.curriculum.show', $course))->assertOk();
        $this->post(route('admin.sections.store', $course), ['title' => 'Modul 03'])->assertSessionHasNoErrors();

        // Everything else stays closed.
        foreach (['admin.users.index', 'admin.payments.index', 'admin.vouchers.index', 'admin.site-settings.index', 'admin.course-categories.index', 'admin.articles.index'] as $name) {
            $this->get(route($name))->assertForbidden();
        }
        $this->delete(route('admin.courses.destroy', $course))->assertForbidden();

        // Course pages: any lesson opens in any order, without enrolling and without recording progress.
        $this->get(route('course.lesson', [$course->slug, $later]))->assertOk()->assertSee('Mode admin');
        $this->assertDatabaseCount('lesson_progress', 0);
        $this->assertDatabaseCount('enrollments', 0);
    }

    public function test_content_managers_manage_articles_only(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $this->actingAs(User::factory()->create(['username' => 'penulis'])->assignRole('content-manager'));

        foreach (['admin.dashboard', 'admin.articles.index', 'admin.article-categories.index', 'admin.tags.index', 'admin.hero-slider.index'] as $name) {
            $this->get(route($name))->assertOk();
        }
        foreach (['admin.site-settings.index', 'admin.courses.index', 'admin.users.index', 'admin.payments.index', 'admin.vouchers.index'] as $name) {
            $this->get(route($name))->assertForbidden();
        }

        $this->assertFalse(User::factory()->create(['username' => 'murid'])->assignRole('student')->can('manage site settings'));
    }
}
