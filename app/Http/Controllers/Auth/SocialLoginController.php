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
            return redirect()->route('login')->with('error', 'Google login failed. Please try again.');
        }
    }

    // Redirect to Facebook
    public function redirectToFacebook()
    {
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
            return redirect()->route('login')->with('error', 'Facebook login failed. Please try again.');
        }
    }

    // Register or login user
    protected function _registerOrLoginUser($socialUser, $provider)
    {
        // Check if a user with the same email already exists
        $existingUser = User::where('email', $socialUser->email)->first();

        if ($existingUser) {
            // If user exists, allow them to log in, regardless of provider
            Auth::login($existingUser);

            // Check if the user has incomplete profile information
            if (empty($existingUser->phone) || empty($existingUser->gender)) {
                return redirect()->route('complete.profile'); // Redirect to complete profile
            }

            // Redirect to dashboard if the profile is complete
            return redirect()->route('passenger.dashboard');
        } else {
            // If no user exists, create a new user with social details
            $newUser = User::create([
                'name' => $socialUser->name,
                'email' => $socialUser->email,
                'password' => Hash::make('password'),  // Default password for social login users
                'provider' => $provider, // Store provider (Google or Facebook)
            ]);

            // Log the new user in
            Auth::login($newUser);

            // Redirect to the complete profile page
            return redirect()->route('complete.profile');
        }
    }
}
