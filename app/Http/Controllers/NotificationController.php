<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    // Fetch recent notifications for the dropdown
    public function fetchRecentNotifications()
    {
        $user = Auth::user();
        Log::info('Fetching recent notifications for user:', ['user_id' => $user->id, 'role' => $user->roles->pluck('name')->toArray()]);

        try {
            // Fetch notifications for the logged-in user
            $notifications = Notification::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
            
            // Calculate unread count for the user
            $unreadCount = Notification::where('user_id', $user->id)
                ->where('status', 'unread')
                ->count();

            Log::info('Unread count calculated for user:', ['user_id' => $user->id, 'unread_count' => $unreadCount]);
            Log::info('Fetched recent notifications.', ['unread_count' => $unreadCount, 'notifications_count' => $notifications->count()]);

            return response()->json([
                'notifications' => $notifications,
                'unreadCount' => $unreadCount,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching recent notifications:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch notifications'], 500);
        }
    }

    public function fetchUnredRecentNotifications()
{
    $user = Auth::user();
    Log::info('Fetching recent notifications for user:', ['user_id' => $user->id, 'role' => $user->roles->pluck('name')->toArray()]);

    try {
        // Fetch notifications for the logged-in user
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Calculate unread count for the user
        $unreadCount = Notification::where('user_id', $user->id)
            ->where('status', 'unread')
            ->count();

        Log::info('Fetched recent notifications.', ['unread_count' => $unreadCount, 'notifications_count' => $notifications->count()]);

        return response()->json([
            'notifications' => $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'message' => $notification->message,
                    'status' => $notification->status,
                    'created_at' => $notification->created_at->diffForHumans(),
                ];
            }),
            'unreadCount' => $unreadCount,
        ]);
    } catch (\Exception $e) {
        Log::error('Error fetching recent notifications:', ['error' => $e->getMessage()]);
        return response()->json(['error' => 'Failed to fetch notifications'], 500);
    }
}


    // View all notifications with pagination
    public function viewAll(Request $request)
    {
        $user = Auth::user();
        Log::info('Viewing all notifications for user:', ['user_id' => $user->id, 'role' => $user->roles->pluck('name')->toArray()]);

        try {
            // Filter notifications for the logged-in user
            $query = Notification::where('user_id', $user->id);

            // Filter by type if selected
            if ($request->filled('type') && $request->type !== 'all') {
                Log::info('Filtering notifications by type.', ['type' => $request->type]);
                $query->where('type', $request->type);
            }

            $notifications = $query->orderBy('created_at', 'desc')->paginate(10);
            Log::info('Fetched all notifications with pagination.', ['total_notifications' => $notifications->total()]);

            return view('admin.notifications.index', compact('notifications'));
        } catch (\Exception $e) {
            Log::error('Error viewing all notifications:', ['error' => $e->getMessage()]);
            return redirect()->back()->withErrors('Failed to load notifications.');
        }
    }

    // Mark notification as read
    public function markAsRead($id)
    {
        Log::info('Marking notification as read.', ['notification_id' => $id]);

        try {
            $notification = Notification::where('id', $id)
                ->where('user_id', Auth::id()) // Ensure the notification belongs to the current user
                ->firstOrFail();

            $notification->update(['status' => 'read']);

            // Recalculate the unread count for the current user
            $unreadCount = Notification::where('user_id', Auth::id())
                ->where('status', 'unread')
                ->count();

            Log::info('Notification marked as read successfully.', ['notification_id' => $id, 'unread_count' => $unreadCount]);

            return response()->json([
                'message' => $notification->message,
                'created_at' => $notification->created_at->diffForHumans(),
                'unreadCount' => $unreadCount, // Return the updated unread count
            ]);
        } catch (\Exception $e) {
            Log::error('Error marking notification as read:', ['notification_id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to mark notification as read'], 500);
        }
    }





    // Fetch notification details
    public function fetchNotification($id)
    {
        try {
            $notification = Notification::findOrFail($id);
            return response()->json([
                'message' => $notification->message,
                'created_at' => $notification->created_at->format('d M, Y H:i:s')
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch notification details:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to load notification details.'], 500);
        }
    }

// Mark notification as read


}
