<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public static function middleware(): array
    {
        return [
            'auth',
            'can:view courses',
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = Course::with('category')->latest()->paginate(10);

        return view('admin.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create courses');
        $categories = CourseCategory::orderBy('name')->get();

        return view('admin.courses.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create courses');
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'instructor' => 'required|string|max:255',
            'description' => 'required',
            'course_type' => 'required|in:paid,free',
            'price' => 'nullable|integer|min:0',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'trailer_video_id' => 'nullable|string',
            'course_category_id' => 'required|exists:course_categories,id',
            'level' => 'required|in:Beginner,Intermediate,Advanced',
            'benefits' => 'nullable|string',
            'topics_preview' => 'nullable|string',
            'schedule_start' => 'nullable|date',
            'schedule_end' => 'nullable|date|after_or_equal:schedule_start',
            'meeting_platform' => 'nullable|string|max:100',
        ]);

        $thumbnail = 'default-course.jpg';
        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail')->store('courses', 'public');
        }

        Course::create([
            'title' => $data['title'],
            'instructor' => $data['instructor'],
            'description' => $data['description'],
            'course_type' => $data['course_type'],
            'price' => $data['course_type'] === 'free' ? 0 : ($data['price'] ?? 0),
            'thumbnail' => $thumbnail,
            'trailer_video_id' => $data['trailer_video_id'] ?? '',
            'course_category_id' => $data['course_category_id'],
            'level' => $data['level'],
            'benefits' => $data['benefits'] ?? null,
            'topics_preview' => $data['topics_preview'] ?? null,
            'schedule_start' => $data['schedule_start'] ?? null,
            'schedule_end' => $data['schedule_end'] ?? null,
            'meeting_platform' => $data['meeting_platform'] ?? null,
        ]);

        return redirect()->route('admin.courses.index')->with('success', 'Course created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $course = Course::with('category')->findOrFail($id);

        return view('admin.courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $this->authorize('edit courses');
        $course = Course::with('category')->findOrFail($id);
        $categories = CourseCategory::orderBy('name')->get();

        return view('admin.courses.edit', compact('course', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->authorize('edit courses');
        $course = Course::findOrFail($id);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'instructor' => 'required|string|max:255',
            'description' => 'required',
            'course_type' => 'required|in:paid,free',
            'price' => 'nullable|integer|min:0',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'trailer_video_id' => 'nullable|string',
            'course_category_id' => 'required|exists:course_categories,id',
            'level' => 'required|in:Beginner,Intermediate,Advanced',
            'benefits' => 'nullable|string',
            'topics_preview' => 'nullable|string',
            'schedule_start' => 'nullable|date',
            'schedule_end' => 'nullable|date|after_or_equal:schedule_start',
            'meeting_platform' => 'nullable|string|max:100',
        ]);

        $payload = [
            'title' => $data['title'],
            'instructor' => $data['instructor'],
            'description' => $data['description'],
            'course_type' => $data['course_type'],
            'price' => $data['course_type'] === 'free' ? 0 : ($data['price'] ?? $course->price),
            'trailer_video_id' => $data['trailer_video_id'] ?? $course->trailer_video_id,
            'course_category_id' => $data['course_category_id'],
            'level' => $data['level'],
            'benefits' => $data['benefits'] ?? null,
            'topics_preview' => $data['topics_preview'] ?? null,
            'schedule_start' => $data['schedule_start'] ?? null,
            'schedule_end' => $data['schedule_end'] ?? null,
            'meeting_platform' => $data['meeting_platform'] ?? null,
        ];

        if ($request->hasFile('thumbnail')) {
            $payload['thumbnail'] = $request->file('thumbnail')->store('courses', 'public');
        }

        $course->update($payload);

        return redirect()->route('admin.courses.index')->with('success', 'Course updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorize('delete courses');
        $course = Course::findOrFail($id);
        $course->delete();

        return redirect()->route('admin.courses.index')->with('success', 'Course deleted successfully.');
    }
}
