<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;

class LessonController extends Controller
{
    public function show(string $slug, Lesson $lesson)
    {
        $course = Course::where('slug', $slug)->firstOrFail();

        if ($lesson->course_id !== $course->id) {
            abort(404);
        }

        $lessons = $course->lessons()->orderBy('module')->orderBy('order')->get();

        return view('lessons.show', compact('course', 'lesson', 'lessons'));
    }
}
