<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Enrollment;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $categorySlug = $request->query('category');

        $query = \App\Models\Course::with(['category'])
            ->withCount('enrollments')
            ->orderBy('created_at', 'desc');

        if ($categorySlug) {
            $query->whereHas('category', fn($q) => $q->where('slug', $categorySlug));
        }

        $courses = $query->paginate(12)->withQueryString();

        $courseCategories = \App\Models\CourseCategory::whereHas('courses')
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
        $course = \App\Models\Course::with(['lessons' => function($query) {
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
        
        return view('course.show', compact('course', 'userHasAccess', 'userEnrollment', 'lessonsByModule', 'totalVideos', 'enrolledStudentsCount', 'initialVideoId'));
    }
    
    public function enroll($slug)
    {
        $course = \App\Models\Course::where('slug', $slug)->firstOrFail();
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }
        
        // Check if user already has enrollment (regardless of status)
        $existingEnrollment = $user->enrollments()
            ->where('course_id', $course->id)
            ->first();
        
        if ($existingEnrollment) {
            return redirect()->back()->with('message', 'Anda sudah mendaftar kelas ini. Silakan tunggu verifikasi pembayaran.');
        }
        
        // Create enrollment record
        $enrollment = \App\Models\Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'pending',
            'enrolled_at' => now(),
            'payment_status' => 'pending',
            'payment_method' => 'manual_transfer',
            'payment_proof' => null
        ]);
        
        return redirect()->back()->with('message', 'Pendaftaran kelas berhasil! Silakan lakukan pembayaran sesuai instruksi yang akan dikirimkan.');
    }
}
