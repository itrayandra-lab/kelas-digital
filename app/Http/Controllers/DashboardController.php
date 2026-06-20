<?php

namespace App\Http\Controllers;

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

        $courses = $user->enrolledCourses;

        return view('dashboard.index', compact('user', 'courses'));
    }
}
