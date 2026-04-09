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

    // DEBUG: Log password comparison
    Log::info('LOGIN_ATTEMPT', [
        'email' => $request->email,
        'submitted_password' => $request->password,
    ]);

    // Check Intern (same pattern as admin and manager)
    $intern = InternAccount::where('email', $request->email)->first();
    
    if ($intern) {
        Log::info('INTERN_FOUND', [
            'email' => $intern->email,
            'db_password' => $intern->password,
            'match' => ($intern->password === $request->password),
        ]);
    }
    
    if ($intern && $intern->password === $request->password) {
        Auth::guard('intern')->login($intern);
        $request->session()->regenerate();
        return redirect()->route('intern.dashboard');
    }

    // 1. Check in Admin Table using 'admin' guard
    $admin = AdminAccount::where('email', $request->email)->first();

    if ($admin) {
        Log::info('ADMIN_FOUND', [
            'email' => $admin->email,
            'db_password' => $admin->password,
            'submitted_password' => $request->password,
            'match' => ($admin->password === $request->password),
        ]);
    }

    if ($admin && $admin->password === $request->password) {
        Auth::guard('admin')->login($admin);
        $request->session()->regenerate();
        return redirect()->route('dashboard-admin');
    }

    // 2. Check in Managers/Supervisors Table using 'manager' guard
    $manager = ManagersAccount::where('email', $request->email)->first();

    if ($manager) {
        Log::info('MANAGER_FOUND', [
            'email' => $manager->email,
            'db_password' => $manager->password,
            'submitted_password' => $request->password,
            'match' => ($manager->password === $request->password),
            'status' => $manager->status,
        ]);
    }

    if ($manager && $manager->password === $request->password) {
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
    Auth::guard('intern')->logout();  // ← intern logout

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