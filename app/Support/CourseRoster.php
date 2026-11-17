<?php

namespace App\Support;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\QuizAttempt;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Everyone enrolled in a course, with the same progress the learner sees (CourseProgress), for the
 * admin's learner list, the module summary and the CSV export.
 */
class CourseRoster
{
    public const STATUSES = [
        'paid' => 'Lunas',
        'free' => 'Kelas gratis',
        'trial' => 'Modul gratis',
        'waiting' => 'Menunggu bayar',
        'failed' => 'Pembayaran gagal',
    ];

    /** One row per learner, newest enrollment first. Admins and instructors who enrolled to check the course are left out. */
    public static function rows(Course $course): Collection
    {
        // shortcut: progress is computed per learner (a few queries each); fine up to a few hundred learners, store it on enrollments beyond that.
        $enrollments = $course->enrollments()->with('user.roles')->latest()->get()
            ->filter(fn (Enrollment $e) => $e->user && ! $e->user->hasAnyRole(CourseProgress::REVIEWER_ROLES));

        $userIds = $enrollments->pluck('user_id');
        $lastLesson = LessonProgress::whereIn('user_id', $userIds)
            ->whereIn('lesson_id', Lesson::where('course_id', $course->id)->select('id'))
            ->groupBy('user_id')->selectRaw('user_id, max(updated_at) as at')->pluck('at', 'user_id');
        $lastTest = QuizAttempt::where('course_id', $course->id)->whereIn('user_id', $userIds)
            ->groupBy('user_id')->selectRaw('user_id, max(updated_at) as at')->pluck('at', 'user_id');

        return $enrollments->map(function (Enrollment $e) use ($course, $lastLesson, $lastTest) {
            $progress = new CourseProgress($course, $e->user);
            $postScores = $progress->sections->map(fn ($s) => $progress->bestScore($s, 'post'))->reject(fn ($v) => $v === null);

            return [
                'enrollment' => $e,
                'user' => $e->user,
                'progress' => $progress,
                'status' => self::status($e, $course),
                'percent' => $progress->percent(),
                'finished' => $progress->isCourseFinished(),
                'next' => $progress->nextStepLabel(),
                'post_avg' => $postScores->isNotEmpty() ? (int) round($postScores->avg()) : null,
                'last_active' => collect([$lastLesson[$e->user_id] ?? null, $lastTest[$e->user_id] ?? null, $e->created_at])
                    ->filter()->map(fn ($t) => Carbon::parse($t))->max(),
            ];
        })->values();
    }

    /** Per module: how many learners started it and passed its post-test, and the average best pre/post scores. */
    public static function modules(Collection $rows): Collection
    {
        if ($rows->isEmpty()) {
            return collect();
        }

        $progresses = $rows->pluck('progress');
        $average = fn (Collection $scores) => $scores->isNotEmpty() ? (int) round($scores->avg()) : null;

        return $rows->first()['progress']->sections->map(fn ($section) => [
            'section' => $section,
            'started' => $progresses->filter(fn (CourseProgress $p) => $p->preTestDone($section)
                || $section->lessons->contains(fn ($lesson) => $p->isCompleted($lesson)))->count(),
            'passed' => $progresses->filter(fn (CourseProgress $p) => $p->postTestPassed($section))->count(),
            'pre_avg' => $average($progresses->map(fn (CourseProgress $p) => $p->firstScore($section, 'pre'))->reject(fn ($v) => $v === null)),
            'post_avg' => $average($progresses->map(fn (CourseProgress $p) => $p->bestScore($section, 'post'))->reject(fn ($v) => $v === null)),
        ]);
    }

    private static function status(Enrollment $e, Course $course): string
    {
        return match (true) {
            $e->payment_status === 'completed' => $course->isFreeClass() ? 'free' : 'paid',
            $e->payment_status === 'pending' && $e->status === 'pending' => 'waiting',
            $e->payment_status === 'pending' && $e->payment_method === 'trial' => 'trial',
            default => 'failed',
        };
    }
}
