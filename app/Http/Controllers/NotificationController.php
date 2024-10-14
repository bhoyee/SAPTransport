<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // Fetch recent notifications for the dropdown
    public function fetchRecentNotifications()
    {
        $user = Auth::user();
        
        // Fetch notifications based on user role
        if ($user->hasRole(['admin', 'consultant'])) {
            // Admins and consultants can see all notifications
            $notifications = Notification::orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        } else {
            // Passengers or other users only see their own notifications
            $notifications = Notification::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }

        // Unread count logic based on role
        $unreadCount = $user->hasRole(['admin', 'consultant']) 
            ? Notification::where('status', 'unread')->count() 
            : Notification::where('user_id', $user->id)->where('status', 'unread')->count();

        return response()->json([
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    // View all notifications with pagination
    public function viewAll(Request $request)
    {
        $user = Auth::user();

        $query = $user->hasRole(['admin', 'consultant'])
            ? Notification::query()  // Admins and consultants see all notifications
            : Notification::where('user_id', $user->id);  // Passengers see only their own

        // Filter by type if selected
        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(10); // Adjust pagination as needed

        return view('admin.notifications.index', compact('notifications'));
    }

    // Mark notification as read
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['status' => 'read']);

        return response()->json([
            'message' => $notification->message,
            'created_at' => $notification->created_at->diffForHumans(),
        ]);
    }
}
