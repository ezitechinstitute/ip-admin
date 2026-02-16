<?php

namespace App\Http\Controllers\authentications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminAccount;
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
    $request->validate(['email' => 'required | email', 'password' => 'required']);

    $admin = AdminAccount::where('email', $request->email)
                         ->where('password', $request->password)->first();

    if ($admin) {
        Auth::guard('admin')->login($admin);

        $request->session()->regenerate();

        return redirect()->route('dashboard-admin');
    }
    
    return back()->withErrors([
        'email' => 'Invalid email or password. Please try again!',
    ])->onlyInput('email');
  }

  public function logoutAuth(Request $request){
    Auth::guard('admin')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login');
  }
}