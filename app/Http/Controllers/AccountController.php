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
        $user = Auth::user();
    
        // Ensure only passengers can access this account page
        if (!$user->hasRole('passenger')) {
            return redirect()->route('home')->with('error', 'You do not have access to this page.');
        }
    
        return view('passenger.account', compact('user'));
    }
    
    public function updateAccount(Request $request)
    {
        $user = Auth::user();
    
        // Ensure only passengers can update this account
        if (!$user->hasRole('passenger')) {
            return redirect()->route('home')->with('error', 'You do not have permission to update this account.');
        }
    
        // Validate input
        $validator = Validator::make($request->all(), [
            'phone' => 'nullable|regex:/^[0-9]{10,15}$/',
            'profile_image' => 'nullable|mimes:jpg,jpeg,png|max:2048',
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
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
