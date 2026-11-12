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
        Http::fake(['*' => Http::response(['choices' => [['message' => ['content' => json_encode(['skor' => $correct ? 100 : 0, 'alasan' => 'Maknanya sesuai.'])]]]])]);
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

        // A new learner lands on the class introduction first, with the module list on the side.
        $this->get(route('course.learn', $slug))->assertRedirect(route('course.intro', $slug));
        $this->get(route('course.intro', $slug))->assertOk()
            ->assertSee('Pengenalan Kelas')->assertSee('Mekanisme belajar')->assertSee('Materi yang akan dibahas')->assertSee('Daftar Modul');

        // Bab 1 starts with its pre-test; lessons and the post-test are locked until then.
        $this->get(route('course.learn', $slug))->assertRedirect(route('course.test', [$slug, $this->bab1, 'pre']));
        $this->get(route('course.test', [$slug, $this->bab1, 'pre']))->assertOk()->assertSee('Materi yang akan dibahas di bab ini')->assertSee('Bacaan');
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
        $this->get(route('course.test', [$slug, $this->bab1, 'pre']))->assertOk()->assertSee('id="q-seconds">10<', false)->assertSee('data-no-copy', false);

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
                return Http::response(['choices' => [['message' => ['content' => '{"skor": 0, "alasan": "Meleset."}']]]]);
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
        $this->actingAs($admin)->put(route('admin.attempts.review', $pending), ['marks' => [$essayId => '0.7']])->assertSessionHasErrors('marks.'.$essayId);
        $this->put(route('admin.attempts.review', $pending), ['marks' => [$essayId => '0.5']])->assertSessionHasNoErrors();
        $this->assertSame(50, $pending->fresh()->score);
        $this->get(route('admin.curriculum.show', $this->course).'?tab=jawaban')->assertOk()->assertSee('Sebagian');
        $this->put(route('admin.attempts.review', $pending), ['marks' => [$essayId => '1']])->assertSessionHasNoErrors();
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

    public function test_a_short_video_completes_even_if_the_admin_typed_a_longer_duration(): void
    {
        $slug = $this->course->slug;
        // The admin typed "1" minute for what is really a 15-second Short.
        $this->video->update(['duration' => 1]);
        $this->actingAs($this->student());
        $this->takeTest($this->bab1, 'pre', $this->answersFor($this->bab1, 'pre', true));

        $this->get(route('course.lesson', [$slug, $this->video]))->assertOk();
        foreach ([5, 10, 15] as $t) {
            $this->travel(5)->seconds();
            $this->postJson(route('course.lesson.progress', [$slug, $this->video]), ['position' => $t, 'duration' => 15])->assertOk();
        }

        $this->postJson(route('course.lesson.complete', [$slug, $this->video]))->assertOk();
    }

    public function test_question_grading_rules(): void
    {
        $multiple = CourseQuestion::where('type', 'multiple')->with('options')->first();
        $evens = $multiple->options->where('is_correct', true)->pluck('id')->all();

        $odd = $multiple->options->where('is_correct', false)->pluck('id')->first();
        $single = CourseQuestion::where('type', 'single')->where('kind', 'pre')->with('options')->first();
        $singleRight = $single->options->where('is_correct', true)->pluck('id')->first();

        // "Pick all that apply": full, partial, cancelled out by a wrong pick, empty.
        $this->assertSame(1.0, $multiple->scoreFor(array_reverse($evens)));
        $this->assertSame(0.5, $multiple->scoreFor([$evens[0]]));
        $this->assertSame(0.0, $multiple->scoreFor([$evens[0], $odd]));
        $this->assertSame(0.5, $multiple->scoreFor([$evens[0], $evens[1], $odd]));
        $this->assertSame(0.0, $multiple->scoreFor([]));
        // Single choice stays all or nothing; essays are never auto-scored.
        $this->assertSame(1.0, $single->scoreFor($singleRight));
        $this->assertSame(0.0, $single->scoreFor([$singleRight, $single->options->where('is_correct', false)->pluck('id')->first()]));
        $this->assertSame(0.0, (new CourseQuestion(['type' => 'essay']))->scoreFor('apa saja'));
    }

    /**
     * The case reported from the Digital Marketing class: 2 of 3 correct picks plus an essay the AI
     * scored 55 must earn points, not zero.
     */
    public function test_partial_answers_earn_partial_points(): void
    {
        $section = $this->course->sections()->create(['title' => 'Bab DM', 'order' => 3]);
        $multi = $this->question($section, 'post', 'multiple', 'Channel digital marketing?', ['Media sosial' => true, 'SEO' => true, 'Brosur' => false, 'Email marketing' => true]);
        $essay = $this->question($section, 'post', 'essay', 'Tujuan email marketing?', 'Menjaga hubungan dengan pelanggan yang sudah mengenal brand.');
        $picked = $multi->options->whereIn('text', ['Media sosial', 'SEO'])->pluck('id')->all();

        $attempt = new QuizAttempt(['kind' => 'post', 'answers' => [$multi->id => $picked, $essay->id => 'berkomunikasi dengan pelanggan lewat email']]);

        // Through the AI path, exactly as a learner's submission is finished.
        Http::fake(['*' => Http::response(['choices' => [['message' => ['content' => '{"skor": 55, "alasan": "Mendekati."}']]]])]);
        $attempt->finish(collect([$multi, $essay]), 60, app(\App\Support\EssayGrader::class));

        $this->assertSame('graded', $attempt->status);
        $this->assertSame(61, $attempt->score); // (2/3 + 0.55) / 2
        $this->assertTrue($attempt->passed);
        $this->assertStringContainsString('Skor 55/100', $attempt->ai_feedback[$essay->id]);
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

    /**
     * The whole job an admin does by hand, through the same requests the admin forms send,
     * followed by a student taking the new course from enrolment to the completion page.
     */
    public function test_admin_creates_a_course_from_scratch_and_a_student_finishes_it(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = User::factory()->create(['username' => 'creator']);
        $admin->assignRole('admin');
        $this->actingAs($admin);

        // 1. Course details. A paid course without a price is rejected.
        $details = [
            'title' => 'Kelas Uji Coba', 'instructor' => 'Admin', 'description' => 'Deskripsi kelas.',
            'course_type' => 'paid', 'trailer_video_id' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'course_category_id' => $this->course->course_category_id, 'level' => 'Beginner',
            'benefits' => "Poin satu\nPoin dua",
        ];
        $this->post(route('admin.courses.store'), $details)->assertSessionHasErrors('price');
        $response = $this->post(route('admin.courses.store'), $details + ['price' => 25000])->assertSessionHasNoErrors();
        $course = Course::where('title', 'Kelas Uji Coba')->firstOrFail();
        $response->assertRedirect(route('admin.curriculum.show', $course));
        $this->assertSame('dQw4w9WgXcQ', $course->trailer_video_id);

        // 2. Sections and lessons.
        $this->post(route('admin.sections.store', $course), ['title' => 'Bab A']);
        $this->post(route('admin.sections.store', $course), ['title' => 'Bab B']);
        [$babA, $babB] = $course->sections()->get()->all();
        $this->post(route('admin.curriculum.lessons.store', $babA), ['title' => 'Video A', 'type' => 'video', 'youtube_video_id' => 'https://youtu.be/dQw4w9WgXcQ', 'duration' => 2, 'is_preview' => '1'])->assertSessionHasNoErrors();
        $this->post(route('admin.curriculum.lessons.store', $babA), ['title' => 'Bacaan A', 'type' => 'text', 'content' => '<p>Materi A</p>'])->assertSessionHasNoErrors();
        $this->post(route('admin.curriculum.lessons.store', $babB), ['title' => 'Bacaan B', 'type' => 'text', 'content' => '<p>Materi B</p>'])->assertSessionHasNoErrors();

        // Edit a lesson, reorder lessons, then delete and re-add one.
        $bacaanA = Lesson::where('title', 'Bacaan A')->firstOrFail();
        $this->put(route('admin.curriculum.lessons.update', $bacaanA), ['title' => 'Bacaan A (revisi)', 'type' => 'text', 'content' => '<p>Materi A revisi</p>'])->assertSessionHasNoErrors();
        $this->post(route('admin.curriculum.lessons.move', [$bacaanA, 'up']));
        $this->assertSame(['Bacaan A (revisi)', 'Video A'], $babA->lessons()->pluck('title')->all());
        $this->post(route('admin.curriculum.lessons.move', [$bacaanA->fresh(), 'down']));
        $tmp = $babB->lessons()->create(['title' => 'Hapus saya', 'type' => 'text', 'content' => 'x', 'order' => 9]);
        $this->delete(route('admin.curriculum.lessons.destroy', $tmp))->assertSessionHasNoErrors();

        // 3. Tests per section, every question type, then edit, reorder and delete.
        $q = fn ($section, $kind, $data) => $this->post(route('admin.questions.store', [$section, $kind]), $data)->assertSessionHasNoErrors();
        $q($babA, 'pre', ['type' => 'single', 'question' => 'A-pre-1', 'options' => [['text' => 'benar'], ['text' => 'salah']], 'correct' => ['0']]);
        $q($babA, 'pre', ['type' => 'true_false', 'question' => 'A-pre-2', 'correct' => ['true']]);
        $q($babA, 'post', ['type' => 'multiple', 'question' => 'A-post-1', 'options' => [['text' => 'x'], ['text' => 'y'], ['text' => 'z']], 'correct' => ['0', '2']]);
        $q($babA, 'post', ['type' => 'essay', 'question' => 'A-post-2', 'answer_key' => 'Kunci A']);
        $q($babB, 'pre', ['type' => 'multiple', 'question' => 'B-pre-1', 'options' => [['text' => 'p'], ['text' => 'q']], 'correct' => ['0', '1']]);
        $q($babB, 'post', ['type' => 'single', 'question' => 'B-post-1', 'options' => [['text' => 'm'], ['text' => 'n']], 'correct' => ['1']]);

        $essay = CourseQuestion::where('question', 'A-post-2')->firstOrFail();
        $this->put(route('admin.questions.update', $essay), ['type' => 'essay', 'question' => 'A-post-2 (revisi)', 'answer_key' => 'Kunci A revisi'])->assertSessionHasNoErrors();
        $this->assertSame('Kunci A revisi', $essay->fresh()->answer_key);
        $this->post(route('admin.questions.move', [$essay, 'up']));
        $this->assertSame(['A-post-2 (revisi)', 'A-post-1'], $babA->testQuestions('post')->pluck('question')->all());
        $this->post(route('admin.questions.move', [$essay->fresh(), 'down']));
        $tmpQ = $babB->questions()->create(['course_id' => $course->id, 'kind' => 'post', 'type' => 'true_false', 'question' => 'hapus', 'order' => 9]);
        $this->delete(route('admin.questions.destroy', $tmpQ))->assertSessionHasNoErrors();

        // Every admin page renders with the new content.
        foreach (['kurikulum', 'tes', 'jawaban'] as $tab) {
            $this->get(route('admin.curriculum.show', $course).'?tab='.$tab)->assertOk()->assertSee('Bab A')->assertSee('A-post-2 (revisi)');
        }
        $this->get(route('admin.courses.index'))->assertOk()->assertSee('Kelas Uji Coba');
        $this->get(route('admin.courses.edit', $course->id))->assertOk();

        // 4. A student buys nothing (enrolled directly), follows the flow and finishes.
        $student = User::factory()->create(['username' => 'learner']);
        $student->assignRole('student');
        Enrollment::create([
            'user_id' => $student->id, 'course_id' => $course->id, 'status' => 'active',
            'enrolled_at' => now(), 'payment_status' => 'completed', 'payment_method' => 'free',
        ]);
        $this->actingAs($student);
        $this->fakeAi(true);
        $this->course = $course;

        $this->get(route('course.show', $course->slug))->assertOk()->assertSee('Bab A')->assertSee('Pre-test')->assertSee('Mulai Belajar');
        $this->takeTest($babA, 'pre', $this->answersFor($babA, 'pre', true));
        foreach ($babA->lessons()->get() as $lesson) {
            $this->finishLesson($lesson);
        }
        $this->takeTest($babA, 'post', $this->answersFor($babA, 'post', true))->assertRedirect(route('course.learn', $course->slug));
        $this->takeTest($babB, 'pre', $this->answersFor($babB, 'pre', true));
        $this->finishLesson($babB->lessons()->first());
        $this->takeTest($babB, 'post', $this->answersFor($babB, 'post', true));

        $this->get(route('course.learn', $course->slug))->assertRedirect(route('course.completed', $course->slug));
        $this->get(route('course.completed', $course->slug))->assertOk()->assertSee('Selamat');

        // The admin sees the student's attempts, with the AI's reasoning on the essay.
        $this->actingAs($admin)->get(route('admin.curriculum.show', $course).'?tab=jawaban')
            ->assertOk()->assertSee($student->email)->assertSee('Maknanya sesuai.');
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
