<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetSuccessMail; // Import the PasswordResetSuccessMail class
use App\Models\User;
use App\Services\ActivityLogger;


class ForgotPasswordController extends Controller
{
    // Show the form for requesting a password reset link
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email'); // Blade view for the form
    }

    // Handle sending the reset link email
    public function sendResetLinkEmail(Request $request)
    {
        // Validate the input
        $request->validate(['email' => 'required|email']);

        // Check if the email exists in the database
        $user = User::where('email', $request->email)->first();

        // If the email doesn't exist
        if (!$user) {
            return back()->withErrors(['email' => 'This email address is not registered.']);
        }

        // If the email exists but is not verified
        if (!$user->hasVerifiedEmail()) {
            return back()->withErrors(['email' => 'This email address is not verified. Please verify your email first.']);
        }

        // If everything is correct, send the password reset link
        $status = Password::sendResetLink($request->only('email'));

        // Return response based on status
        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))  // Link sent successfully
            : back()->withErrors(['email' => __($status)]);  // Error while sending link
    }

    // Show the reset password form (with token)
    public function showResetForm($token)
    {
        return view('auth.passwords.reset', ['token' => $token]);
    }

    // Handle the password reset
    public function reset(Request $request)
    {
        // Validate the form input
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        // Attempt to reset the user's password
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                // Update the password
                $user->forceFill([
                    'password' => bcrypt($password),
                ])->save();

                // Send password reset success email
                Mail::to($user->email)->send(new PasswordResetSuccessMail($user));
            }
        );

         // Check if the password was successfully reset
            if ($status === Password::PASSWORD_RESET) {
                // Redirect to the password reset success page
                return redirect()->route('password.reset.success')->with('status', __($status));
            } else {
                // If there was an issue, show an error on the same page
                return back()->withErrors(['email' => [__($status)]]);
            }
    }
}
