<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class QuizAttempt extends Model
{
    protected $fillable = ['user_id', 'course_id', 'kind', 'status', 'score', 'passed', 'answers', 'essay_marks', 'reviewed_at'];

    protected $casts = [
        'passed' => 'boolean',
        'answers' => 'array',
        'essay_marks' => 'array',
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

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Scores the attempt. Choice questions are checked automatically; essays count only once an admin
     * has marked them, so an attempt with unmarked essays stays pending without a score.
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
}
