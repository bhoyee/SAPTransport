<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Uncomment this if you want to seed multiple users using the factory
        // User::factory(10)->create();

        // This line is the one causing the issue, so it needs to be commented out or removed
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        
        // Call the UsersTableSeeder to seed the admin and staff users
        $this->call(UsersTableSeeder::class);
    }
}
