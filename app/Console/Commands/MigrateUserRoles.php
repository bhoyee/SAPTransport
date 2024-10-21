<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;

class MigrateUserRoles extends Command
{
    protected $signature = 'migrate:user-roles';
    protected $description = 'Migrate user roles from users table to model_has_roles table';

    public function handle()
    {
        Log::info('Starting user role migration.');

        // Map old roles in users table to new roles
        $roleMapping = [
            'admin' => 'admin',
            'consultant' => 'consultant',
            'passenger' => 'passenger',
        ];

        $users = User::all(); // Get all users

        foreach ($users as $user) {
            $oldRole = $user->role; // Get the old role from users table

            if ($oldRole && isset($roleMapping[$oldRole])) {
                $newRole = Role::where('name', $roleMapping[$oldRole])->first();

                if ($newRole) {
                    // Assign the new role to the user
                    $user->assignRole($newRole->name);
                    Log::info("Assigned role {$newRole->name} to user {$user->id}");
                } else {
                    Log::error("Role {$roleMapping[$oldRole]} not found in roles table.");
                }
            } else {
                Log::warning("User {$user->id} has no recognized role.");
            }
        }

        Log::info('User role migration completed.');
        $this->info('User role migration completed.');
    }
}
