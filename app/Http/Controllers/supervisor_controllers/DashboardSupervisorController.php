<?php

namespace App\Http\Controllers\supervisor_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardSupervisorController extends Controller
{
    public function index()
    {
        return view('pages.supervisor.dashboard.dashboard');
    }
}
