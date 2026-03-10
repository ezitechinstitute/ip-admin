<?php

namespace App\Http\Controllers\manager_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ManagersAccount;



class Supervisorcontroller extends Controller
{
    public function index(){
          $supervisors = ManagersAccount::where('loginas', 'Supervisor')
                        ->latest('manager_id')
                        ->get();

        return view('pages.manager.supervisor.Supervisor',compact('supervisors'));
    }
}
;