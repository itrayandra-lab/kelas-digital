<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Enrollment;
use App\Support\CourseProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        // Only plain strings; "?category[]=x" would otherwise break the queries and hidden inputs.
        $filters = collect($request->only(['q', 'category', 'type', 'level', 'sort']))
            ->map(fn ($v) => is_string($v) ? trim($v) : '')
            ->filter(fn ($v) => $v !== '')
            ->all();
        $categorySlug = $filters['category'] ?? null;
        $search = $filters['q'] ?? '';
        $type = $filters['type'] ?? null;
        $level = $filters['level'] ?? null;
        $sort = $filters['sort'] ?? null;

        $query = Course::with('category')
            ->withCount(['lessons', 'enrollments as students_count' => fn ($q) => $q->where('payment_status', 'completed')])
            ->when($categorySlug, fn ($q) => $q->whereHas('category', fn ($c) => $c->where('slug', $categorySlug)))
            ->when($search !== '', fn ($q) => $q->where(fn ($w) => $w->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('instructor', 'like', "%{$search}%")))
            ->when(in_array($type, ['free', 'paid'], true), fn ($q) => $q->where('course_type', $type))
            ->when(in_array($level, ['Beginner', 'Intermediate', 'Advanced'], true), fn ($q) => $q->where('level', $level));

        match ($sort) {
            'popular' => $query->orderByDesc('students_count')->latest(),
            'cheap' => $query->orderBy('price')->latest(),
            default => $query->latest(),
        };

        $courses = $query->paginate(12)->withQueryString();

        $courseCategories = CourseCategory::whereHas('courses')
            ->withCount('courses')
            ->orderBy('name')
            ->get();

        $activeCategory = $categorySlug
            ? $courseCategories->firstWhere('slug', $categorySlug)
            : null;

        return view('course.index', compact('courses', 'courseCategories', 'activeCategory', 'categorySlug', 'search', 'type', 'level', 'sort', 'filters'));
    }

    public function show($slug)
    {
        $course = Course::with('sections.lessons')->where('slug', $slug)->firstOrFail();

        $user = Auth::user();

        $userHasAccess = false;
        $userEnrollment = null;

        if ($user) {
            $userEnrollment = $user->enrollments()
                ->where('course_id', $course->id)
                ->first();

            $userHasAccess = CourseProgress::canAccessCourse($user, $course);
        }

        $progress = $userHasAccess ? new CourseProgress($course, $user) : null;
        $course->sections->loadCount([
            'questions as pre_count' => fn ($q) => $q->where('kind', 'pre'),
            'questions as post_count' => fn ($q) => $q->where('kind', 'post'),
        ]);
        $sections = $course->sections->filter(fn ($section) => $section->lessons->isNotEmpty())->values();
        $lessons = $sections->flatMap->lessons;
        $totalMinutes = (int) $lessons->sum(fn ($lesson) => (int) $lesson->duration);
        $questionCount = $course->questions()->count();
        $enrolledStudentsCount = $course->enrollments()->where('payment_status', 'completed')->count();

        // Related courses from same category
        $relatedCourses = Course::with('category')
            ->where('course_category_id', $course->course_category_id)
            ->where('id', '!=', $course->id)
            ->withCount('enrollments')
            ->latest()
            ->limit(6)
            ->get();

        return view('course.show', compact('course', 'userHasAccess', 'userEnrollment', 'progress', 'sections', 'lessons', 'totalMinutes', 'questionCount', 'enrolledStudentsCount', 'relatedCourses'));
    }

    public function enroll($slug)
    {
        $course = Course::where('slug', $slug)->firstOrFail();
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $existingEnrollment = $user->enrollments()
            ->where('course_id', $course->id)
            ->first();

        // A pending/rejected enrollment from when the course was still paid would otherwise lock the user out.
        if ($existingEnrollment && $course->isFreeClass() && $existingEnrollment->payment_status !== 'completed') {
            $existingEnrollment->update([
                'status' => 'active',
                'payment_status' => 'completed',
                'payment_method' => 'free',
            ]);

            return redirect()->back()->with('message', 'Pendaftaran berhasil! Anda sudah terdaftar di kelas ini.');
        }

        if ($existingEnrollment) {
            $message = $course->isFreeClass()
                ? 'Anda sudah terdaftar di kelas ini.'
                : 'Anda sudah mendaftar kelas ini. Silakan selesaikan pembayaran.';

            return redirect()->back()->with('message', $message);
        }

        if ($course->isFreeClass()) {
            Enrollment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'status' => 'active',
                'enrolled_at' => now(),
                'payment_status' => 'completed',
                'payment_method' => 'free',
                'payment_proof' => null,
            ]);

            return redirect()->back()->with('message', 'Pendaftaran berhasil! Anda sudah terdaftar di kelas ini.');
        }

        $enrollment = Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'pending',
            'enrolled_at' => now(),
            'payment_status' => 'pending',
            'payment_method' => 'midtrans',
            'payment_proof' => null,
        ]);

        return redirect()->back()->with('message', 'Pendaftaran berhasil! Silakan lakukan pembayaran.');
    }
}
