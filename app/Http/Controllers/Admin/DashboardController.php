<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('instructor')) {
            // Instructors manage every course for now (not only their own), so they see the full count.
            $totalCourses = Course::count();
            $totalUsers = null; // Hidden untuk instructor
            $totalEnrollments = null; // Hidden untuk instructor
            $pendingPayments = null; // Hidden untuk instructor

            $recentEnrollments = collect();

        } elseif ($user->hasRole('content-manager')) {
            // Dashboard khusus untuk content manager
            $totalCourses = null; // Hidden untuk content manager
            $totalUsers = null; // Hidden untuk content manager
            $totalEnrollments = null; // Hidden untuk content manager
            $pendingPayments = null; // Hidden untuk content manager

            // Get recent enrollments - tidak ada untuk content manager
            $recentEnrollments = collect();

        } else {
            // Dashboard untuk admin (data lengkap)
            $totalCourses = Course::count();
            $totalUsers = User::count();
            $totalEnrollments = Enrollment::count();
            $pendingPayments = Enrollment::where('payment_status', 'pending')->count();

            // Get recent enrollments
            $recentEnrollments = Enrollment::with(['user', 'course'])
                ->latest()
                ->take(5)
                ->get();
        }

        // Get article data for content manager and admin
        $totalArticles = null;
        $recentArticles = collect();
        $scheduledArticles = null;
        $publishedToday = null;
        $readyToPublish = null;

        if ($user->hasRole('content-manager') || $user->hasRole('admin') || $user->hasRole('super-admin')) {
            $totalArticles = Article::count();
            $recentArticles = Article::latest()->take(5)->get();

            // Scheduling statistics
            $scheduledArticles = Article::scheduled()->count();
            $publishedToday = Article::whereDate('published_at', today())->count();
            $readyToPublish = Article::readyToPublish()->count();
        }

        return view('admin.dashboard', compact(
            'totalCourses',
            'totalUsers',
            'totalEnrollments',
            'pendingPayments',
            'recentEnrollments',
            'totalArticles',
            'recentArticles',
            'scheduledArticles',
            'publishedToday',
            'readyToPublish'
        ));
    }
}
