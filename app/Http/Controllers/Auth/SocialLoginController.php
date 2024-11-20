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
use Spatie\Permission\Models\Role;

class SocialLoginController extends Controller
{
    // Redirect to Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $socialUser = Socialite::driver('google')->stateless()->user();
    
            Log::info('Social user retrieved', ['email' => $socialUser->email]);
    
            $user = User::where('email', $socialUser->email)->first();
    
            if ($user) {
                Log::info('Existing user found', ['email' => $user->email]);
    
                if ($user->status === 'suspend') {
                    Log::warning('Suspended account attempted login', ['email' => $user->email]);
                    return redirect()->route('login')->with('error', 'Your account has been suspended, please contact support.');
                }
    
                if ($user->status === 'deleted') {
                    Log::warning('Deleted account attempted login', ['email' => $user->email]);
                    return redirect()->route('login')->with('error', 'Your account has been removed from our system, contact support for further clarification.');
                }
    
                Auth::login($user);
                Log::info('User logged in', ['email' => $user->email]);
    
                return redirect()->route('passenger.dashboard');
            } else {
                Log::info('Registering new user', ['email' => $socialUser->email]);
    
                $newUser = User::create([
                    'name' => $socialUser->name,
                    'email' => $socialUser->email,
                    'password' => bcrypt('social-login-password'),
                ]);
    
                Auth::login($newUser);
                Log::info('New user logged in', ['email' => $socialUser->email]);
    
                return redirect()->route('complete.profile');
            }
        } catch (\Exception $e) {
            Log::error('Social login failed', ['error' => $e->getMessage()]);
            return redirect()->route('login')->with('error', 'Unable to authenticate with Google. Please try again.');
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
                'status' => 'inactive',
                'created_by' => $socialUser->email,  // Set created_by as the user's email
            ]);

            // Assign the 'passenger' role to the newly created user
            $newUser->assignRole('passenger');

            // Log the user in
            Auth::login($newUser);

            // Log account creation and login
            ActivityLogger::customLog('Account Created', 'created an account via ' . ucfirst($provider) . ': ' . $socialUser->email, $newUser->id);

            return redirect()->route('complete.profile');
        }
    }
}
