<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Show the form to create a user
    public function create()
{
    $modules = Module::all();
    // Path: resources/views/pages/admin/users/create.blade.php
    return view('pages.admin.users.create', compact('modules')); 
}
    // Store the new user
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'role' => 'required',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            // The cast in User model automatically converts this array to JSON
            'assigned_modules' => $request->modules ?? [], 
        ]);

        return redirect()->back()->with('success', 'User Created with Custom Permissions!');
    }

    public function index()
    {
        $users = \App\Models\User::all(); // Fetches all migrated users
        return view('page.admin.users.index', compact('users'));
    }

    public function edit(\App\Models\User $user)
    {
        // Fetches modules from the DB table we created earlier
        $modules = \App\Models\Module::all(); 
        return view('page.admin.users.edit', compact('user', 'modules'));
    }

    public function update(Request $request, \App\Models\User $user)
    {
        // Updates the JSON column in the users table
        $user->update([
            'assigned_modules' => $request->modules ?? []
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User modules updated!');
    }
}