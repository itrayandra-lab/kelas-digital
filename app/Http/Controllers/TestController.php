<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\QuizAttempt;
use App\Support\CourseProgress;
use App\Support\EssayGrader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Pre-test and post-test of one section, taken like an exam: one question at a time with a timer.
 * The server owns the clock (which question is on screen and when it started), so refreshing,
 * going back or opening a second tab can't buy extra time or a second attempt.
 */
class TestController extends Controller
{
    public function show(string $slug, CourseSection $section, string $kind)
    {
        [$course, $progress] = $this->context($slug, $section, $kind);

        if ($progress->reviewer) {
            $questions = $section->testQuestions($kind)->with('options')->get();

            return view('tests.preview', compact('course', 'section', 'kind', 'questions', 'progress'));
        }

        $attempt = $this->withLock($slug, function () use ($course, $section, $kind) {
            $attempt = $this->openAttempt($course, $section, $kind);
            $current = $attempt?->questions()->get($attempt->current_index);

            // The learner left the current question until its time ran out: it stays empty.
            if ($current && $attempt->timeIsUp($current)) {
                $attempt->answerCurrent($attempt->questions(), null);
                $this->saveOrFinish($attempt, $course);
            } elseif ($attempt && $attempt->isFinished($attempt->questions())) {
                // Questions were removed by an admin mid-attempt; nothing is left to show.
                $this->saveOrFinish($attempt, $course);
            }

            return $attempt?->fresh();
        });

        if ($attempt && ! $attempt->isInProgress()) {
            return $this->afterFinish($attempt, $course, $section);
        }

        $questions = $section->testQuestions($kind)->with('options')->get();

        if ($attempt) {
            return view('tests.question', [
                'course' => $course, 'section' => $section, 'kind' => $kind, 'attempt' => $attempt, 'progress' => $progress,
                'question' => $questions->get($attempt->current_index), 'total' => $questions->count(),
            ]);
        }

        $lastAttempt = QuizAttempt::where('user_id', auth()->id())->where('section_id', $section->id)
            ->where('kind', $kind)->latest()->first();
        $pending = $kind === 'post' && $progress->postTestPending($section);

        return view('tests.intro', compact('course', 'section', 'kind', 'questions', 'progress', 'lastAttempt', 'pending'));
    }

    public function start(string $slug, CourseSection $section, string $kind)
    {
        [$course, $progress] = $this->context($slug, $section, $kind);

        if ($progress->reviewer || ($kind === 'post' && $progress->postTestPending($section))) {
            return redirect()->route('course.test', [$slug, $section, $kind]);
        }

        $this->withLock($slug, function () use ($course, $section, $kind) {
            if (! $this->openAttempt($course, $section, $kind)) {
                QuizAttempt::create([
                    'user_id' => auth()->id(),
                    'course_id' => $course->id,
                    'section_id' => $section->id,
                    'kind' => $kind,
                    'status' => 'in_progress',
                    'current_index' => 0,
                    'question_started_at' => now(),
                    'passed' => false,
                    'answers' => [],
                ]);
            }
        });

        return redirect()->route('course.test', [$slug, $section, $kind]);
    }

    public function answer(Request $request, string $slug, CourseSection $section, string $kind)
    {
        [$course, $progress] = $this->context($slug, $section, $kind);

        if ($progress->reviewer) {
            return redirect()->route('course.test', [$slug, $section, $kind]);
        }

        $attempt = $this->withLock($slug, function () use ($request, $course, $section, $kind) {
            $attempt = $this->openAttempt($course, $section, $kind);

            // Only the question on screen can be answered; a resubmitted or stale form is ignored.
            if ($attempt && (int) $request->input('index') === $attempt->current_index) {
                $attempt->answerCurrent($attempt->questions(), $request->input('answer'));
                $this->saveOrFinish($attempt, $course);
            }

            return $attempt?->fresh();
        });

        if ($attempt && ! $attempt->isInProgress()) {
            return $this->afterFinish($attempt, $course, $section);
        }

        return redirect()->route('course.test', [$slug, $section, $kind]);
    }

    private function saveOrFinish(QuizAttempt $attempt, Course $course): void
    {
        $questions = $attempt->questions();

        if ($attempt->isFinished($questions)) {
            $attempt->finish($questions, $course->post_test_pass_score, app(EssayGrader::class));
        }

        $attempt->save();
    }

    private function afterFinish(QuizAttempt $attempt, Course $course, CourseSection $section)
    {
        $label = ($attempt->kind === 'pre' ? 'Pre-test ' : 'Post-test ').$section->title;

        if ($attempt->isPending()) {
            return redirect()->route('course.test', [$course->slug, $section, $attempt->kind]);
        }

        if ($attempt->kind === 'pre') {
            return redirect()->route('course.learn', $course->slug)
                ->with('message', "{$label} selesai. Skor awal kamu: {$attempt->score}. Selamat belajar!");
        }

        if ($attempt->passed) {
            return redirect()->route('course.learn', $course->slug)
                ->with('message', "Selamat, kamu lulus {$label} dengan skor {$attempt->score}!");
        }

        return redirect()->route('course.test', [$course->slug, $section, 'post'])
            ->with('error', "Skor kamu {$attempt->score}, minimal {$course->post_test_pass_score} untuk lulus. Pelajari lagi materi bab ini lalu coba lagi.");
    }

    private function openAttempt(Course $course, CourseSection $section, string $kind): ?QuizAttempt
    {
        return QuizAttempt::where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->where('section_id', $section->id)
            ->where('kind', $kind)
            ->where('status', 'in_progress')
            ->first();
    }

    /** One write at a time per learner and course, so double clicks or two tabs can't race. */
    private function withLock(string $slug, callable $callback): mixed
    {
        return Cache::lock('quiz:'.auth()->id().':'.$slug, 15)->block(10, $callback);
    }

    private function context(string $slug, CourseSection $section, string $kind): array
    {
        $course = Course::where('slug', $slug)->firstOrFail();
        $user = auth()->user();

        abort_unless($section->course_id === $course->id, 404);
        abort_unless(CourseProgress::canStart($user, $course), 403);

        $progress = new CourseProgress($course, $user);

        abort_unless($progress->sectionHasTest($section, $kind), 404);

        if (! $progress->testUnlocked($section, $kind)) {
            abort(redirect()->route('course.learn', $slug)->with('error', 'Selesaikan langkah sebelumnya dulu.'));
        }

        $done = $kind === 'pre' ? $progress->preTestDone($section) : $progress->postTestPassed($section);

        // Learners take a finished test once (pre) or until passed (post); admins only preview tests.
        if ($done && ! $progress->reviewer && ! $this->openAttempt($course, $section, $kind)) {
            abort(redirect()->route('course.learn', $slug));
        }

        return [$course, $progress];
    }
}
