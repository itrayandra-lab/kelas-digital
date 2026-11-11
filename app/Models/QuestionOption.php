<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionOption extends Model
{
    protected $fillable = ['course_question_id', 'text', 'is_correct'];

    protected $casts = ['is_correct' => 'boolean'];
}
