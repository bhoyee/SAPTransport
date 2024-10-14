<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;   
use Illuminate\Support\Facades\Hash;
use App\Models\User; // Use the User model to assign roles
use Spatie\Permission\Models\Role; // Use Spatie's Role model

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // Ensure roles exist before assigning them
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $consultantRole = Role::firstOrCreate(['name' => 'consultant']);

        // Check if admin user exists, update or insert new
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@saptransportationandlogistics.ng'],  // Check for existing admin by email
            [
                'name' => 'SAP Admin',
                'password' => Hash::make('password123'),  // Using bcrypt to hash the password
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Assign the admin role using Spatie's method
        $adminUser->assignRole($adminRole);

        // Check if staff user exists, update or insert new
        $staffUser = User::updateOrCreate(
            ['email' => 'test.staff@saptransportationandlogistics.ng'],  // Check for existing staff by email
            [
                'name' => 'James Adoga',
                'password' => Hash::make('password123'),  // Using bcrypt to hash the password
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Assign the consultant role using Spatie's method
        $staffUser->assignRole($consultantRole);
    }
}
