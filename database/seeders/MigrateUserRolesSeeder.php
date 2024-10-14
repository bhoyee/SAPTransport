<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class MigrateUserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Ensure roles are created in the roles table
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'consultant']);
        Role::firstOrCreate(['name' => 'passenger']);

        // Fetch users with 'role' column and assign roles using Spatie
        $users = User::all();

        foreach ($users as $user) {
            // Assign roles based on the current 'role' column
            if ($user->role === 'admin') {
                $user->assignRole('admin');
            } elseif ($user->role === 'consultant') {
                $user->assignRole('consultant');
            } elseif ($user->role === 'passenger') {
                $user->assignRole('passenger');
            }
        }

        // Remove the role column if necessary
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
}
