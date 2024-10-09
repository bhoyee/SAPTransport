<?php



namespace App\Http\Controllers\Auth;



use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Illuminate\Foundation\Auth\EmailVerificationRequest;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Mail;

use App\Mail\UserRegistered;

use App\Services\ActivityLogger;
use App\Models\User;






class VerificationController extends Controller

{

    // Show the email verification notice page

    public function show()

    {

        return view('auth.verify-email'); // A custom view asking the user to verify their email

    }



    // Handle the email verification process

    public function verify(Request $request)
    {
        // Find the user by the ID passed in the URL route
        $user = User::findOrFail($request->route('id'));
    
        // Check if the verification hash matches
        if (! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            return redirect()->route('verification.notice')->with('error', 'Invalid verification link.');
        }
    
        // Check if the user's email is already verified
        if (!$user->hasVerifiedEmail()) {
            // Mark the user's email as verified
            $user->markEmailAsVerified();
    
            // Update the user's status to active if they are inactive
            if ($user->status === 'inactive') {
                $user->update(['status' => 'active']);
                Log::info('User status updated to active', ['user_id' => $user->id]);
            }
    
            Log::info('Email verification successful', ['user_id' => $user->id]);
    
            // Send welcome email after email verification
            Mail::to($user->email)->send(new UserRegistered($user));
    
            // Log the activity
            ActivityLogger::log('Email Verified', 'User email verified and status updated: ' . $user->email);
        }
    
        // Redirect to the custom verification success page
        return redirect()->route('verification.success')->with('success', 'Email verified successfully!');
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

