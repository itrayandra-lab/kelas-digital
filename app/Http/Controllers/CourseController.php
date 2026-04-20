<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Enrollment;
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
        $course = Course::with(['lessons' => function ($query) {
            $query->orderBy('module')->orderBy('order');
        }])->where('slug', $slug)->firstOrFail();

        $user = Auth::user();

        $userHasAccess = false;
        $userEnrollment = null;

        if ($user) {
            $userEnrollment = $user->enrollments()
                ->where('course_id', $course->id)
                ->first();

            $userHasAccess = $userEnrollment && $userEnrollment->payment_status === 'completed';
        }

        // Group lessons by module
        $lessonsByModule = $course->lessons->groupBy('module');

        // Calculate total videos
        $totalVideos = $course->lessons->count();

        // Calculate enrolled students count
        $enrolledStudentsCount = $course->enrollments()->where('payment_status', 'completed')->count();

        // Always start with trailer video when opening course detail
        $initialVideoId = $course->trailer_video_id;

        // Related courses from same category
        $relatedCourses = Course::with('category')
            ->where('course_category_id', $course->course_category_id)
            ->where('id', '!=', $course->id)
            ->withCount('enrollments')
            ->latest()
            ->limit(6)
            ->get();

        return view('course.show', compact('course', 'userHasAccess', 'userEnrollment', 'lessonsByModule', 'totalVideos', 'enrolledStudentsCount', 'initialVideoId', 'relatedCourses'));
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

        if ($existingEnrollment) {
            $message = $course->isFreeClass()
                ? 'Anda sudah terdaftar di kelas ini.'
                : 'Anda sudah mendaftar kelas ini. Silakan tunggu verifikasi pembayaran.';

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

        Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'pending',
            'enrolled_at' => now(),
            'payment_status' => 'pending',
            'payment_method' => 'manual_transfer',
            'payment_proof' => null,
        ]);

        return redirect()->back()->with('message', 'Pendaftaran kelas berhasil! Silakan lakukan pembayaran sesuai instruksi yang akan dikirimkan.');
    }
}