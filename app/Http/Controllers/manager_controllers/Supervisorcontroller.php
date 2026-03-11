<?php

namespace App\Http\Controllers\manager_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ManagersAccount;
use Illuminate\Support\Facades\Auth;

class Supervisorcontroller extends Controller
{
    public function index()
    {
        // Get logged in manager
        $manager = Auth::guard('manager')->user();

        if (!$manager) {
            return redirect('/login');
        }

        $managerId = $manager->manager_id;

        // Only supervisors assigned to this manager
        $supervisors = ManagersAccount::where('loginas', 'Supervisor')
                        ->where('assigned_manager', $managerId)
                        ->latest('manager_id')
                        ->paginate();

        return view('pages.manager.supervisor.Supervisor', compact('supervisors'));
    }
}