<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ShareDomainPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'view share domains',
            'create share domains',
            'edit share domains',
            'delete share domains',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $admin = Role::findByName('admin');
        if ($admin) {
            $admin->givePermissionTo($permissions);
        }

        $superAdmin = Role::findByName('Super-Admin');
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissions);
        }
    }
}
