<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('settings')->updateOrInsert(
            ['key' => 'booking_status'],
            ['value' => 'open', 'created_at' => now(), 'updated_at' => now()]
        );
    }
}
