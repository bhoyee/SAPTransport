<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LockScreenController extends Controller
{
    public function show()
    {
        // Log for debugging
        \Log::info('LockScreenController@show called');

        // Check if session is locked
        if (session('is_locked', false)) {
            \Log::info('Session is locked, showing lock screen');
            return view('auth.lockscreen');
        }

        \Log::info('Session is not locked, redirecting to login');
        return redirect()->route('login');
    }

    public function unlock(Request $request)
    {
        $user = Auth::user();
    
        // Check if password matches
        if (is_null($request->password) || !Hash::check($request->password, $user->password)) {
            \Log::info('Invalid password provided');
            return redirect()->route('lockscreen.show')->withErrors(['password' => 'Invalid password.']);
        }
    
        // Check user status before unlocking
        if ($user->status === 'suspend') {
            \Log::info('Attempt to unlock by suspended user', ['user_id' => $user->id]);
            return redirect()->route('login')->withErrors(['error' => 'Your account has been suspended, please contact support.']);
        }
    
        if ($user->status === 'deleted') {
            \Log::info('Attempt to unlock by deleted user', ['user_id' => $user->id]);
            return redirect()->route('login')->withErrors(['error' => 'Your account has been removed from the system, please contact support.']);
        }
    
        // Unlock the session
        \Log::info('Unlocking session', ['user_id' => $user->id]);
        session()->put('is_locked', false);
    
        // Redirect based on role
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'consultant') {
            return redirect()->route('staff.dashboard');
        } else {
            return redirect()->route('passenger.dashboard');
        }
    }
    

    public function handleGoogleUnlock()
    {
        try {
            $user = Auth::user();
            
            // Log user authentication status
            \Log::info('Attempting to unlock with Google', ['user_id' => $user ? $user->id : null]);
    
            // Check if the user is authenticated through Google
            if ($user && $user->provider == 'google') {
    
                // Check user status before unlocking
                if ($user->status === 'suspend') {
                    \Log::info('Attempt to unlock by suspended user (Google)', ['user_id' => $user->id]);
                    return redirect()->route('login')->withErrors(['error' => 'Your account has been suspended, please contact support.']);
                }
    
                if ($user->status === 'deleted') {
                    \Log::info('Attempt to unlock by deleted user (Google)', ['user_id' => $user->id]);
                    return redirect()->route('login')->withErrors(['error' => 'Your account has been removed from the system, please contact support.']);
                }
    
                // Unlock the session
                \Log::info('Google unlock successful, unlocking session', ['user_id' => $user->id]);
                session()->put('is_locked', false); // Unlock session
    
                // Redirect based on role
                if ($user->role === 'admin') {
                    return redirect()->route('admin.dashboard');
                } elseif ($user->role === 'consultant') {
                    return redirect()->route('staff.dashboard');
                } else {
                    return redirect()->route('passenger.dashboard');
                }
            }
    
            // If Google authentication fails
            \Log::info('Google authentication failed, user not authenticated through Google');
            return redirect()->route('login')->withErrors(['error' => 'Google authentication failed.']);
        } catch (\Exception $e) {
            // Catch and log any unexpected errors
            \Log::error('Error during Google unlock', ['error' => $e->getMessage(), 'user_id' => $user ? $user->id : null]);
            return redirect()->route('login')->withErrors(['error' => 'An unexpected error occurred. Please try again.']);
        }
    }
    
    
    
}
