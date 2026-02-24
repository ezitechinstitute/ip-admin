<?php

namespace App\Http\Controllers\authentications;

use App\Http\Controllers\Controller;
use App\Models\AdminAccount;
use App\Models\ManagersAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginCover extends Controller
{
  public function index()
  {
    $pageConfigs = ['myLayout' => 'blank'];
    return view('content.authentications.auth-login-cover', ['pageConfigs' => $pageConfigs]);
  }

  public function loginAuthForm(Request $request)
{
    $request->validate([
        'email' => 'required|email', 
        'password' => 'required'
    ]);

    $credentials = ['email' => $request->email, 'password' => $request->password];

    // 1. Check in Admin Table using 'admin' guard
    // Attempt to find user in AdminAccount table
    $admin = AdminAccount::where('email', $request->email)
                                     ->where('password', $request->password)->first();

    if ($admin) {
        Auth::guard('admin')->login($admin);
        $request->session()->regenerate();
        return redirect()->route('dashboard-admin');
    }

    // 2. Check in Managers Table using 'manager' guard
    // Attempt to find user in ManagersAccount table
    $manager = ManagersAccount::where('email', $request->email)
                            ->where('password', $request->password)
                            ->first();

if ($manager) {
    // English comments: Check if the manager account is active (status == 1)
    if ($manager->status != 1) {
        return back()->withErrors([
            'email' => 'Your account is deactivated. Please contact the Admin!',
        ])->onlyInput('email');
    }

    // English comments: If active, proceed with login
    Auth::guard('manager')->login($manager);
    $request->session()->regenerate();
    return redirect()->route('manager.dashboard');
}

    // 3. Fallback if both fail
    return back()->withErrors([
        'email' => 'Invalid email or password. Please try again!',
    ])->onlyInput('email');
}

  public function logoutAuth(Request $request)
{
    Auth::guard('admin')->logout();

    $request->session()->forget('admin'); // optional
    $request->session()->regenerate();

    return redirect()->route('login');
}

  public function managerLogout(Request $request)
{
    Auth::guard('manager')->logout();

    $request->session()->forget('manager'); // optional
    $request->session()->regenerate();

    return redirect()->route('login'); 
}
}