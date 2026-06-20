<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample enrollments
        $user = User::where('email', 'student@example.com')->first();
        $course = Course::where('title', 'Introduction to Web Development')->first();

        if ($user && $course) {
            Enrollment::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                ],
                [
                    'status' => 'active',
                    'payment_status' => 'completed',
                    'payment_method' => 'manual_transfer',
                    'payment_proof' => null,
                    'enrolled_at' => now(),
                ]
            );
        }

        // Create another enrollment with pending payment
        $admin = User::where('email', 'admin@example.com')->first();
        $course2 = Course::where('title', 'Advanced Laravel Techniques')->first();

        if ($admin && $course2) {
            Enrollment::updateOrCreate(
                [
                    'user_id' => $admin->id,
                    'course_id' => $course2->id,
                ],
                [
                    'status' => 'pending',
                    'payment_status' => 'pending',
                    'payment_method' => 'manual_transfer',
                    'payment_proof' => null,
                    'enrolled_at' => now(),
                ]
            );
        }
    }
}
