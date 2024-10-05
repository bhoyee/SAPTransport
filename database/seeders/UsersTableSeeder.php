<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;   // Import the DB facade
use Illuminate\Support\Facades\Hash; // Import the Hash facade for password encryption

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // Check if admin user exists, update or insert new
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@saptransportationandlogistics.ng'],  // Check for existing admin by email
            [
                'name' => 'SAP Admin',
                'password' => Hash::make('password123'),  // Using bcrypt to hash the password
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Check if staff user exists, update or insert new
        DB::table('users')->updateOrInsert(
            ['email' => 'test.staff@saptransportationandlogistics.ng'],  // Check for existing staff by email
            [
                'name' => 'James Adoga',
                'password' => Hash::make('password123'),  // Using bcrypt to hash the password
                'role' => 'consultant',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
