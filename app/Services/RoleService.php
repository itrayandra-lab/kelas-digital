<?php

namespace App\Services;

use App\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleService
{
    /**
     * Get permissions grouped by category.
     *
     * @return array<string, \Illuminate\Support\Collection>
     */
    public function getPermissionGroups(): array
    {
        $permissions = Permission::orderBy('name')->get();

        return [
            'Course Management' => $permissions->filter(fn($p) =>
                str_contains($p->name, 'courses') &&
                !str_contains($p->name, 'enroll') &&
                !str_contains($p->name, 'enrolled')
            ),
            'Lesson Management' => $permissions->filter(fn($p) =>
                str_contains($p->name, 'lessons')
            ),
            'Article Management' => $permissions->filter(fn($p) =>
                str_contains($p->name, 'articles')
            ),
            'User Management' => $permissions->filter(fn($p) =>
                str_contains($p->name, 'users') || str_contains($p->name, 'roles')
            ),
            'Enrollment Management' => $permissions->filter(fn($p) =>
                str_contains($p->name, 'enrollments')
            ),
            'Category Management' => $permissions->filter(fn($p) =>
                str_contains($p->name, 'categories')
            ),
            'Tag Management' => $permissions->filter(fn($p) =>
                str_contains($p->name, 'tags')
            ),
            'Admin Panel' => $permissions->filter(fn($p) =>
                str_contains($p->name, 'admin') ||
                str_contains($p->name, 'dashboard') ||
                str_contains($p->name, 'reports') ||
                str_contains($p->name, 'site settings')
            ),
            'System Management' => $permissions->filter(fn($p) =>
                str_contains($p->name, 'manage roles') ||
                str_contains($p->name, 'manage permissions')
            ),
            'Student Features' => $permissions->filter(fn($p) =>
                str_contains($p->name, 'enroll') ||
                str_contains($p->name, 'enrolled') ||
                str_contains($p->name, 'course content') ||
                str_contains($p->name, 'complete lessons')
            ),
        ];
    }

    /**
     * Check if a role is protected (cannot be deleted).
     *
     * @param Role $role
     * @return bool
     */
    public function isProtectedRole(Role $role): bool
    {
        $protectedRoles = config('authorization.protected_roles', []);
        return in_array($role->name, $protectedRoles);
    }

    /**
     * Get critical permissions for a role.
     *
     * @param Role $role
     * @return array<string>
     */
    public function getCriticalPermissions(Role $role): array
    {
        return config("authorization.critical_permissions.{$role->name}", []);
    }

    /**
     * Validate if permission update is allowed for a role.
     *
     * @param Role $role
     * @param array<string> $newPermissions
     * @return array{valid: bool, missing: array<string>}
     */
    public function validatePermissionUpdate(Role $role, array $newPermissions): array
    {
        $criticalPermissions = $this->getCriticalPermissions($role);
        $missingCritical = array_diff($criticalPermissions, $newPermissions);

        return [
            'valid' => empty($missingCritical),
            'missing' => array_values($missingCritical),
        ];
    }
}
