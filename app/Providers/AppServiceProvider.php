<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Notification;

use Illuminate\Support\Facades\Auth;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share notifications with the admin navbar view
        View::composer('admin.partials.navbar', function ($view) {
            if (Auth::check()) {
                $notifications = Notification::where('user_id', Auth::id())
                                             ->orderBy('created_at', 'desc')
                                             ->take(5)
                                             ->get();
    
                $unreadCount = Notification::where('user_id', Auth::id())
                                           ->where('status', 'unread')
                                           ->count();
    
                // Share the notifications and unread count with the view
                $view->with([
                    'notifications' => $notifications,
                    'unreadCount' => $unreadCount,
                ]);
            }
        });
    }
    
}
