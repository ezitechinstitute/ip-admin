<?php

namespace App\Http\Controllers\intern;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InternResourceController extends Controller
{
    public function index()
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }
        
        $resources = DB::table('knowledge_bases')
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('pages.intern.resources.index', compact('resources'));
    }
}