<?php

namespace App\Http\Controllers\intern;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InternProjectController extends Controller
{
    public function index()
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }
        
        $projects = DB::table('intern_projects')
            ->where('eti_id', $intern->eti_id)
            ->orderBy('createdat', 'desc')
            ->paginate(10);
        
        return view('pages.intern.projects.index', compact('projects'));
    }
    
    public function show($id)
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }
        
        $project = DB::table('intern_projects')
            ->where('project_id', $id)
            ->where('eti_id', $intern->eti_id)
            ->first();
        
        if (!$project) {
            abort(404);
        }
        
        return view('pages.intern.projects.show', compact('project'));
    }
}