<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class Analytics extends Controller
{
  public function index()
  {
    if(Auth::check()){
      return view('content.dashboard.dashboards-analytics');
    }else{
      return view('content.dashboard.dashboards-analytics');
    }
    
  }
}
