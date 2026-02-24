<?php

namespace App\Http\Controllers\manager_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class ProfileSettingsController extends Controller
{
    public function index()
    {
        $manager = Auth::guard('manager')->user();

        if (!$manager) {
            return redirect()->route('manager.login');
        }

        return view('pages.manager.profile-settings.profileSettings', compact('manager'));
    }



    public function update(Request $request)
{
    $manager = Auth::guard('manager')->user();
    if (!$manager) return abort(403);

    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:manager_accounts,email,' . $manager->manager_id . ',manager_id',
        'contact' => 'required|string|max:20',
        'emergency_contact' => 'required|string|max:20',
        'avatar' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
    ]);

    // Update fields
    $manager->name = $request->name;
    $manager->email = $request->email;
    $manager->contact = $request->contact;
    $manager->emergency_contact = $request->emergency_contact;

    // Handle avatar upload
    if ($request->hasFile('avatar')) {

    // Delete old image if exists
    if ($manager->image && File::exists(public_path($manager->image))) {
        File::delete(public_path($manager->image));
    }

    $image = $request->file('avatar');
    $imageName = 'avatar_' . time() . '.' . $image->getClientOriginalExtension();

    // Create folder if not exists
    $destinationPath = public_path('uploads/managers');
    if (!File::exists($destinationPath)) {
        File::makeDirectory($destinationPath, 0755, true);
    }

    $image->move($destinationPath, $imageName);

    $manager->image = '/uploads/managers/' . $imageName;
}

    $manager->save();

    return back()->with('success', 'Profile updated successfully.');
}

public function updatePassword(Request $request)
{
    $manager = Auth::guard('manager')->user();

    // 1️⃣ Validate input
    $request->validate([
        'current_password' => 'required',
        'new_password' => 'required|min:6|confirmed', // expects new_password_confirmation field
    ]);

    // 2️⃣ Check current password (plain text comparison)
    if ($request->current_password !== $manager->password) {
        return back()->withErrors(['current_password' => 'Current password does not match.']);
    }

    // 3️⃣ Update password (plain text)
    $manager->password = $request->new_password;
    $manager->save();

    return back()->with('success', 'Password updated successfully.');
}
}
