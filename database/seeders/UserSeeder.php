<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a default admin user
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'username' => 'admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'), // Default password: password
                'last_login' => null,
            ]
        );

        // Create a default student user
        $studentUser = User::updateOrCreate(
            ['email' => 'student@example.com'],
            [
                'name' => 'Student User',
                'username' => 'student',
                'email' => 'student@example.com',
                'password' => bcrypt('  '), // Default password: password
                'last_login' => null,
            ]
        );

        // Create a super admin user
        $superAdminUser = User::updateOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin User',
                'username' => 'superadmin',
                'email' => 'superadmin@example.com',
                'password' => bcrypt('password'), // Default password: password
                'last_login' => null,
            ]
        );

        // Assign roles to users
        if (! $adminUser->hasRole('admin')) {
            $adminUser->assignRole('admin');
        }

        if (! $studentUser->hasRole('student')) {
            $studentUser->assignRole('student');
        }

        if (! $superAdminUser->hasRole('Super-Admin')) {
            $superAdminUser->assignRole('Super-Admin');
        }

        // Create additional test users
        $instructorUser = User::updateOrCreate(
            ['email' => 'instructor@example.com'],
            [
                'name' => 'Instructor User',
                'username' => 'instructor',
                'email' => 'instructor@example.com',
                'password' => bcrypt('password'),
                'last_login' => null,
            ]
        );

        if (! $instructorUser->hasRole('instructor')) {
            $instructorUser->assignRole('instructor');
        }

        // Create a content manager user
        $contentManagerUser = User::updateOrCreate(
            ['email' => 'content@example.com'],
            [
                'name' => 'Content Manager User',
                'username' => 'content_manager',
                'email' => 'content@example.com',
                'password' => bcrypt('password'),
                'last_login' => null,
            ]
        );

        if (! $contentManagerUser->hasRole('content-manager')) {
            $contentManagerUser->assignRole('content-manager');
        }
    }
}
