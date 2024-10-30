<?php

// app/Http/Controllers/Admin/SettingsController.php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\ActivityLog;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    public function showSettings()
    {
        return view('admin.settings');
    }
    public function changePassword(Request $request)
    {
        \Log::info('Attempting to change password for user.', ['user_id' => Auth::id()]);
    
        // Validate inputs
        $validatedData = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);
    
        $user = Auth::user();
    
        // Verify the current password
        if (!Hash::check($request->current_password, $user->password)) {
            \Log::warning('Current password is incorrect.', ['user_id' => $user->id]);
            return response()->json(['message' => 'Current password is incorrect.', 'success' => false]);
        }
    
        // Update password
        try {
            $user->update(['password' => Hash::make($request->new_password)]);
            \Log::info('Password updated successfully.', ['user_id' => $user->id]);
            return response()->json(['message' => 'Password updated successfully.', 'success' => true]);
        } catch (\Exception $e) {
            \Log::error('Failed to update password.', ['error' => $e->getMessage(), 'user_id' => $user->id]);
            return response()->json(['message' => 'Failed to update password. Please try again.', 'success' => false], 500);
        }
    }
    

    public function fetchActivityLog()
    {
        Log::info('Fetching activity log data...'); // Log entry to verify method access
    
        try {
            Log::info('Attempting to retrieve activity logs from the database ordered by updated_at.');
    
            $activityLogs = ActivityLog::with('user')
                ->orderBy('updated_at', 'desc') // Order by updated_at column
                ->get(['id', 'user_id', 'action', 'description', 'ip_address', 'updated_at', 'created_at'])
                ->map(function ($log) {
                    Log::info('Mapping log entry', [
                        'id' => $log->id,
                        'user_id' => $log->user_id,
                        'action' => $log->action,
                        'description' => $log->description,
                        'ip_address' => $log->ip_address,
                        'updated_at' => $log->updated_at,
                    ]);
    
                    return [
                        'id' => $log->id,
                        'user_name' => optional($log->user)->name ?? 'N/A',
                        'action' => $log->action,
                        'description' => $log->description,
                        'ip_address' => $log->ip_address,
                        'timestamp' => $log->updated_at ? $log->updated_at->format('Y-m-d H:i:s') : $log->created_at->format('Y-m-d H:i:s'), // Use updated_at or created_at as fallback
                    ];
                });
    
            Log::info('Activity logs successfully retrieved and formatted.', ['count' => $activityLogs->count()]);
    
            return response()->json($activityLogs);
        } catch (\Exception $e) {
            Log::error('Failed to fetch activity log:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to load activity log data'], 500);
        }
    }
    
}

