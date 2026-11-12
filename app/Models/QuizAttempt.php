<?php

namespace App\Models;

use App\Support\EssayGrader;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * One run through a section's pre-test or post-test. Questions are shown one at a time; the server
 * keeps the index and when the current question started, so a refresh never adds time.
 */
class QuizAttempt extends Model
{
    public const SECONDS_PER_QUESTION = 30;

    /** Allowance for network delay between the timer running out and the answer arriving. */
    public const GRACE_SECONDS = 3;

    protected $fillable = [
        'user_id', 'course_id', 'section_id', 'kind', 'status', 'current_index', 'question_started_at',
        'score', 'passed', 'answers', 'essay_marks', 'ai_feedback', 'reviewed_at',
    ];

    protected $casts = [
        'passed' => 'boolean',
        'answers' => 'array',
        'essay_marks' => 'array',
        'ai_feedback' => 'array',
        'question_started_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function section()
    {
        return $this->belongsTo(CourseSection::class, 'section_id');
    }

    public function questions(): Collection
    {
        return CourseQuestion::with('options')
            ->where('section_id', $this->section_id)
            ->where('kind', $this->kind)
            ->orderBy('order')->orderBy('id')
            ->get();
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function secondsLeft(): int
    {
        return max(0, self::SECONDS_PER_QUESTION - (int) $this->question_started_at->diffInSeconds(now(), true));
    }

    public function timeIsUp(): bool
    {
        return $this->question_started_at->diffInSeconds(now(), true) > self::SECONDS_PER_QUESTION + self::GRACE_SECONDS;
    }

    /**
     * Stores the answer for the current question (empty if its time ran out) and moves on.
     */
    public function answerCurrent(Collection $questions, mixed $answer): void
    {
        $question = $questions->get($this->current_index);
        $answers = (array) $this->answers;

        if ($question) {
            $answers[$question->id] = $this->timeIsUp() ? null : $this->clean($question, $answer);
        }

        $this->answers = $answers;
        $this->current_index++;
        $this->question_started_at = now();
    }

    public function isFinished(Collection $questions): bool
    {
        return $this->current_index >= $questions->count();
    }

    /**
     * Essays go to the AI grader; an empty essay is simply wrong. If the AI can't be reached or the
     * question has no answer key, the essay stays unmarked and the attempt waits for an admin.
     */
    public function finish(Collection $questions, int $passScore, EssayGrader $grader): void
    {
        $marks = (array) $this->essay_marks;
        $feedback = (array) $this->ai_feedback;

        foreach ($questions->where('type', 'essay') as $q) {
            $answer = trim((string) ($this->answers[$q->id] ?? ''));

            if ($answer === '') {
                $marks[$q->id] = false;
                $feedback[$q->id] = 'Tidak dijawab.';

                continue;
            }

            // Without an answer key the AI has nothing to compare against; an admin marks it instead.
            if (trim((string) $q->answer_key) === '') {
                continue;
            }

            if ($result = $grader->grade($q->question, (string) $q->answer_key, $answer)) {
                $marks[$q->id] = $result['correct'];
                $feedback[$q->id] = $result['reason'].' (dinilai AI via '.$result['provider'].')';
            }
        }

        $this->essay_marks = $marks;
        $this->ai_feedback = $feedback;
        $this->grade($questions, $passScore);
    }

    /**
     * Scores the attempt. Choice questions are checked automatically; essays count only once marked,
     * so an attempt with an unmarked essay stays pending without a score.
     */
    public function grade(Collection $questions, int $passScore): void
    {
        $marks = (array) $this->essay_marks;
        $hasUnmarkedEssay = $questions->contains(fn ($q) => $q->type === 'essay' && ! array_key_exists($q->id, $marks));

        if ($hasUnmarkedEssay) {
            $this->fill(['status' => 'pending', 'score' => null, 'passed' => false]);

            return;
        }

        $correct = $questions->filter(fn ($q) => $q->type === 'essay'
            ? (bool) $marks[$q->id]
            : $q->isAnsweredCorrectly($this->answers[$q->id] ?? null))->count();
        $score = $questions->isEmpty() ? 100 : (int) round($correct / $questions->count() * 100);

        $this->fill([
            'status' => 'graded',
            'score' => $score,
            // The pre-test only needs to be taken; the post-test must reach the course's pass score.
            'passed' => $this->kind === 'pre' || $score >= $passScore,
        ]);
    }

    private function clean(CourseQuestion $question, mixed $answer): mixed
    {
        if ($question->type === 'essay') {
            $text = trim(mb_substr((string) $answer, 0, 5000));

            return $text === '' ? null : $text;
        }

        // Only option ids that belong to this question are kept.
        $ids = collect((array) $answer)->map(fn ($id) => (int) $id)->intersect($question->options->pluck('id'))->values();

        if ($ids->isEmpty()) {
            return null;
        }

        return $question->type === 'multiple' ? $ids->all() : $ids->first();
    }
}
