<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\ActivityLogger;

class ProfileController extends Controller
{
    public function showCompleteProfileForm()
    {
        $user = Auth::user();
        return view('auth.complete-profile', compact('user'));
    }

        public function saveProfile(Request $request)
    {
        // Validate the form input
        $request->validate([
            'phone' => 'required|numeric',
            'gender' => 'required|string|in:male,female',
        ]);

        // Get the current authenticated user
        $user = Auth::user();

        // Update the user's profile with phone and gender
        $user->update([
            'phone' => $request->phone,
            'gender' => $request->gender,
        ]);

        // If the user's email is not verified, send the verification link
        if ($user->email_verified_at === null) {
            // Log that the verification email is being sent
            Log::info('Sending verification email after profile update', ['user_id' => $user->id]);

            // Send the email verification notification
            $user->sendEmailVerificationNotification();

            // Redirect to the verification notice page
            return redirect()->route('verification.notice')->with('resent', true);
        }

        // If the user's email is already verified, redirect to the dashboard
        return redirect()->route('passenger.dashboard');
    }

}
