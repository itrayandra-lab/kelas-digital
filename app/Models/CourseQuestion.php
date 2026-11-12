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
     * Choice questions need exactly the set of correct options. Essays are marked by the AI grader or an admin instead.
     */
    public function isAnsweredCorrectly(mixed $answer): bool
    {
        if ($this->type === 'essay') {
            return false;
        }

        $given = collect((array) $answer)->map(fn ($id) => (int) $id)->sort()->values()->all();
        $correct = $this->options->where('is_correct', true)->pluck('id')->sort()->values()->all();

        return $given !== [] && $given === $correct;
    }
}
