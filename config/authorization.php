<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Protected Roles
    |--------------------------------------------------------------------------
    |
    | These roles are critical to the system and cannot be deleted.
    | - super-admin: Required for system administration and bootstrap access
    | - student: Foundational for enrollment and course access functionality
    |
    */
    'protected_roles' => [
        'super-admin',
        'student',
    ],

    /*
    |--------------------------------------------------------------------------
    | Critical Permissions
    |--------------------------------------------------------------------------
    |
    | These permissions cannot be revoked from their associated protected roles.
    | Attempting to remove these permissions will result in a validation error.
    |
    | Format: 'role-name' => ['permission-1', 'permission-2', ...]
    |
    */
    'critical_permissions' => [
        'super-admin' => [
            'manage roles and permissions',
        ],
        'student' => [
            'enroll in courses',
            'view enrolled courses',
        ],
    ],
];
