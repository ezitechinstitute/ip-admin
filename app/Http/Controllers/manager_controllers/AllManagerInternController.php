<?php

namespace App\Http\Controllers\manager_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AllManagerInternController extends Controller
{
    public function index(){
        return view('pages.manager.all_interns.allinterns');
    }
}
