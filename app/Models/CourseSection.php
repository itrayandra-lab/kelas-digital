<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseSection extends Model
{
    protected $fillable = ['course_id', 'title', 'order', 'is_free'];

    protected $casts = ['is_free' => 'boolean'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class, 'section_id')->orderBy('order')->orderBy('id');
    }

    public function questions()
    {
        return $this->hasMany(CourseQuestion::class, 'section_id')->orderBy('order')->orderBy('id');
    }

    public function testQuestions(string $kind)
    {
        return $this->questions()->where('kind', $kind);
    }
}
