<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\QuizAttempt;
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

        // Paid courses the learner started buying but has not paid for yet.
        $unpaid = $user->enrollments()
            ->where('payment_status', 'pending')
            ->with('course.category')
            ->latest()
            ->get()
            ->pluck('course')
            ->filter();

        $recentTests = QuizAttempt::with(['course:id,title,slug', 'section:id,title'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['graded', 'pending'])
            ->latest()
            ->limit(5)
            ->get();

        $stats = [
            'lessons' => $items->sum(fn ($item) => count($item['progress']->completedIds)),
            'passed' => QuizAttempt::where('user_id', $user->id)->where('kind', 'post')->where('passed', true)->distinct()->count('section_id'),
        ];

        // Courses from the categories the learner already studies come first.
        $categoryIds = $items->pluck('course.course_category_id')->unique();
        $recommended = Course::with('category')
            ->withCount(['lessons', 'enrollments as students_count' => fn ($q) => $q->where('payment_status', 'completed')])
            ->whereNotIn('id', $items->pluck('course.id')->merge($unpaid->pluck('id')))
            ->latest()
            ->limit(12)
            ->get()
            ->sortBy(fn (Course $course) => $categoryIds->contains($course->course_category_id) ? 0 : 1)
            ->take(4);

        return view('dashboard.index', compact('user', 'ongoing', 'finished', 'unpaid', 'recentTests', 'stats', 'recommended'));
    }
}
