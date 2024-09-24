<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    public static function log($action, $description = null)
    {
        $userId = Auth::check() ? Auth::id() : null; // If user is logged in, get the user ID, otherwise null

        ActivityLog::create([
            'user_id' => $userId, // User might not be logged in
            'action' => $action,
            'description' => $description,
            'ip_address' => Request::ip(),
        ]);
    }
}
