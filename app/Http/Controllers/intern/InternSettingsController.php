<?php

namespace App\Http\Controllers\intern;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InternSettingsController extends Controller
{
    public function index()
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }
        
        return view('pages.intern.settings.index', compact('intern'));
    }
    
    public function updateSettings(Request $request)
    {
        $intern = Auth::guard('intern')->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:100',
            'university' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
        ]);
        
        $updateData = [
            'name' => $validated['name'],
            'updated_at' => now(),
        ];
        
        if (Schema::hasColumn('intern_accounts', 'phone')) {
            $updateData['phone'] = $validated['phone'] ?? $intern->phone;
        }
        
        if (Schema::hasColumn('intern_accounts', 'city')) {
            $updateData['city'] = $validated['city'] ?? null;
        }
        
        if (Schema::hasColumn('intern_accounts', 'university')) {
            $updateData['university'] = $validated['university'] ?? null;
        }
        
        if (Schema::hasColumn('intern_accounts', 'bio')) {
            $updateData['bio'] = $validated['bio'] ?? null;
        }
        
        DB::table('intern_accounts')
            ->where('int_id', $intern->int_id)
            ->update($updateData);
        
        return redirect()->route('intern.settings')
            ->with('success', 'Settings updated successfully!');
    }
    
    public function updatePassword(Request $request)
    {
        $intern = Auth::guard('intern')->user();
        
        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);
        
        // Verify current password (plain text comparison)
        if ($validated['current_password'] != $intern->password) {
            return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect']);
        }
        
        // Update password (store as plain text)
        DB::table('intern_accounts')
            ->where('int_id', $intern->int_id)
            ->update([
                'password' => $validated['new_password'],
                'updated_at' => now(),
            ]);
        
        return redirect()->route('intern.settings')
            ->with('success', 'Password updated successfully!');
    }
}