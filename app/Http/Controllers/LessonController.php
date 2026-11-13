<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Support\CourseProgress;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    /**
     * Single entry point for "Mulai/Lanjut Belajar": sends the learner to whatever step is next.
     */
    public function learn(string $slug)
    {
        $course = Course::where('slug', $slug)->firstOrFail();

        if (! CourseProgress::canStart(auth()->user(), $course)) {
            return redirect()->route('course.show', $slug)->with('error', 'Daftar kelas ini dulu untuk mulai belajar.');
        }

        $progress = new CourseProgress($course, auth()->user());
        $step = $progress->nextStep();

        // A learner who hasn't started yet sees the class introduction first.
        if ($progress->percent() === 0 && ! session()->has("intro_seen.{$course->id}")) {
            return redirect()->route('course.intro', $slug);
        }

        if ($progress->needsPayment()) {
            return redirect()->route('course.show', $slug)
                ->with('checkout', 'Bab gratis sudah kamu selesaikan. Beli kelas lengkap untuk lanjut ke '.$step['section']->title.'.');
        }

        if ($step && $step['type'] === 'lesson') {
            return redirect()->route('course.lesson', [$slug, $step['lesson']]);
        }

        if ($step) {
            return redirect()->route('course.test', [$slug, $step['section'], $step['type']]);
        }

        if ($progress->isCourseFinished()) {
            return redirect()->route('course.completed', $slug);
        }

        return redirect()->route('course.show', $slug)->with('message', 'Materi kelas ini belum tersedia.');
    }

    /**
     * "Pengenalan Kelas": what the class covers, its chapters and how studying works.
     */
    public function intro(string $slug)
    {
        $course = Course::where('slug', $slug)->firstOrFail();

        if (! CourseProgress::canStart(auth()->user(), $course)) {
            return redirect()->route('course.show', $slug)->with('error', 'Daftar kelas ini dulu untuk mulai belajar.');
        }

        session()->put("intro_seen.{$course->id}", true);
        $progress = new CourseProgress($course, auth()->user());

        return view('lessons.intro', compact('course', 'progress'));
    }

    public function show(string $slug, Lesson $lesson)
    {
        $course = Course::where('slug', $slug)->firstOrFail();

        if ($lesson->course_id !== $course->id) {
            abort(404);
        }

        $user = auth()->user();

        // Only the trailer is public; lessons open after enrolling ("Mulai gratis" for free chapters), in order.
        if (! CourseProgress::canStart($user, $course)) {
            return redirect()->route('course.show', $course->slug)->with('error', $course->isPartlyFree()
                ? 'Login lalu klik Mulai Gratis untuk membuka materi.'
                : 'Daftar kelas ini dulu untuk membuka materi.');
        }

        $progress = new CourseProgress($course, $user);

        if (! $progress->isUnlocked($lesson)) {
            return redirect()->route('course.learn', $course->slug)
                ->with('error', $progress->sectionOpen($lesson->section) ? 'Selesaikan langkah sebelumnya dulu.' : null);
        }

        // Admins only review content; nothing about their visit is recorded as learner progress.
        $watch = null;
        if (! $progress->reviewer) {
            $watch = LessonProgress::firstOrCreate(
                ['user_id' => $user->id, 'lesson_id' => $lesson->id],
                ['started_at' => now()],
            );
            // Each page view starts a new watching session: time spent away doesn't count as watching.
            $watch->update(['last_ping_at' => now()]);
        }

        return view('lessons.show', compact('course', 'lesson', 'progress', 'watch'));
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
            'can_complete' => $watch->videoWatchedEnough(),
        ]);
    }

    public function complete(string $slug, Lesson $lesson)
    {
        $watch = $this->unlockedProgress($slug, $lesson);

        if (! $watch->completed_at) {
            $ok = $lesson->isVideo() ? $watch->videoWatchedEnough() : $watch->readLongEnough($lesson);

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

        abort_if($lesson->course_id !== $course->id || ! CourseProgress::canStart($user, $course), 403);
        $progress = new CourseProgress($course, $user);
        abort_unless($progress->isUnlocked($lesson) && ! $progress->reviewer, 403);

        return LessonProgress::firstOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            ['started_at' => now(), 'last_ping_at' => now()],
        );
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

        return view('lessons.completed', compact('course', 'progress'));
    }

}
