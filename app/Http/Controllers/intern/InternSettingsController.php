<?php

namespace App\Http\Controllers\intern;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
    
    public function update(Request $request)
    {
        $intern = Auth::guard('intern')->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:100',
            'university' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
        ]);
        
        DB::table('intern_accounts')
            ->where('int_id', $intern->int_id)
            ->update([
                'name' => $validated['name'],
                'phone' => $validated['phone'] ?? $intern->phone,
                'city' => $validated['city'] ?? null,
                'university' => $validated['university'] ?? null,
                'bio' => $validated['bio'] ?? null,
                'updated_at' => now(),
            ]);
        
        return redirect()->route('intern.settings')
            ->with('success', 'Settings updated successfully!');
    }
}