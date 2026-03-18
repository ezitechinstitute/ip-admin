<?php

namespace App\Http\Controllers\supervisor_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupervisorProfileController extends Controller
{
    public function index()
    {
        $profile = (object) [
            'name' => 'Supervisor User',
            'email' => 'supervisor@example.com',
            'phone' => '03001234567',
            'role' => 'Supervisor'
        ];

        return view('content.supervisor.profile-settings', compact('profile'));
    }
}