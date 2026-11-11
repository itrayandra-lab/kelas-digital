<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\QuizAttempt;
use App\Support\CourseProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LessonController extends Controller
{
    /**
     * Single entry point for "Mulai/Lanjut Belajar": sends the learner to whatever step is next.
     */
    public function learn(string $slug)
    {
        $course = Course::where('slug', $slug)->firstOrFail();

        if (! CourseProgress::canAccessCourse(auth()->user(), $course)) {
            return redirect()->route('course.show', $slug)->with('error', 'Daftar kelas ini dulu untuk mulai belajar.');
        }

        $progress = new CourseProgress($course, auth()->user());

        if ($progress->hasTests && ! $progress->preTestDone) {
            return redirect()->route('course.test', [$slug, 'pre']);
        }

        if ($lesson = $progress->nextLesson()) {
            return redirect()->route('course.lesson', [$slug, $lesson]);
        }

        if ($progress->hasTests && ! $progress->postTestPassed()) {
            return redirect()->route('course.test', [$slug, 'post']);
        }

        if ($progress->isCourseFinished()) {
            return redirect()->route('course.completed', $slug);
        }

        return redirect()->route('course.show', $slug)->with('message', 'Materi kelas ini belum tersedia.');
    }

    public function show(string $slug, Lesson $lesson)
    {
        $course = Course::where('slug', $slug)->firstOrFail();

        if ($lesson->course_id !== $course->id) {
            abort(404);
        }

        $user = auth()->user();
        $hasAccess = CourseProgress::canAccessCourse($user, $course);

        if (! $hasAccess && ! $lesson->is_preview) {
            return redirect()->route('course.show', $course->slug)
                ->with('error', 'Daftar kelas ini dulu untuk membuka materi.');
        }

        $progress = $hasAccess ? new CourseProgress($course, $user) : null;

        if ($progress && ! $progress->isUnlocked($lesson)) {
            return redirect()->route('course.learn', $course->slug)
                ->with('error', 'Selesaikan langkah sebelumnya dulu.');
        }

        $sections = $course->sections()->with('lessons')->get();

        $watch = null;
        if ($progress) {
            $watch = LessonProgress::firstOrCreate(
                ['user_id' => $user->id, 'lesson_id' => $lesson->id],
                ['started_at' => now()],
            );
            // Each page view starts a new watching session: time spent away doesn't count as watching.
            $watch->update(['last_ping_at' => now()]);
        }

        return view('lessons.show', compact('course', 'lesson', 'sections', 'progress', 'watch'));
    }

    /**
     * The player reports its position every few seconds; the server decides how much counts as watched.
     */
    public function ping(Request $request, string $slug, Lesson $lesson)
    {
        $data = $request->validate([
            'position' => 'required|numeric|min:0|max:86400',
            'duration' => 'required|numeric|min:0|max:86400',
        ]);

        $watch = $this->unlockedProgress($slug, $lesson);
        $watch->recordPing((int) $data['position'], (int) $data['duration']);
        $watch->save();

        return response()->json([
            'watched' => $watch->watched_seconds,
            'can_complete' => $watch->videoWatchedEnough($lesson),
        ]);
    }

    public function complete(string $slug, Lesson $lesson)
    {
        $watch = $this->unlockedProgress($slug, $lesson);

        if (! $watch->completed_at) {
            $ok = $lesson->isVideo() ? $watch->videoWatchedEnough($lesson) : $watch->readLongEnough($lesson);

            if (! $ok) {
                return response()->json(['message' => $lesson->isVideo()
                    ? 'Video belum ditonton sampai selesai. Lanjutkan menonton dulu, ya.'
                    : 'Baca materinya dulu sampai selesai, ya.'], 422);
            }

            $watch->update(['completed_at' => now()]);
        }

        return response()->json(['next' => route('course.learn', $slug)]);
    }

    private function unlockedProgress(string $slug, Lesson $lesson): LessonProgress
    {
        $course = Course::where('slug', $slug)->firstOrFail();
        $user = auth()->user();

        abort_if($lesson->course_id !== $course->id || ! CourseProgress::canAccessCourse($user, $course), 403);
        abort_unless((new CourseProgress($course, $user))->isUnlocked($lesson), 403);

        return LessonProgress::firstOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            ['started_at' => now(), 'last_ping_at' => now()],
        );
    }

    public function test(string $slug, string $kind)
    {
        [$course, $progress] = $this->testContext($slug, $kind);

        $questions = $course->questions()->with('options')->get();
        $lastAttempt = QuizAttempt::where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->where('kind', $kind)
            ->latest()
            ->first();

        return view('lessons.test', compact('course', 'kind', 'questions', 'progress', 'lastAttempt'));
    }

    public function submitTest(Request $request, string $slug, string $kind)
    {
        // One submission at a time per learner and course: double clicks or a second tab must not
        // create a second pre-test or post-test attempt. The checks below run inside the lock.
        $lock = Cache::lock('quiz-submit:'.auth()->id().':'.$slug, 15);

        if (! $lock->get()) {
            return redirect()->route('course.learn', $slug)->with('message', 'Jawabanmu sedang diproses.');
        }

        try {
            [$course] = $this->testContext($slug, $kind);

            $questions = $course->questions()->with('options')->get();
            $answers = (array) $request->input('answers', []);

            $request->validate(
                $questions->mapWithKeys(fn ($q) => ["answers.{$q->id}" => 'required'])->all(),
                ['answers.*.required' => 'Semua soal wajib dijawab.'],
            );

            $attempt = new QuizAttempt([
                'user_id' => auth()->id(),
                'course_id' => $course->id,
                'kind' => $kind,
                'answers' => $questions->mapWithKeys(fn ($q) => [$q->id => $answers[$q->id] ?? null])->all(),
            ]);
            $attempt->grade($questions, $course->post_test_pass_score);
            $attempt->save();
        } finally {
            $lock->release();
        }

        if ($kind === 'pre') {
            return redirect()->route('course.learn', $slug)->with('message', $attempt->isPending()
                ? 'Pre-test terkirim. Nilaimu sedang diverifikasi, materi sudah bisa kamu pelajari. Selamat belajar!'
                : "Pre-test selesai. Skor awal kamu: {$attempt->score}. Selamat belajar!");
        }

        if ($attempt->isPending()) {
            return redirect()->route('course.test', [$slug, 'post']);
        }

        if ($attempt->passed) {
            return redirect()->route('course.completed', $slug);
        }

        return redirect()->route('course.test', [$slug, 'post'])
            ->with('error', "Skor kamu {$attempt->score}, minimal {$course->post_test_pass_score} untuk lulus. Pelajari lagi materinya lalu coba lagi.");
    }

    public function completed(string $slug)
    {
        $course = Course::where('slug', $slug)->firstOrFail();
        $user = auth()->user();

        if (! CourseProgress::canAccessCourse($user, $course)) {
            return redirect()->route('course.show', $slug);
        }

        $progress = new CourseProgress($course, $user);

        if (! $progress->isCourseFinished()) {
            return redirect()->route('course.learn', $slug);
        }

        $preTest = QuizAttempt::where('user_id', $user->id)->where('course_id', $course->id)
            ->where('kind', 'pre')->oldest()->first();

        return view('lessons.completed', compact('course', 'progress', 'preTest'));
    }

    private function testContext(string $slug, string $kind): array
    {
        abort_unless(in_array($kind, ['pre', 'post'], true), 404);

        $course = Course::where('slug', $slug)->firstOrFail();
        $user = auth()->user();

        abort_unless(CourseProgress::canAccessCourse($user, $course), 403);

        $progress = new CourseProgress($course, $user);

        abort_unless($progress->hasTests, 404);

        if ($kind === 'pre' && $progress->preTestDone) {
            abort(redirect()->route('course.learn', $slug));
        }

        if ($kind === 'post' && ! $progress->postTestUnlocked()) {
            abort(redirect()->route('course.learn', $slug)->with('error', 'Selesaikan semua materi dulu sebelum post-test.'));
        }

        if ($kind === 'post' && $progress->postTestPassed()) {
            abort(redirect()->route('course.completed', $slug));
        }

        // While a post-test waits for essay review the learner sees its status, not a new form.
        if ($kind === 'post' && $progress->postTestPending && request()->isMethod('post')) {
            abort(redirect()->route('course.test', [$slug, 'post']));
        }

        return [$course, $progress];
    }
}
