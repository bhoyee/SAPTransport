<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserRegistered;
use App\Services\ActivityLogger;


class VerificationController extends Controller
{
    // Show the email verification notice page
    public function show()
    {
        return view('auth.verify-email'); // A custom view asking the user to verify their email
    }

    // Handle the email verification process
    public function verify(EmailVerificationRequest $request)
    {
        // Marks the user's email as verified
        $request->fulfill();

        Log::info('Email verification successful', ['user_id' => $request->user()->id]);
        
        // Send welcome email after email verification
        Mail::to($request->user()->email)->send(new UserRegistered($request->user()));

        // Redirect to the custom verification success page
        return redirect()->route('verification.success');
    }

    // Resend the email verification link
    public function resend(Request $request)
    {
        // If the user has already verified their email, redirect them to the dashboard
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended('/passenger/dashboard');
        }

        // Resend the verification link
        $request->user()->sendEmailVerificationNotification();

        Log::info('Verification email resent', ['user_id' => $request->user()->id]);

        // After resending, redirect to the verification notice page with a success message
        return redirect()->route('verification.notice')->with('resent', true);
    }
}
