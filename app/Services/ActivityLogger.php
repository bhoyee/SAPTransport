<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB; // Import the DB facade

class ActivityLogger
{
    public static function log($action, $description = null, $userId = null)
    {
        $userId = $userId ?? (Auth::check() ? Auth::id() : null); 

        if (DB::connection()->getDriverName() !== 'sqlite') {
            // This is where your SHOW INDEX query should be 
            $indexExists = DB::select("SHOW INDEX FROM `messages` WHERE Key_name = 'messages_sender_id_index'");
            // ... any other related index check logic ...
        }

        ActivityLog::create([
            'user_id' => $userId, 
            'action' => $action,
            'description' => $description,
            'ip_address' => Request::ip(),
        ]);
    }
    // Customize log description for logged-in users
    public static function customLog($action, $description, $userId = null)
    {
        if (Auth::check() && Auth::id() == $userId) {
            $description = 'You ' . strtolower($description);
        }
        
        self::log($action, $description, $userId);
    }
}