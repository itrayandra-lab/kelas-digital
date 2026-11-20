<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\User;
use App\Support\CourseProgress;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModuleStepOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_puts_the_pre_test_after_the_overview_video(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $category = CourseCategory::create(['name' => 'Marketing', 'description' => '-']);
        $course = Course::create([
            'title' => 'Kelas Urutan', 'instructor' => 'Tim', 'description' => '-', 'course_type' => 'paid', 'price' => 50000,
            'thumbnail' => 'default-course.jpg', 'trailer_video_id' => '', 'course_category_id' => $category->id, 'level' => 'Beginner',
        ]);
        $section = CourseSection::create(['course_id' => $course->id, 'title' => 'Modul 1', 'order' => 1]);
        $lesson = fn (string $title, int $order) => Lesson::create(['course_id' => $course->id, 'section_id' => $section->id, 'title' => $title, 'type' => 'text', 'content' => '<p>x</p>', 'order' => $order, 'module' => 'Modul 1']);
        [$overview, $materi] = [$lesson('Video overview', 1), $lesson('Materi', 2)];
        foreach (['pre', 'post'] as $kind) {
            $section->questions()->create(['course_id' => $course->id, 'kind' => $kind, 'type' => 'single', 'question' => '?', 'order' => 1]);
        }

        $learner = User::factory()->create(['username' => 'peserta'])->assignRole('student');
        Enrollment::create(['user_id' => $learner->id, 'course_id' => $course->id, 'status' => 'active', 'payment_status' => 'completed', 'payment_method' => 'midtrans']);
        $order = fn () => (new CourseProgress($course->fresh(), $learner))->steps->map(fn ($s) => $s['type'] === 'lesson' ? $s['lesson']->title : $s['type'])->all();

        $this->assertSame(['pre', 'Video overview', 'Materi', 'post'], $order());

        $admin = User::factory()->create(['username' => 'staf'])->assignRole('admin');
        $this->actingAs($admin)->put(route('admin.sections.update', $section), ['pretest_after' => 1])->assertSessionHasNoErrors();
        $this->assertSame(['Video overview', 'pre', 'Materi', 'post'], $order());

        // The overview opens first; the next lesson waits for the pre-test.
        $progress = new CourseProgress($course->fresh(), $learner);
        $this->assertTrue($progress->isUnlocked($overview));
        $this->assertFalse($progress->testUnlocked($section, 'pre'));
        $learner->completedLessons()->attach($overview->id, ['completed_at' => now()]);
        $progress = new CourseProgress($course->fresh(), $learner);
        $this->assertTrue($progress->testUnlocked($section, 'pre'));
        $this->assertFalse($progress->isUnlocked($materi));

        // Renaming the module leaves the order alone.
        $this->put(route('admin.sections.update', $section), ['title' => 'Modul Satu'])->assertSessionHasNoErrors();
        $this->assertSame(1, $section->fresh()->pretest_after);
    }
}
