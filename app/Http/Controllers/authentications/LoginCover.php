<?php

namespace App\Http\Controllers\authentications;

use App\Http\Controllers\Controller;
use App\Models\AdminAccount;
use App\Models\ManagersAccount;
use App\Models\InternAccount;  
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
  

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

    // Check Intern (Plain text password check)
    $intern = InternAccount::where('email', $request->email)->first();
    
    if ($intern && $request->password == $intern->password) {  // ✅ Changed to plain text
        Auth::guard('intern')->login($intern);
        $request->session()->regenerate();
        return redirect()->route('intern.dashboard');
    }

    // 1. Check in Admin Table using 'admin' guard (Plain text)
    $admin = AdminAccount::where('email', $request->email)->first();

    if ($admin && $request->password == $admin->password) {  // ✅ Changed to plain text
        Auth::guard('admin')->login($admin);
        $request->session()->regenerate();
        return redirect()->route('dashboard-admin');
    }

    // 2. Check in Managers/Supervisors Table using 'manager' guard (Plain text)
    $manager = ManagersAccount::where('email', $request->email)->first();

    if ($manager && $request->password == $manager->password) {  // ✅ Changed to plain text
        if ($manager->status != 1) {
            return back()->withErrors([
                'email' => 'Your account is deactivated. Please contact the Admin!'
            ])->onlyInput('email');
        }

        Auth::guard('manager')->login($manager);
        $request->session()->regenerate();

        // Store custom session values for middleware
        session([
            'manager_id' => $manager->manager_id,
            'loginas' => $manager->loginas,
            'manager_email' => $manager->email,
            'manager_name' => $manager->name,
            'manager_department' => $manager->department ?? null,
        ]);

        if ($manager->loginas === 'Supervisor') {
            return redirect()->route('supervisor.dashboard');
        }

        return redirect()->route('manager.dashboard');
    }

    // 4. Fallback if all fail
    Log::info('LOGIN_FAILED', [
        'email' => $request->email,
        'attempted_password' => $request->password,
    ]);
    
    return back()->withErrors([
        'email' => 'Invalid email or password. Please try again!',
    ])->onlyInput('email');
}

  public function logoutAuth(Request $request)
{
    Auth::guard('admin')->logout();
    Auth::guard('intern')->logout();

    $request->session()->forget('admin');
    $request->session()->regenerate();

    return redirect()->route('login');
}

  public function managerLogout(Request $request)
{
    Auth::guard('manager')->logout();

    $request->session()->forget('manager');
    $request->session()->regenerate();

    return redirect()->route('login'); 
}

  public function internLogout(Request $request)
{
    Auth::guard('intern')->logout();

    $request->session()->forget('intern');
    $request->session()->regenerate();

    return redirect()->route('login');
}
}