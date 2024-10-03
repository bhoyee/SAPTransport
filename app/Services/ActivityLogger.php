<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    public static function log($action, $description = null, $userId = null)
    {
        // Default to the logged-in user if user ID is not provided
        $userId = $userId ?? (Auth::check() ? Auth::id() : null); 

        ActivityLog::create([
            'user_id' => $userId, // If user is logged in, use the user ID
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
