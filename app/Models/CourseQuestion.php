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

    protected $fillable = ['course_id', 'type', 'question', 'order'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function options()
    {
        return $this->hasMany(QuestionOption::class)->orderBy('id');
    }

    /**
     * Choice questions need exactly the set of correct options. Essays are marked by an admin instead.
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
