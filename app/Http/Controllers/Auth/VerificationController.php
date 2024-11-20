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
        // Find the user by ID
        $user = User::findOrFail($request->route('id'));
    
        // Check if the hash matches
        if (!hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            return redirect()->route('verification.notice')->with('error', 'Invalid verification link.');
        }
    
        // If email is not verified, mark it as verified
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
    
            // Update status to 'active' if 'inactive'
            if ($user->status === 'inactive') {
                $user->update(['status' => 'active']);
            }
    
            // Log activity
            Log::info('User email verified successfully', ['user_id' => $user->id]);
            ActivityLogger::log('Email Verified', 'User email verified: ' . $user->email);
    
            return redirect()->route('verification.success')->with('success', 'Email verified successfully!');
        }
    
        // Redirect if already verified
        return redirect()->route('verification.notice')->with('success', 'Email is already verified.');
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

