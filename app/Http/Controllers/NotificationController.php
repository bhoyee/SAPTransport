<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    //
     // Fetch recent notifications for the dropdown
     public function fetchRecentNotifications()
     {
         $userId = Auth::id();
         $notifications = Notification::where('user_id', $userId)
             ->orderBy('created_at', 'desc')
             ->limit(5)
             ->get();
     
         $unreadCount = Notification::where('user_id', $userId)
             ->where('status', 'unread')
             ->count();
     
         return response()->json([
             'notifications' => $notifications,
             'unreadCount' => $unreadCount,
         ]);
     }
     // View all notifications with pagination
     public function viewAll(Request $request)
     {
         $query = Notification::where('user_id', Auth::id());
     
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
