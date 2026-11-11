<?php

namespace App\Support;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Where a learner stands in a course. Study order is: pre-test (if the course has questions),
 * every lesson in order, then the post-test. Each step unlocks only after the previous one.
 */
class CourseProgress
{
    public readonly Collection $lessons;

    public readonly array $completedIds;

    public readonly bool $hasTests;

    public readonly bool $preTestDone;

    public readonly ?QuizAttempt $bestPostTest;

    public readonly bool $postTestPending;

    public function __construct(public readonly Course $course, public readonly User $user)
    {
        $this->lessons = $course->orderedLessons();
        $this->completedIds = $user->completedLessons()
            ->whereIn('lessons.id', $this->lessons->pluck('id'))
            ->pluck('lessons.id')
            ->all();
        $this->hasTests = $course->questions()->exists();

        $attempts = QuizAttempt::where('user_id', $user->id)->where('course_id', $course->id);
        $this->preTestDone = (clone $attempts)->where('kind', 'pre')->exists();
        $this->bestPostTest = (clone $attempts)->where('kind', 'post')->where('status', 'graded')->orderByDesc('score')->first();
        $this->postTestPending = (clone $attempts)->where('kind', 'post')->where('status', 'pending')->exists();
    }

    public static function canAccessCourse(?User $user, Course $course): bool
    {
        return $user && ($user->hasAnyRole(['admin', 'Super-Admin'])
            || $user->enrollments()
                ->where('course_id', $course->id)
                ->where('payment_status', 'completed')
                ->exists());
    }

    public function isCompleted(Lesson $lesson): bool
    {
        return in_array($lesson->id, $this->completedIds, true);
    }

    public function isUnlocked(Lesson $lesson): bool
    {
        if ($this->hasTests && ! $this->preTestDone) {
            return false;
        }

        foreach ($this->lessons as $item) {
            if ($item->id === $lesson->id) {
                return true;
            }

            if (! $this->isCompleted($item)) {
                return false;
            }
        }

        return false;
    }

    public function allLessonsDone(): bool
    {
        return $this->lessons->every(fn (Lesson $lesson) => $this->isCompleted($lesson));
    }

    public function postTestUnlocked(): bool
    {
        return $this->hasTests && $this->preTestDone && $this->allLessonsDone();
    }

    public function postTestPassed(): bool
    {
        return (bool) $this->bestPostTest?->passed;
    }

    public function isCourseFinished(): bool
    {
        if ($this->lessons->isEmpty() && ! $this->hasTests) {
            return false;
        }

        return $this->allLessonsDone() && (! $this->hasTests || $this->postTestPassed());
    }

    public function nextLesson(): ?Lesson
    {
        return $this->lessons->first(fn (Lesson $lesson) => ! $this->isCompleted($lesson));
    }

    /**
     * What "Lanjut Belajar" will open next, for showing on cards.
     */
    public function nextStepLabel(): string
    {
        if ($this->hasTests && ! $this->preTestDone) {
            return 'Pre-test';
        }

        if ($lesson = $this->nextLesson()) {
            return $lesson->title;
        }

        if ($this->hasTests && ! $this->postTestPassed()) {
            return $this->postTestPending ? 'Post-test (nilai sedang diverifikasi)' : 'Post-test';
        }

        return 'Kelas selesai';
    }

    /**
     * Pre- and post-test each count as one step next to the lessons.
     */
    public function percent(): int
    {
        $total = $this->lessons->count() + ($this->hasTests ? 2 : 0);

        if ($total === 0) {
            return 0;
        }

        $done = count($this->completedIds)
            + ($this->hasTests && $this->preTestDone ? 1 : 0)
            + ($this->hasTests && $this->postTestPassed() ? 1 : 0);

        return (int) floor($done / $total * 100);
    }
}
