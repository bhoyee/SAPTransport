<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Services\ActivityLogger;

class SocialLoginController extends Controller
{
    // Redirect to Google
    public function redirectToGoogle()
    {
        ActivityLogger::log('Login Attempt', 'User attempting to login via Google');
        return Socialite::driver('google')->redirect();
    }

    // Handle Google callback
    public function handleGoogleCallback()
    {
        try {
            $socialUser = Socialite::driver('google')->stateless()->user();
            return $this->_registerOrLoginUser($socialUser, 'google');
        } catch (\Exception $e) {
            Log::error('Google login failed: ' . $e->getMessage());
            ActivityLogger::log('Login Failed', 'Google login failed: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Google login failed. Please try again.');
        }
    }

    // Redirect to Facebook
    public function redirectToFacebook()
    {
        ActivityLogger::log('Login Attempt', 'User attempting to login via Facebook');
        return Socialite::driver('facebook')->redirect();
    }

    // Handle Facebook callback
    public function handleFacebookCallback()
    {
        try {
            $socialUser = Socialite::driver('facebook')->stateless()->user();
            return $this->_registerOrLoginUser($socialUser, 'facebook');
        } catch (\Exception $e) {
            Log::error('Facebook login failed: ' . $e->getMessage());
            ActivityLogger::log('Login Failed', 'Facebook login failed: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Facebook login failed. Please try again.');
        }
    }

    // Register or login user
// Register or login user
    protected function _registerOrLoginUser($socialUser, $provider)
    {
        $existingUser = User::where('email', $socialUser->email)->first();

        // If the user already exists
        if ($existingUser) {
            
            // Check if the user is suspended or deleted
            if ($existingUser->status === 'suspend') {
                // Log the suspended account attempt
                ActivityLogger::customLog('Login Failed - Account Suspended', 'User attempted to log in with suspended account via ' . ucfirst($provider) . ': ' . $socialUser->email, $existingUser->id);
                
                return redirect()->route('login')->with('error', 'Your account has been suspended, please contact support.');
            }

            if ($existingUser->status === 'deleted') {
                // Log the deleted account attempt
                ActivityLogger::customLog('Login Failed - Account Deleted', 'User attempted to log in with deleted account via ' . ucfirst($provider) . ': ' . $socialUser->email, $existingUser->id);
                
                return redirect()->route('login')->with('error', 'Your account has been removed from the system, please contact support.');
            }

            // Log the user in
            Auth::login($existingUser);

            // Log activity for login with customization
            ActivityLogger::customLog('Login Success', 'logged in via ' . ucfirst($provider) . ': ' . $socialUser->email, $existingUser->id);

            // Check for incomplete profile
            if (empty($existingUser->phone) || empty($existingUser->gender)) {
                return redirect()->route('complete.profile');
            }

            // Redirect to the dashboard
            return redirect()->route('passenger.dashboard');
        } else {
            // If the user does not exist, create a new account
            $newUser = User::create([
                'name' => $socialUser->name,
                'email' => $socialUser->email,
                'password' => Hash::make('password'), // Default password
                'provider' => $provider,
                'created_by' => $socialUser->email,  // Set created_by as the user's email
            ]);

            // Log the user in
            Auth::login($newUser);

            // Log account creation and login
            ActivityLogger::customLog('Account Created', 'created an account via ' . ucfirst($provider) . ': ' . $socialUser->email, $newUser->id);

            return redirect()->route('complete.profile');
        }
    }

}
