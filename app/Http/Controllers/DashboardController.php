<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Support\CourseProgress;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Only students can access /dashboard
        if (! $user->hasRole('student')) {
            return redirect()->route('admin.dashboard');
        }

        // Most recently enrolled first; each course paired with the learner's progress in it.
        $items = $user->enrolledCourses()
            ->with('category')
            ->orderByPivot('enrolled_at', 'desc')
            ->get()
            ->map(fn (Course $course) => ['course' => $course, 'progress' => new CourseProgress($course, $user)]);

        [$finished, $ongoing] = $items->partition(fn ($item) => $item['progress']->isCourseFinished());

        $recommended = Course::with('category')
            ->whereNotIn('id', $items->pluck('course.id'))
            ->latest()
            ->limit(3)
            ->get();

        return view('dashboard.index', compact('user', 'ongoing', 'finished', 'recommended'));
    }
}
