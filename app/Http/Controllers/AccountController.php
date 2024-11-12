<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\User;


class AccountController extends Controller
{
    public function showAccountPage()
    {
        $user = Auth::user();
        return view('passenger.account', compact('user')); // Ensure view path is 'passenger.account'
    }
    
    public function updateAccount(Request $request)
    {
        $user = Auth::user();
    
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
    
        // Update phone with unique check
        $newPhone = $request->input('phone');
        if ($newPhone && $newPhone !== $user->phone) {
            // Check if the new phone number already exists in the database
            $existingPhone = User::where('phone', $newPhone)->where('id', '!=', $user->id)->exists();
            if ($existingPhone) {
                return redirect()->back()->with('error', 'The phone number already exists. Please choose a different number.');
            }
    
            // If unique, update the user's phone number
            $user->phone = $newPhone;
        }
    
        $user->save();
    
        return redirect()->back()->with('success', 'Profile updated successfully!');
    }
    
    
}
