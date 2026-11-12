<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseQuestion extends Model
{
    public const TYPES = [
        'single' => 'Pilihan ganda (1 jawaban)',
        'multiple' => 'Pilihan ganda (lebih dari 1 jawaban)',
        'true_false' => 'Benar / Salah',
        'essay' => 'Essay',
    ];

    /** Pre-tests only measure the starting point, so they have no essays. */
    public const PRE_TYPES = ['single', 'multiple', 'true_false'];

    protected $fillable = ['course_id', 'section_id', 'kind', 'type', 'question', 'answer_key', 'order'];

    public function section()
    {
        return $this->belongsTo(CourseSection::class, 'section_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function options()
    {
        return $this->hasMany(QuestionOption::class)->orderBy('id');
    }

    /**
     * Credit for a choice question, from 0 to 1. Single and true/false are all or nothing. "Pick all that
     * apply" gives partial credit: each correct pick adds a share, each wrong pick takes one away
     * (so ticking every option doesn't pay off), never below zero. Essays are scored by the AI or an admin.
     */
    public function scoreFor(mixed $answer): float
    {
        if ($this->type === 'essay') {
            return 0.0;
        }

        $given = collect((array) $answer)->map(fn ($id) => (int) $id)->unique();
        $correct = $this->options->where('is_correct', true)->pluck('id');

        if ($given->isEmpty() || $correct->isEmpty()) {
            return 0.0;
        }

        if ($this->type !== 'multiple') {
            return $given->values()->all() === $correct->values()->all() ? 1.0 : 0.0;
        }

        $hits = $given->intersect($correct)->count();
        $misses = $given->diff($correct)->count();

        return max(0, $hits - $misses) / $correct->count();
    }
}
