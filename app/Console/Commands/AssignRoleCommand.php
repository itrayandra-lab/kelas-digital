<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class AssignRoleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:assign-role {email} {role}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign a role to a user by email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $roleName = $this->argument('role');

        // Find user by email
        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("User with email '{$email}' not found.");

            return 1;
        }

        // Check if role exists
        $role = Role::where('name', $roleName)->first();

        if (! $role) {
            $this->error("Role '{$roleName}' not found.");
            $this->info('Available roles: '.Role::all()->pluck('name')->implode(', '));

            return 1;
        }

        // Check if user already has the role
        if ($user->hasRole($roleName)) {
            $this->info("User '{$email}' already has the role '{$roleName}'.");

            return 0;
        }

        // Assign the role
        $user->assignRole($roleName);

        $this->info("Successfully assigned role '{$roleName}' to user '{$email}'.");

        // Show user's current roles
        $this->info('User current roles: '.$user->getRoleNames()->implode(', '));

        return 0;
    }
}
