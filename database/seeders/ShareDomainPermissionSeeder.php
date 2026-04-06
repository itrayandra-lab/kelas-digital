<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ShareDomainPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        $permissions = [
            'view share domains',
            'create share domains',
            'edit share domains',
            'delete share domains',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
            $this->command->info("✓ Created: {$permission}");
        }

        // Assign to admin
        $admin = Role::findByName('admin');
        if ($admin) {
            $admin->givePermissionTo($permissions);
            $this->command->info('✓ Permissions assigned to Admin role');
        }

        // Assign to Super-Admin (if exists)
        $superAdmin = Role::findByName('Super-Admin');
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissions);
            $this->command->info('✓ Permissions assigned to Super-Admin role');
        }

        $this->command->info('');
        $this->command->info('✓ Done! Logout and login again.');
    }
}