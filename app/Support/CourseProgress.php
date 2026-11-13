<?php

namespace App\Support;

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Where a learner stands in a course. Study order is, for every section in turn: its pre-test (if it
 * has one), its lessons in order, then its post-test (if it has one). Each step unlocks only after
 * every step before it is done, so the next section opens once the previous post-test is passed.
 */
class CourseProgress
{
    public readonly Collection $sections;

    public readonly Collection $lessons;

    /** Every step in study order: ['type' => pre|lesson|post, 'section' => CourseSection, 'lesson' => ?Lesson]. */
    public readonly Collection $steps;

    public readonly array $completedIds;

    /** Paid (or free course, or admin). Without it only the free chapters of a partly free course are open. */
    public readonly bool $fullAccess;

    private Collection $attempts;

    public function __construct(public readonly Course $course, public readonly User $user)
    {
        $this->fullAccess = self::canAccessCourse($user, $course);

        $this->sections = $course->sections()
            ->with('lessons')
            ->withCount([
                'questions as pre_count' => fn ($q) => $q->where('kind', 'pre'),
                'questions as post_count' => fn ($q) => $q->where('kind', 'post'),
            ])
            ->get();
        $this->lessons = $this->sections->flatMap->lessons->values();

        $this->completedIds = $user->completedLessons()
            ->whereIn('lessons.id', $this->lessons->pluck('id'))
            ->pluck('lessons.id')
            ->all();

        $this->attempts = QuizAttempt::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->get(['id', 'section_id', 'kind', 'status', 'score', 'passed', 'created_at']);

        $this->steps = $this->sections->flatMap(fn (CourseSection $s) => collect()
            ->when($s->pre_count > 0, fn ($c) => $c->push(['type' => 'pre', 'section' => $s, 'lesson' => null]))
            ->merge($s->lessons->map(fn ($l) => ['type' => 'lesson', 'section' => $s, 'lesson' => $l]))
            ->when($s->post_count > 0, fn ($c) => $c->push(['type' => 'post', 'section' => $s, 'lesson' => null]))
        )->values();
    }

    public static function canAccessCourse(?User $user, Course $course): bool
    {
        return $user && ($user->hasAnyRole(['admin', 'Super-Admin'])
            || $user->enrollments()
                ->where('course_id', $course->id)
                ->where('payment_status', 'completed')
                ->exists());
    }

    /**
     * Whether the learner can study at all: full access, or "Mulai gratis" on a partly free course
     * (any enrollment row counts, so an abandoned or expired payment never takes the free chapters away).
     */
    public static function canStart(?User $user, Course $course): bool
    {
        return self::canAccessCourse($user, $course)
            || ($user && $course->isPartlyFree() && $user->enrollments()->where('course_id', $course->id)->exists());
    }

    public function sectionOpen(CourseSection $section): bool
    {
        return $this->fullAccess || $section->is_free;
    }

    /** The next step sits in a chapter the learner hasn't paid for: time to offer the full course. */
    public function needsPayment(): bool
    {
        $step = $this->nextStep();

        return $step !== null && ! $this->sectionOpen($step['section']);
    }

    // ---------- Tests ----------

    public function hasTests(): bool
    {
        return $this->sections->contains(fn ($s) => $s->pre_count > 0 || $s->post_count > 0);
    }

    public function sectionHasTest(CourseSection $section, string $kind): bool
    {
        return (bool) $this->sectionRow($section)?->{$kind.'_count'};
    }

    public function preTestDone(CourseSection $section): bool
    {
        return $this->attemptsFor($section, 'pre')->contains(fn ($a) => ! $a->isInProgress());
    }

    public function postTestPassed(CourseSection $section): bool
    {
        return $this->attemptsFor($section, 'post')->contains(fn ($a) => $a->status === 'graded' && $a->passed);
    }

    public function postTestPending(CourseSection $section): bool
    {
        return $this->attemptsFor($section, 'post')->contains(fn ($a) => $a->isPending());
    }

    public function bestScore(CourseSection $section, string $kind): ?int
    {
        return $this->attemptsFor($section, $kind)->where('status', 'graded')->max('score');
    }

    public function testUnlocked(CourseSection $section, string $kind): bool
    {
        return $this->stepUnlocked(fn ($step) => $step['type'] === $kind && $step['section']->id === $section->id);
    }

    // ---------- Lessons ----------

    public function isCompleted(Lesson $lesson): bool
    {
        return in_array($lesson->id, $this->completedIds, true);
    }

    public function isUnlocked(Lesson $lesson): bool
    {
        return $this->stepUnlocked(fn ($step) => $step['type'] === 'lesson' && $step['lesson']->id === $lesson->id);
    }

    // ---------- Overall ----------

    public function isStepDone(array $step): bool
    {
        return match ($step['type']) {
            'pre' => $this->preTestDone($step['section']),
            'post' => $this->postTestPassed($step['section']),
            default => $this->isCompleted($step['lesson']),
        };
    }

    public function nextStep(): ?array
    {
        return $this->steps->first(fn ($step) => ! $this->isStepDone($step));
    }

    public function isCourseFinished(): bool
    {
        return $this->steps->isNotEmpty() && $this->nextStep() === null;
    }

    public function percent(): int
    {
        if ($this->steps->isEmpty()) {
            return 0;
        }

        return (int) floor($this->steps->filter(fn ($s) => $this->isStepDone($s))->count() / $this->steps->count() * 100);
    }

    /**
     * What "Lanjut Belajar" will open next, for showing on cards.
     */
    public function nextStepLabel(): string
    {
        $step = $this->nextStep();

        return match ($step['type'] ?? null) {
            'pre' => 'Pre-test '.$step['section']->title,
            'lesson' => $step['lesson']->title,
            'post' => 'Post-test '.$step['section']->title.($this->postTestPending($step['section']) ? ' (nilai sedang diverifikasi)' : ''),
            default => 'Kelas selesai',
        };
    }

    private function stepUnlocked(callable $match): bool
    {
        foreach ($this->steps as $step) {
            if (! $this->sectionOpen($step['section'])) {
                return false;
            }

            if ($match($step)) {
                return true;
            }

            if (! $this->isStepDone($step)) {
                return false;
            }
        }

        return false;
    }

    private function attemptsFor(CourseSection $section, string $kind): Collection
    {
        return $this->attempts->where('section_id', $section->id)->where('kind', $kind);
    }

    private function sectionRow(CourseSection $section): ?CourseSection
    {
        return $this->sections->firstWhere('id', $section->id);
    }
}
