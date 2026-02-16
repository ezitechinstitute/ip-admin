<?php

namespace App\Http\Controllers;

use App\Models\AdminAccount;
use App\Models\AdminSetting; // Naya model shamil kiya
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;

class SettingsController extends Controller
{
    public function index()
    {
        $admin = AdminAccount::first();
        
        $settings = AdminSetting::first() ?? new AdminSetting();

        $countries = config('countries');
        $languages = ['en' => 'English', 'fr' => 'French', 'de' => 'German', 'pt' => 'Portuguese'];
        $timezones = ['-5' => '(GMT-05:00) Eastern Time (US & Canada)', '-8' => '(GMT-08:00) Pacific Time'];
        $currencies = ['usd' => 'USD', 'euro' => 'Euro'];

        return view(
            'pages.admin.settings.settingsTask',
            compact('admin', 'settings', 'countries', 'languages', 'timezones', 'currencies')
        );
    }

public function updateAllSettings(Request $request)
{
    // 1. Validation
    $request->validate([
        'name' => 'nullable|string|max:255',
        'email' => 'nullable|email|max:255',
        'avatar' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        'system_logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        'pagination_limit' => 'nullable|integer',
        'interview_timeout' => 'nullable|integer',
        'internship_duration' => 'nullable|integer',
    ]);

    // 2. Update Admin Profile (Tab 1)
    $admin = AdminAccount::first();
    if ($request->has('name')) $admin->name = $request->name;
    if ($request->has('email')) $admin->email = $request->email;

    if ($request->hasFile('avatar')) {
        if ($admin->image && File::exists(public_path($admin->image))) {
            File::delete(public_path($admin->image));
        }
        $image = $request->file('avatar');
        $imageName = 'avatar_' . time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('uploads/admins'), $imageName);
        $admin->image = '/uploads/admins/' . $imageName;
    }
    $admin->save();

    // 3. Update System Settings
    $settings = AdminSetting::first() ?: new AdminSetting;

    // Branding Update (Logo)
    if ($request->hasFile('system_logo')) {
        if ($settings->system_logo && File::exists(public_path($settings->system_logo))) {
            File::delete(public_path($settings->system_logo));
        }
        $logo = $request->file('system_logo');
        $logoName = 'logo_' . time() . '.' . $logo->getClientOriginalExtension();
        $logo->move(public_path('uploads/branding'), $logoName);
        $settings->system_logo = '/uploads/branding/' . $logoName;
    }

    // 4. Update SMTP & Notifications (Tab 2)
    if ($request->hasAny(['smtp_active_check', 'notify_intern_reg', 'smtp_host'])) {
        $settings->smtp_active_check = $request->has('smtp_active_check') ? 1 : 0;
        $settings->notify_intern_reg = $request->has('notify_intern_reg') ? 1 : 0;
        
        $settings->fill($request->only([
            'smtp_host', 'smtp_port', 'smtp_username', 'smtp_password', 'smtp_email'
        ]));
    }

    // 5. Update Advanced Settings & Permissions (Tab 3)
    // Check fields or if the form was submitted from Tab 3
    if ($request->hasAny(['pagination_limit', 'expense_categories', 'interview_timeout', 'internship_duration', 'export_permissions'])) {
        
        $settings->fill($request->only([
            'interview_timeout', 'pagination_limit', 'internship_duration'
        ]));

        // Expense Categories
        if ($request->has('expense_categories')) {
            $categories = array_filter(array_map('trim', explode(',', $request->expense_categories)));
            $settings->expense_categories = array_values($categories);
        }

        $roles = ['admin', 'manager', 'supervisor', 'intern'];
        $permissions = [];
        foreach ($roles as $role) {
            $permissions[$role] = $request->has("export_permissions.$role") ? 1 : 0;
        }
        $settings->export_permissions = $permissions;
    }

    $settings->save();

    return redirect()->back()->with('success', 'Settings updated successfully!');
}



    public function updatePassword(Request $request)
{
    $request->validate([
        'current_password' => 'required',
        'new_password'     => 'required',
        'confirm_password' => 'required|same:new_password',
    ], [
        'confirm_password.same' => 'The confirmation password does not match the new password.',
    ]);

    $admin = AdminAccount::first(); 

    if (!$admin) {
        return back()->withErrors(['error' => 'Admin account not found.']);
    }

    if ($request->current_password !== $admin->password) {
        return back()->withErrors(['current_password' => 'The provided current password does not match our records.']);
    }

    // 4. Password Update Karein
    $admin->password = $request->new_password;
    $admin->save();

    return back()->with('success', 'Password updated successfully!');
}
}