<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Create the initial roles and permissions for Kelas Digital.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Course Management
            'view courses',
            'create courses',
            'edit courses',
            'delete courses',
            'publish courses',
            'unpublish courses',
            
            // Lesson Management
            'view lessons',
            'create lessons',
            'edit lessons',
            'delete lessons',
            
            // Article Management
            'view articles',
            'create articles',
            'edit articles',
            'delete articles',
            'publish articles',
            'unpublish articles',
            
            // User Management
            'view users',
            'create users',
            'edit users',
            'delete users',
            'assign roles',
            
            // Enrollment Management
            'view enrollments',
            'manage enrollments',
            'approve enrollments',
            'reject enrollments',
            
            // Article Category Management
            'view article categories',
            'create article categories',
            'edit article categories',
            'delete article categories',
            
            // Course Category Management
            'view course categories',
            'create course categories',
            'edit course categories',
            'delete course categories',
            
            // Tag Management
            'view tags',
            'create tags',
            'edit tags',
            'delete tags',
            
            // Admin Panel Access
            'access admin panel',
            'view dashboard',
            'manage site settings',
            'view reports',
            
            // System Management (Super-Admin only)
            'manage roles',
            'manage permissions',
            
            // Student specific permissions
            'enroll courses',
            'view enrolled courses',
            'access course content',
            'complete lessons',
            
            // Site Settings Management
            'manage site settings',
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // Student role
        $studentRole = Role::updateOrCreate(['name' => 'student']);
        $studentRole->syncPermissions([
            'view courses',
            'view lessons',
            'view articles',
            'enroll courses',
            'view enrolled courses',
            'access course content',
            'complete lessons',
            
            // Site Settings Management
            'manage site settings',
        ]);

        // Content Manager role - Limited to article management only
        $contentManagerRole = Role::updateOrCreate(['name' => 'content-manager']);
        $contentManagerRole->syncPermissions([
            // Article Management Only
            'view articles',
            'create articles',
            'edit articles',
            'delete articles',
            'publish articles',
            'unpublish articles',
            // Article Category Management Only
            'view article categories',
            'create article categories',
            'edit article categories',
            'delete article categories',
            'view tags',
            'create tags',
            'edit tags',
            'delete tags',
            // Admin Panel Access (for dashboard)
            'access admin panel',
            'view dashboard',
            'manage site settings',
        ]);

        // Instructor role - Limited to course management only
        $instructorRole = Role::updateOrCreate(['name' => 'instructor']);
        $instructorRole->syncPermissions([
            // Course Management Only
            'view courses',
            'create courses',
            'edit courses',
            'view lessons',
            'create lessons',
            'edit lessons',
            'delete lessons',
            // Course Category Management Only
            'view course categories',
            'create course categories',
            'edit course categories',
            'delete course categories',
            // Admin Panel Access (for dashboard)
            'access admin panel',
            'view dashboard',
            'manage site settings',
        ]);

        // Admin role
        $adminRole = Role::updateOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions([
            'view courses',
            'create courses',
            'edit courses',
            'delete courses',
            'publish courses',
            'unpublish courses',
            'view lessons',
            'create lessons',
            'edit lessons',
            'delete lessons',
            'view articles',
            'create articles',
            'edit articles',
            'delete articles',
            'publish articles',
            'unpublish articles',
            'view users',
            'create users',
            'edit users',
            'delete users',
            'assign roles',
            'view enrollments',
            'manage enrollments',
            'approve enrollments',
            'reject enrollments',
            // Article Category Management
            'view article categories',
            'create article categories',
            'edit article categories',
            'delete article categories',
            // Course Category Management
            'view course categories',
            'create course categories',
            'edit course categories',
            'delete course categories',
            'view tags',
            'create tags',
            'edit tags',
            'delete tags',
            'access admin panel',
            'view dashboard',
            'manage site settings',
            'view reports',
        ]);

        // Super Admin role - gets all permissions via Gate::before rule + specific system permissions
        $superAdminRole = Role::updateOrCreate(['name' => 'Super-Admin']);
        $superAdminRole->syncPermissions([
            // System Management permissions
            'manage roles',
            'manage permissions',
            // All other permissions are granted automatically via Gate::before in AuthServiceProvider
        ]);

        $this->command->info('Roles and permissions created successfully!');
    }
}

