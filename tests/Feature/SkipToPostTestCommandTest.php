<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Support\CourseProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SkipToPostTestCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_takes_a_test_account_to_the_module_post_test_and_can_run_twice(): void
    {
        $category = CourseCategory::create(['name' => 'Marketing', 'description' => '-']);
        $course = Course::create([
            'title' => 'Kelas Uji', 'instructor' => 'Tim', 'description' => '-', 'course_type' => 'paid', 'price' => 100000,
            'thumbnail' => 'default-course.jpg', 'trailer_video_id' => '', 'course_category_id' => $category->id, 'level' => 'Beginner',
        ]);
        $section = CourseSection::create(['course_id' => $course->id, 'title' => 'Modul Satu', 'order' => 1, 'is_free' => true]);
        foreach ([1, 2] as $n) {
            Lesson::create(['course_id' => $course->id, 'section_id' => $section->id, 'title' => "Materi $n", 'type' => 'text', 'content' => '<p>x</p>', 'order' => $n, 'module' => 'Modul Satu']);
        }
        foreach (['pre', 'post'] as $kind) {
            $section->questions()->create(['course_id' => $course->id, 'kind' => $kind, 'type' => 'single', 'question' => 'Q?', 'order' => 1]);
        }
        $user = User::factory()->create(['email' => 'uji@example.com', 'username' => 'uji']);

        foreach ([1, 2] as $run) {
            $this->artisan('course:skip-to-post-test', ['email' => 'uji@example.com', 'course' => $course->slug])
                ->expectsOutputToContain('Langkah berikutnya: Post-test Modul Satu')->assertSuccessful();
        }

        $this->assertSame(1, QuizAttempt::where('user_id', $user->id)->where('kind', 'pre')->count());
        $this->assertSame('trial', $user->enrollments()->first()->payment_method);
        $progress = new CourseProgress($course, $user);
        $this->assertSame('post', $progress->nextStep()['type']);
        $this->assertTrue($progress->testUnlocked($section, 'post'));
    }
}
