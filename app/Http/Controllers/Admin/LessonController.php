<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function index(Request $request)
    {
        $query = Lesson::with('course');

        if ($request->has('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        $lessons = $query->latest()->paginate(10);
        $courses = Course::all();

        return view('admin.lessons.index', compact('lessons', 'courses'));
    }

    public function create()
    {
        $courses = Course::all();

        return view('admin.lessons.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'youtube_video_id' => 'required|string|max:255',
            'module' => 'required|string|max:255',
            'order' => 'required|integer',
            'duration' => 'nullable|string|max:255',
            'is_preview' => 'boolean',
        ]);

        Lesson::create($request->all());

        return redirect()->route('admin.lessons.index')->with('message', 'Lesson created successfully.');
    }

    public function show(Lesson $lesson)
    {
        return view('admin.lessons.show', compact('lesson'));
    }

    public function edit(Lesson $lesson)
    {
        $courses = Course::all();

        return view('admin.lessons.edit', compact('lesson', 'courses'));
    }

    public function update(Request $request, Lesson $lesson)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'youtube_video_id' => 'required|string|max:255',
            'module' => 'required|string|max:255',
            'order' => 'required|integer',
            'duration' => 'nullable|string|max:255',
            'is_preview' => 'boolean',
        ]);

        $lesson->update($request->all());

        return redirect()->route('admin.lessons.index')->with('message', 'Lesson updated successfully.');
    }

    public function destroy(Lesson $lesson)
    {
        $lesson->delete();

        return redirect()->route('admin.lessons.index')->with('message', 'Lesson deleted successfully.');
    }
}
