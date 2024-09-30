<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    public function showAccountPage()
    {
        $user = Auth::user(); // Get the logged-in user
        return view('passenger.account', compact('user'));
    }

    public function updateAccount(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'phone' => 'nullable|regex:/^[0-9]{10,15}$/',
            'profile_image' => 'nullable|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            if ($user->profile_image) {
                // Remove the old image
                Storage::delete('public/profile_images/' . $user->profile_image);
            }

            $file = $request->file('profile_image');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/profile_images', $fileName);

            $user->profile_image = $fileName;
        }

        // Update phone
        if ($request->input('phone')) {
            $user->phone = $request->input('phone');
        }

        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }
}
