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
        $categorySlug = $request->query('category');

        $query = Course::with(['category'])
            ->withCount('enrollments')
            ->orderBy('created_at', 'desc');

        if ($categorySlug) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
        }

        $courses = $query->paginate(12)->withQueryString();

        $courseCategories = CourseCategory::whereHas('courses')
            ->withCount('courses')
            ->orderBy('name')
            ->get();

        $activeCategory = $categorySlug
            ? $courseCategories->firstWhere('slug', $categorySlug)
            : null;

        return view('course.index', compact('courses', 'courseCategories', 'activeCategory', 'categorySlug'));
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
