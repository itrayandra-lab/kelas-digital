<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseQuestion;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseLearningFlowTest extends TestCase
{
    use RefreshDatabase;

    private Course $course;

    private Lesson $video;

    private Lesson $text;

    private CourseQuestion $single;

    private CourseQuestion $multiple;

    protected function setUp(): void
    {
        parent::setUp();

        $category = CourseCategory::create(['name' => 'Bisnis', 'slug' => 'bisnis', 'description' => '-']);
        $this->course = Course::create([
            'title' => 'Personal Branding',
            'instructor' => 'Raya',
            'description' => 'Kelas personal branding.',
            'course_type' => 'paid',
            'price' => 100000,
            'thumbnail' => 'default-course.jpg',
            'trailer_video_id' => 'https://youtu.be/dQw4w9WgXcQ',
            'course_category_id' => $category->id,
            'level' => 'Beginner',
            'post_test_pass_score' => 70,
        ]);

        $bab1 = $this->course->sections()->create(['title' => 'Bab 1', 'order' => 1]);
        $bab2 = $this->course->sections()->create(['title' => 'Bab 2', 'order' => 2]);
        // Created out of order on purpose: study order must follow section order, not insertion order.
        $this->text = $bab2->lessons()->create(['title' => 'Bacaan', 'type' => 'text', 'content' => '<p>Isi</p>', 'order' => 1]);
        $this->video = $bab1->lessons()->create(['title' => 'Video', 'type' => 'video', 'youtube_video_id' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'order' => 1]);

        $this->single = $this->course->questions()->create(['type' => 'single', 'question' => '1+1?', 'order' => 1]);
        $this->single->options()->createMany([['text' => '2', 'is_correct' => true], ['text' => '3', 'is_correct' => false]]);
        $this->multiple = $this->course->questions()->create(['type' => 'multiple', 'question' => 'Bilangan genap?', 'order' => 2]);
        $this->multiple->options()->createMany([['text' => '2', 'is_correct' => true], ['text' => '4', 'is_correct' => true], ['text' => '5', 'is_correct' => false]]);
    }

    private function student(): User
    {
        $user = User::factory()->create(['username' => fake()->unique()->userName()]);
        Enrollment::create([
            'user_id' => $user->id, 'course_id' => $this->course->id, 'status' => 'active',
            'enrolled_at' => now(), 'payment_status' => 'completed', 'payment_method' => 'midtrans',
        ]);

        return $user;
    }

    /**
     * Completes a lesson the honest way: a 100s video is watched in real (travelled) time, a text
     * lesson stays open long enough to be read.
     */
    private function finishLesson(string $slug, Lesson $lesson): void
    {
        $this->get(route('course.lesson', [$slug, $lesson]))->assertOk();

        if ($lesson->isVideo()) {
            for ($t = 10; $t <= 100; $t += 10) {
                $this->travel(10)->seconds();
                $this->postJson(route('course.lesson.progress', [$slug, $lesson]), ['position' => $t, 'duration' => 100])->assertOk();
            }
        } else {
            $this->travel(15)->seconds();
        }

        $this->postJson(route('course.lesson.complete', [$slug, $lesson]))->assertOk();
    }

    private function answers(bool $correct): array
    {
        [$right, $wrong] = [$this->single->options[0]->id, $this->single->options[1]->id];
        $evens = $this->multiple->options->where('is_correct', true)->pluck('id')->all();

        return ['answers' => [
            $this->single->id => $correct ? $right : $wrong,
            $this->multiple->id => $correct ? $evens : [$evens[0]],
        ]];
    }

    public function test_youtube_links_are_stored_as_ids(): void
    {
        $this->assertSame('dQw4w9WgXcQ', $this->video->fresh()->youtube_video_id);
        $this->assertSame('dQw4w9WgXcQ', $this->course->fresh()->trailer_video_id);
    }

    public function test_guest_and_unpaid_users_cannot_open_locked_lessons(): void
    {
        $this->get(route('course.lesson', [$this->course->slug, $this->video]))
            ->assertRedirect(route('course.show', $this->course->slug));

        $unpaid = User::factory()->create(['username' => 'unpaid']);
        $this->actingAs($unpaid)->get(route('course.learn', $this->course->slug))
            ->assertRedirect(route('course.show', $this->course->slug));
    }

    public function test_full_learning_flow_is_enforced_in_order(): void
    {
        $slug = $this->course->slug;
        $this->seed(RolePermissionSeeder::class);
        $this->actingAs($this->student()->assignRole('student'));

        // Pre-test comes first and gates the lessons.
        $this->get(route('course.learn', $slug))->assertRedirect(route('course.test', [$slug, 'pre']));
        $this->get(route('course.lesson', [$slug, $this->video]))->assertRedirect(route('course.learn', $slug));
        $this->get(route('course.test', [$slug, 'post']))->assertRedirect(route('course.learn', $slug));

        // A wrong pre-test still counts as done.
        $this->post(route('course.test.submit', [$slug, 'pre']), $this->answers(false))
            ->assertRedirect(route('course.learn', $slug));

        // Lessons follow section order, and the next one stays locked until the current is complete.
        $this->get(route('course.learn', $slug))->assertRedirect(route('course.lesson', [$slug, $this->video]));
        $this->get(route('course.lesson', [$slug, $this->video]))->assertOk()->assertSee('youtube.com/iframe_api', false);
        $this->get(route('course.lesson', [$slug, $this->text]))->assertRedirect(route('course.learn', $slug));
        $this->postJson(route('course.lesson.complete', [$slug, $this->text]))->assertForbidden();

        $this->finishLesson($slug, $this->video);
        $this->get(route('course.lesson', [$slug, $this->text]))->assertOk()->assertSee('Isi', false);
        $this->finishLesson($slug, $this->text);

        // Post-test: a failing score keeps the learner on the test, a passing one finishes the course.
        $this->get(route('course.learn', $slug))->assertRedirect(route('course.test', [$slug, 'post']));
        $this->post(route('course.test.submit', [$slug, 'post']), $this->answers(false))
            ->assertRedirect(route('course.test', [$slug, 'post']));
        $this->get(route('course.completed', $slug))->assertRedirect(route('course.learn', $slug));

        $this->post(route('course.test.submit', [$slug, 'post']), $this->answers(true))
            ->assertRedirect(route('course.completed', $slug));
        $this->get(route('course.completed', $slug))->assertOk()->assertSee('Selamat');
        $this->get(route('dashboard'))->assertSee('100%');
    }

    public function test_question_grading_rules(): void
    {
        $evens = $this->multiple->options->where('is_correct', true)->pluck('id')->all();

        $this->assertTrue($this->multiple->isAnsweredCorrectly(array_reverse($evens)));
        $this->assertFalse($this->multiple->isAnsweredCorrectly([$evens[0]]));
        $this->assertFalse($this->multiple->isAnsweredCorrectly([]));

        // Essays are never auto-scored; an admin marks them.
        $essay = new CourseQuestion(['type' => 'essay']);
        $this->assertFalse($essay->isAnsweredCorrectly('jawaban apa saja'));
    }

    public function test_lessons_cannot_be_completed_without_really_watching_or_reading(): void
    {
        $slug = $this->course->slug;
        $this->actingAs($this->student());
        $this->post(route('course.test.submit', [$slug, 'pre']), $this->answers(true));

        $this->get(route('course.lesson', [$slug, $this->video]))->assertOk();
        $progress = route('course.lesson.progress', [$slug, $this->video]);
        $complete = route('course.lesson.complete', [$slug, $this->video]);

        // Not watched at all.
        $this->postJson($complete)->assertStatus(422);

        // Claiming the end of the video 3 seconds after opening is capped to what real time allows.
        $this->travel(3)->seconds();
        $this->postJson($progress, ['position' => 100, 'duration' => 100])->assertOk()->assertJson(['can_complete' => false]);
        $this->postJson($complete)->assertStatus(422);

        // Shrinking the reported duration doesn't help: the longest reported length wins.
        $this->postJson($progress, ['position' => 9, 'duration' => 10])->assertOk()->assertJson(['can_complete' => false]);

        // Watching resumes where it left off after a refresh, and time away from the page doesn't count.
        $this->travel(5)->minutes();
        $this->get(route('course.lesson', [$slug, $this->video]))->assertOk()->assertSee('const resumeAt = 9', false);
        $this->travel(2)->seconds();
        $this->postJson($progress, ['position' => 95, 'duration' => 100])->assertOk()->assertJson(['can_complete' => false]);

        // Text lesson: opened but closed again right away is rejected.
        $this->finishLesson($slug, $this->video);
        $this->get(route('course.lesson', [$slug, $this->text]))->assertOk();
        $this->postJson(route('course.lesson.complete', [$slug, $this->text]))->assertStatus(422);
        $this->travel(15)->seconds();
        $this->postJson(route('course.lesson.complete', [$slug, $this->text]))->assertOk();
    }

    public function test_pre_test_can_only_be_submitted_once(): void
    {
        $slug = $this->course->slug;
        $this->actingAs($this->student());

        $this->post(route('course.test.submit', [$slug, 'pre']), $this->answers(true))->assertRedirect(route('course.learn', $slug));
        $this->post(route('course.test.submit', [$slug, 'pre']), $this->answers(false))->assertRedirect(route('course.learn', $slug));

        $this->assertSame(1, \App\Models\QuizAttempt::where('kind', 'pre')->count());
        $this->get(route('course.test', [$slug, 'pre']))->assertRedirect(route('course.learn', $slug));
    }

    public function test_essay_answers_wait_for_admin_review_before_scoring(): void
    {
        $essay = $this->course->questions()->create(['type' => 'essay', 'question' => 'Jelaskan.', 'order' => 3]);
        $slug = $this->course->slug;
        $student = $this->student();
        $this->actingAs($student);

        $answers = fn (bool $ok) => ['answers' => $this->answers($ok)['answers'] + [$essay->id => 'Jawaban essay saya']];

        // Pre-test with an essay: pending, but the lessons still open.
        $this->post(route('course.test.submit', [$slug, 'pre']), $answers(false));
        $this->assertTrue(\App\Models\QuizAttempt::where('kind', 'pre')->first()->isPending());
        $this->get(route('course.learn', $slug))->assertRedirect(route('course.lesson', [$slug, $this->video]));

        $this->finishLesson($slug, $this->video);
        $this->finishLesson($slug, $this->text);

        // Post-test with an essay: no score yet, the course is not finished, and it can't be resubmitted.
        $this->post(route('course.test.submit', [$slug, 'post']), $answers(true))->assertRedirect(route('course.test', [$slug, 'post']));
        $this->get(route('course.test', [$slug, 'post']))->assertOk()->assertSee('Hasilmu sedang diverifikasi')->assertDontSee('Kirim Jawaban');
        $this->post(route('course.test.submit', [$slug, 'post']), $answers(true));
        $this->assertSame(1, \App\Models\QuizAttempt::where('kind', 'post')->count());
        $this->get(route('course.completed', $slug))->assertRedirect(route('course.learn', $slug));

        // Admin marks the essay correct: 3/3 = 100, passed, course finished.
        $this->seed(RolePermissionSeeder::class);
        $admin = User::factory()->create(['username' => 'grader']);
        $admin->assignRole('admin');
        $post = \App\Models\QuizAttempt::where('kind', 'post')->first();

        $this->actingAs($admin)->get(route('admin.curriculum.show', $this->course).'?tab=jawaban')
            ->assertOk()->assertSee('Jawaban essay saya')->assertSee('perlu dinilai');
        $this->put(route('admin.attempts.review', $post), ['marks' => []])->assertSessionHasErrors('marks');
        $this->put(route('admin.attempts.review', $post), ['marks' => [$essay->id => '1']])->assertSessionHasNoErrors();

        $post->refresh();
        $this->assertFalse($post->isPending());
        $this->assertSame(100, $post->score);
        $this->assertTrue($post->passed);

        $this->actingAs($student)->get(route('course.completed', $slug))->assertOk()->assertSee('Selamat');
    }

    public function test_pre_test_requires_every_question_answered(): void
    {
        $this->actingAs($this->student())
            ->post(route('course.test.submit', [$this->course->slug, 'pre']), ['answers' => [$this->single->id => $this->single->options[0]->id]])
            ->assertSessionHasErrors("answers.{$this->multiple->id}");
    }

    public function test_payment_cannot_be_marked_complete_without_a_midtrans_order(): void
    {
        $user = User::factory()->create(['username' => 'buyer']);
        $enrollment = Enrollment::create([
            'user_id' => $user->id, 'course_id' => $this->course->id, 'status' => 'pending',
            'enrolled_at' => now(), 'payment_status' => 'pending', 'payment_method' => 'midtrans',
        ]);

        $this->actingAs($user)->postJson(route('payment.complete', $enrollment), ['order_id' => 'ENR-999-1'])->assertStatus(422);
        $this->assertSame('pending', $enrollment->fresh()->payment_status);
    }

    public function test_admin_builds_curriculum_and_questions(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = User::factory()->create(['username' => 'admin1']);
        $admin->assignRole('admin');
        $this->actingAs($admin);

        $this->get(route('admin.curriculum.show', $this->course))->assertOk()->assertSee('Bab 1');

        $this->post(route('admin.sections.store', $this->course), ['title' => 'Bab 3'])->assertSessionHasNoErrors();
        $bab3 = $this->course->sections()->where('title', 'Bab 3')->firstOrFail();

        $this->post(route('admin.curriculum.lessons.store', $bab3), ['title' => 'Video baru', 'type' => 'video', 'youtube_video_id' => 'https://youtu.be/dQw4w9WgXcQ?si=x'])
            ->assertSessionHasNoErrors();
        $this->post(route('admin.curriculum.lessons.store', $bab3), ['title' => 'Kosong', 'type' => 'text'])
            ->assertSessionHasErrors('content');
        $this->assertSame('dQw4w9WgXcQ', $bab3->lessons()->first()->youtube_video_id);

        // Moving Bab 3 up places it before Bab 2.
        $this->post(route('admin.sections.move', [$bab3, 'up']));
        $this->assertSame(['Bab 1', 'Bab 3', 'Bab 2'], $this->course->sections()->pluck('title')->all());

        $this->post(route('admin.questions.store', $this->course), ['type' => 'true_false', 'question' => 'Langit biru?', 'correct' => ['true']])
            ->assertSessionHasNoErrors();
        $tf = $this->course->questions()->where('type', 'true_false')->first();
        $this->assertSame('Benar', $tf->options()->where('is_correct', true)->value('text'));

        $this->post(route('admin.questions.store', $this->course), ['type' => 'single', 'question' => 'Dua benar?', 'options' => [['text' => 'a'], ['text' => 'b']], 'correct' => ['0', '1']])
            ->assertSessionHasErrors('correct');

        $this->post(route('admin.questions.store', $this->course), ['type' => 'essay', 'question' => 'Jelaskan.'])
            ->assertSessionHasNoErrors();
    }
}
