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
        return view('auth.lockscreen');  // Update the path to reflect the correct location
    }


    public function unlock(Request $request)
    {
        \Log::info('Unlock process initiated by user ID: ' . auth()->id());
    
        // Get the currently authenticated user
        $user = Auth::user();
        
        // Check if the user exists and password was entered
        if (is_null($request->password)) {
            \Log::info('Password was not provided');
            return redirect()->route('lockscreen.show')->withErrors(['password' => 'Please enter a password.']);
        }
    
        // Check if the password matches the user's password
        if (!Hash::check($request->password, $user->password)) {
            \Log::info('Invalid password provided by user ID: ' . $user->id);
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
    
        // Log successful unlock
        \Log::info('Unlocking session for user ID: ' . $user->id);
        session()->forget('is_locked'); // Clear the lock session variable
        
        // Redirect based on role using Spatie's hasRole()
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole('consultant')) {
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
    
                // Redirect based on role using Spatie's hasRole()
                if ($user->hasRole('admin')) {
                    return redirect()->route('admin.dashboard');
                } elseif ($user->hasRole('consultant')) {
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

    public function checkSessionStatus()
    {
        if (Auth::check()) {
            return response()->json(['active' => true]);
        } else {
            return response()->json(['active' => false]);
        }
    }

}
