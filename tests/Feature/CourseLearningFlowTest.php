<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseQuestion;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\QuizAttempt;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CourseLearningFlowTest extends TestCase
{
    use RefreshDatabase;

    private Course $course;

    private CourseSection $bab1;

    private CourseSection $bab2;

    private Lesson $video;

    private Lesson $text;

    private Lesson $text2;

    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
        config(['services.groq.key' => 'test-groq', 'services.deepseek.key' => 'test-deepseek']);

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

        $this->bab1 = $this->course->sections()->create(['title' => 'Bab 1', 'order' => 1]);
        $this->bab2 = $this->course->sections()->create(['title' => 'Bab 2', 'order' => 2]);
        // Created out of order on purpose: study order must follow section order, not insertion order.
        $this->text2 = $this->bab2->lessons()->create(['title' => 'Bacaan bab 2', 'type' => 'text', 'content' => '<p>Isi dua</p>', 'order' => 1]);
        $this->text = $this->bab1->lessons()->create(['title' => 'Bacaan', 'type' => 'text', 'content' => '<p>Isi</p>', 'order' => 2]);
        $this->video = $this->bab1->lessons()->create(['title' => 'Video', 'type' => 'video', 'youtube_video_id' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'order' => 1]);

        $this->question($this->bab1, 'pre', 'single', '1+1?', ['2' => true, '3' => false]);
        $this->question($this->bab1, 'post', 'single', '2+2?', ['4' => true, '5' => false]);
        $this->question($this->bab1, 'post', 'multiple', 'Bilangan genap?', ['2' => true, '4' => true, '5' => false]);
        $this->question($this->bab2, 'pre', 'true_false', 'Langit biru?', ['Benar' => true, 'Salah' => false]);
        $this->question($this->bab2, 'post', 'essay', 'Jelaskan personal branding.', 'Persepsi orang tentang diri kita yang dibangun secara sadar.');
    }

    private function question(CourseSection $section, string $kind, string $type, string $text, array|string $extra): CourseQuestion
    {
        $q = $section->questions()->create([
            'course_id' => $this->course->id, 'kind' => $kind, 'type' => $type, 'question' => $text,
            'answer_key' => $type === 'essay' ? $extra : null, 'order' => $section->questions()->count() + 1,
        ]);

        foreach ($type === 'essay' ? [] : $extra as $optionText => $correct) {
            $q->options()->create(['text' => (string) $optionText, 'is_correct' => $correct]);
        }

        return $q->load('options');
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

    /** Correct (or deliberately wrong) answers for a section's test, keyed in question order. */
    private function answersFor(CourseSection $section, string $kind, bool $correct, string $essay = 'Jawaban essay saya'): array
    {
        return $section->testQuestions($kind)->with('options')->get()->map(function (CourseQuestion $q) use ($correct, $essay) {
            if ($q->type === 'essay') {
                return $essay;
            }

            $right = $q->options->where('is_correct', true)->pluck('id')->all();
            $wrong = $q->options->where('is_correct', false)->pluck('id')->all();

            return $q->type === 'multiple' ? ($correct ? $right : [$right[0]]) : ($correct ? $right[0] : $wrong[0]);
        })->all();
    }

    /** Takes a whole test the way the browser does: start, then one answer per question. */
    private function takeTest(CourseSection $section, string $kind, array $answers)
    {
        $slug = $this->course->slug;
        $this->get(route('course.test', [$slug, $section, $kind]))->assertOk()->assertSee('detik per soal');
        $this->post(route('course.test.start', [$slug, $section, $kind]));

        $response = null;
        foreach (array_values($answers) as $i => $answer) {
            $this->get(route('course.test', [$slug, $section, $kind]))->assertOk()->assertSee('Soal');
            $response = $this->post(route('course.test.answer', [$slug, $section, $kind]), ['index' => $i, 'answer' => $answer]);
        }

        return $response;
    }

    private function finishLesson(Lesson $lesson): void
    {
        $slug = $this->course->slug;
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

    private function fakeAi(bool $correct): void
    {
        Http::fake(['*' => Http::response(['choices' => [['message' => ['content' => json_encode(['benar' => $correct, 'alasan' => 'Maknanya sesuai.'])]]]])]);
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

        $this->actingAs(User::factory()->create(['username' => 'unpaid']))
            ->get(route('course.learn', $this->course->slug))
            ->assertRedirect(route('course.show', $this->course->slug));
    }

    public function test_each_section_runs_pre_test_lessons_post_test_in_order(): void
    {
        $slug = $this->course->slug;
        $this->seed(RolePermissionSeeder::class);
        $this->actingAs($this->student()->assignRole('student'));
        $this->fakeAi(true);

        // Bab 1 starts with its pre-test; lessons and the post-test are locked until then.
        $this->get(route('course.learn', $slug))->assertRedirect(route('course.test', [$slug, $this->bab1, 'pre']));
        $this->get(route('course.lesson', [$slug, $this->video]))->assertRedirect(route('course.learn', $slug));
        $this->get(route('course.test', [$slug, $this->bab1, 'post']))->assertRedirect(route('course.learn', $slug));

        // A wrong pre-test still counts as done, and can't be taken again.
        $this->takeTest($this->bab1, 'pre', $this->answersFor($this->bab1, 'pre', false))->assertRedirect(route('course.learn', $slug));
        $this->get(route('course.test', [$slug, $this->bab1, 'pre']))->assertRedirect(route('course.learn', $slug));

        // Lessons of bab 1 in order; bab 2 stays locked.
        $this->get(route('course.learn', $slug))->assertRedirect(route('course.lesson', [$slug, $this->video]));
        $this->get(route('course.lesson', [$slug, $this->text]))->assertRedirect(route('course.learn', $slug));
        $this->finishLesson($this->video);
        $this->finishLesson($this->text);
        $this->get(route('course.test', [$slug, $this->bab2, 'pre']))->assertRedirect(route('course.learn', $slug));

        // Bab 1 post-test: failing keeps bab 2 closed, passing opens it.
        $this->get(route('course.learn', $slug))->assertRedirect(route('course.test', [$slug, $this->bab1, 'post']));
        $this->takeTest($this->bab1, 'post', $this->answersFor($this->bab1, 'post', false))
            ->assertRedirect(route('course.test', [$slug, $this->bab1, 'post']));
        $this->get(route('course.learn', $slug))->assertRedirect(route('course.test', [$slug, $this->bab1, 'post']));
        $this->takeTest($this->bab1, 'post', $this->answersFor($this->bab1, 'post', true))->assertRedirect(route('course.learn', $slug));

        // Bab 2: pre-test, lesson, post-test with an AI-graded essay.
        $this->get(route('course.learn', $slug))->assertRedirect(route('course.test', [$slug, $this->bab2, 'pre']));
        $this->takeTest($this->bab2, 'pre', $this->answersFor($this->bab2, 'pre', true));
        $this->finishLesson($this->text2);
        $this->takeTest($this->bab2, 'post', $this->answersFor($this->bab2, 'post', true))->assertRedirect(route('course.learn', $slug));

        $this->get(route('course.learn', $slug))->assertRedirect(route('course.completed', $slug));
        $this->get(route('course.completed', $slug))->assertOk()->assertSee('Selamat')->assertSee('Bab 2');
        $this->get(route('dashboard'))->assertSee('100%');
    }

    public function test_pre_tests_cannot_contain_essays_and_post_essays_need_an_answer_key(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = User::factory()->create(['username' => 'admin1']);
        $admin->assignRole('admin');
        $this->actingAs($admin);

        $this->post(route('admin.questions.store', [$this->bab1, 'pre']), ['type' => 'essay', 'question' => 'Jelaskan.', 'answer_key' => 'x'])
            ->assertSessionHasErrors('type');
        $this->post(route('admin.questions.store', [$this->bab1, 'post']), ['type' => 'essay', 'question' => 'Jelaskan.'])
            ->assertSessionHasErrors('answer_key');
        $this->post(route('admin.questions.store', [$this->bab1, 'post']), ['type' => 'essay', 'question' => 'Jelaskan.', 'answer_key' => 'Kunci'])
            ->assertSessionHasNoErrors();

        $this->assertSame('post', CourseQuestion::where('question', 'Jelaskan.')->first()->kind);
        $this->get(route('admin.curriculum.show', $this->course).'?tab=tes')->assertOk()->assertSee('Kunci:');
    }

    public function test_question_timer_is_enforced_by_the_server(): void
    {
        $slug = $this->course->slug;
        $this->actingAs($this->student());
        $this->post(route('course.test.start', [$slug, $this->bab1, 'pre']));
        $attempt = QuizAttempt::firstOrFail();

        // Refreshing doesn't reset the clock.
        $this->travel(20)->seconds();
        $this->get(route('course.test', [$slug, $this->bab1, 'pre']))->assertOk()->assertSee('id="q-seconds">10<', false);

        // An answer that arrives after the time limit (plus grace) counts as empty.
        $this->travel(15)->seconds();
        $right = $this->answersFor($this->bab1, 'pre', true)[0];
        $this->post(route('course.test.answer', [$slug, $this->bab1, 'pre']), ['index' => 0, 'answer' => $right]);

        $attempt->refresh();
        $this->assertSame('graded', $attempt->status);
        $this->assertNull(array_values($attempt->answers)[0]);
        $this->assertSame(0, $attempt->score);
    }

    public function test_leaving_a_question_until_time_runs_out_moves_on_without_an_answer(): void
    {
        $slug = $this->course->slug;
        $this->actingAs($this->student());
        $this->takeTest($this->bab1, 'pre', $this->answersFor($this->bab1, 'pre', true));
        $this->finishLesson($this->video);
        $this->finishLesson($this->text);

        $this->post(route('course.test.start', [$slug, $this->bab1, 'post']));
        $this->travel(40)->seconds();

        // Coming back after the timer expired skips question 1 and shows question 2.
        $this->get(route('course.test', [$slug, $this->bab1, 'post']))->assertOk()->assertSee('Bilangan genap?');
        $this->assertSame(1, QuizAttempt::where('kind', 'post')->first()->current_index);
    }

    public function test_stale_or_double_submitted_answers_are_ignored(): void
    {
        $slug = $this->course->slug;
        $this->actingAs($this->student());
        $this->takeTest($this->bab1, 'pre', $this->answersFor($this->bab1, 'pre', true));
        $this->finishLesson($this->video);
        $this->finishLesson($this->text);

        $this->post(route('course.test.start', [$slug, $this->bab1, 'post']));
        $this->post(route('course.test.start', [$slug, $this->bab1, 'post']));
        $this->assertSame(1, QuizAttempt::where('kind', 'post')->count());

        [$first, $second] = $this->answersFor($this->bab1, 'post', true);
        $url = route('course.test.answer', [$slug, $this->bab1, 'post']);
        $this->post($url, ['index' => 0, 'answer' => $first]);
        // The same form sent twice must not answer question 2 with question 1's answer.
        $this->post($url, ['index' => 0, 'answer' => $first]);

        $attempt = QuizAttempt::where('kind', 'post')->first();
        $this->assertSame(1, $attempt->current_index);
        $this->assertCount(1, $attempt->answers);

        // Option ids from another question are dropped.
        $this->post($url, ['index' => 1, 'answer' => [$first]]);
        $this->assertNull($attempt->fresh()->answers[CourseQuestion::where('question', 'Bilangan genap?')->value('id')]);
    }

    public function test_essays_are_graded_by_groq_then_deepseek_then_wait_for_an_admin(): void
    {
        $slug = $this->course->slug;
        $student = $this->student();
        $this->actingAs($student);
        $finishBab1 = function () {
            $this->takeTest($this->bab1, 'pre', $this->answersFor($this->bab1, 'pre', true));
            $this->finishLesson($this->video);
            $this->finishLesson($this->text);
            $this->takeTest($this->bab1, 'post', $this->answersFor($this->bab1, 'post', true));
            $this->takeTest($this->bab2, 'pre', $this->answersFor($this->bab2, 'pre', true));
            $this->finishLesson($this->text2);
        };
        $finishBab1();

        // Groq fails, DeepSeek answers: graded via the fallback. Later both go down.
        $deepseekUp = true;
        Http::fake(function ($request) use (&$deepseekUp) {
            if ($deepseekUp && str_contains($request->url(), 'api.deepseek.com')) {
                return Http::response(['choices' => [['message' => ['content' => '{"benar": false, "alasan": "Meleset."}']]]]);
            }

            return Http::response('down', 503);
        });
        $this->takeTest($this->bab2, 'post', $this->answersFor($this->bab2, 'post', true))
            ->assertRedirect(route('course.test', [$slug, $this->bab2, 'post']));
        $attempt = QuizAttempt::where('section_id', $this->bab2->id)->where('kind', 'post')->latest('id')->first();
        $this->assertSame('graded', $attempt->status);
        $this->assertSame(0, $attempt->score);
        $this->assertStringContainsString('deepseek', array_values($attempt->ai_feedback)[0]);
        Http::assertSentCount(2);

        // Both down: the attempt waits for an admin, and the learner sees it's being verified.
        $deepseekUp = false;
        $this->takeTest($this->bab2, 'post', $this->answersFor($this->bab2, 'post', true));
        $pending = QuizAttempt::where('section_id', $this->bab2->id)->where('kind', 'post')->latest('id')->first();
        $this->assertTrue($pending->isPending());
        $this->get(route('course.test', [$slug, $this->bab2, 'post']))->assertSee('Hasilmu sedang diverifikasi')->assertDontSee('Mulai Post-test');

        // The admin marks the essay; the learner finishes.
        $this->seed(RolePermissionSeeder::class);
        $admin = User::factory()->create(['username' => 'grader']);
        $admin->assignRole('admin');
        $essayId = CourseQuestion::where('type', 'essay')->value('id');
        $this->actingAs($admin)->put(route('admin.attempts.review', $pending), ['marks' => [$essayId => '1']])->assertSessionHasNoErrors();
        $this->assertSame(100, $pending->fresh()->score);

        $this->actingAs($student)->get(route('course.completed', $slug))->assertOk();
    }

    public function test_lessons_cannot_be_completed_without_really_watching_or_reading(): void
    {
        $slug = $this->course->slug;
        $this->actingAs($this->student());
        $this->takeTest($this->bab1, 'pre', $this->answersFor($this->bab1, 'pre', true));

        $this->get(route('course.lesson', [$slug, $this->video]))->assertOk();
        $progress = route('course.lesson.progress', [$slug, $this->video]);
        $complete = route('course.lesson.complete', [$slug, $this->video]);

        $this->postJson($complete)->assertStatus(422);
        $this->travel(3)->seconds();
        $this->postJson($progress, ['position' => 100, 'duration' => 100])->assertOk()->assertJson(['can_complete' => false]);
        $this->postJson($progress, ['position' => 9, 'duration' => 10])->assertOk()->assertJson(['can_complete' => false]);

        $this->travel(5)->minutes();
        $this->get(route('course.lesson', [$slug, $this->video]))->assertOk()->assertSee('const resumeAt = 9', false);
        $this->travel(2)->seconds();
        $this->postJson($progress, ['position' => 95, 'duration' => 100])->assertOk()->assertJson(['can_complete' => false]);

        $this->finishLesson($this->video);
        $this->get(route('course.lesson', [$slug, $this->text]))->assertOk();
        $this->postJson(route('course.lesson.complete', [$slug, $this->text]))->assertStatus(422);
        $this->travel(15)->seconds();
        $this->postJson(route('course.lesson.complete', [$slug, $this->text]))->assertOk();
    }

    public function test_question_grading_rules(): void
    {
        $multiple = CourseQuestion::where('type', 'multiple')->with('options')->first();
        $evens = $multiple->options->where('is_correct', true)->pluck('id')->all();

        $this->assertTrue($multiple->isAnsweredCorrectly(array_reverse($evens)));
        $this->assertFalse($multiple->isAnsweredCorrectly([$evens[0]]));
        $this->assertFalse($multiple->isAnsweredCorrectly([]));
        $this->assertFalse((new CourseQuestion(['type' => 'essay']))->isAnsweredCorrectly('apa saja'));
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

    public function test_admin_builds_curriculum(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = User::factory()->create(['username' => 'builder']);
        $admin->assignRole('admin');
        $this->actingAs($admin);

        $this->get(route('admin.curriculum.show', $this->course))->assertOk()->assertSee('Bab 1');

        $this->post(route('admin.sections.store', $this->course), ['title' => 'Bab 3'])->assertSessionHasNoErrors();
        $bab3 = $this->course->sections()->where('title', 'Bab 3')->firstOrFail();

        $this->post(route('admin.curriculum.lessons.store', $bab3), ['title' => 'Video baru', 'type' => 'video', 'youtube_video_id' => 'https://youtu.be/dQw4w9WgXcQ?si=x'])
            ->assertSessionHasNoErrors();
        $this->post(route('admin.curriculum.lessons.store', $bab3), ['title' => 'Kosong', 'type' => 'text'])->assertSessionHasErrors('content');

        $this->post(route('admin.sections.move', [$bab3, 'up']));
        $this->assertSame(['Bab 1', 'Bab 3', 'Bab 2'], $this->course->sections()->pluck('title')->all());

        $this->post(route('admin.questions.store', [$bab3, 'pre']), ['type' => 'true_false', 'question' => 'Langit hijau?', 'correct' => ['false']])
            ->assertSessionHasNoErrors();
        $this->assertSame('Salah', CourseQuestion::where('question', 'Langit hijau?')->first()->options()->where('is_correct', true)->value('text'));

        $this->post(route('admin.questions.store', [$bab3, 'post']), ['type' => 'single', 'question' => 'Dua benar?', 'options' => [['text' => 'a'], ['text' => 'b']], 'correct' => ['0', '1']])
            ->assertSessionHasErrors('correct');
    }
}
