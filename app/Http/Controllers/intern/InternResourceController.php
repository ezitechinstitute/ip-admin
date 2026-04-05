<?php

namespace App\Http\Controllers\intern;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InternResourceController extends Controller
{
    public function index(Request $request)
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }
        
        $query = DB::table('knowledge_bases')
            ->where('status', 'active');
        
        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('content', 'LIKE', "%{$search}%");
            });
        }
        
        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        $resources = $query->orderBy('created_at', 'desc')->paginate(12);
        
        // Get unique categories for filter
        $categories = DB::table('knowledge_bases')
            ->where('status', 'active')
            ->select('category')
            ->distinct()
            ->pluck('category');
        
        return view('pages.intern.resources.index', compact('resources', 'categories'));
    }
    
    public function show($id)
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }
        
        $resource = DB::table('knowledge_bases')
            ->where('id', $id)
            ->where('status', 'active')
            ->first();
        
        if (!$resource) {
            abort(404, 'Resource not found');
        }
        
        // Increment view count (if views column exists)
        // DB::table('knowledge_bases')->where('id', $id)->increment('views');
        
        return view('pages.intern.resources.show', compact('resource'));
    }
}