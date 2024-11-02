<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    // Display the settings page
    public function index()
    {
        $settings = Setting::all();
        return view('admin.settings.index', compact('settings'));
    }

    // Update a setting
    public function update(Request $request, $id)
    {
        $setting = Setting::findOrFail($id);
        $setting->value = $request->input('value');
        $setting->save();

        return redirect()->route('admin.settings.index')->with('success', 'Setting updated successfully.');
    }
}
