<?php

namespace App\Http\Controllers\intern;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InternTaskController extends Controller
{
    public function index()
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }
        
        $tasks = DB::table('intern_tasks')
            ->where('eti_id', $intern->eti_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('pages.intern.tasks.index', compact('tasks'));
    }
    
    public function show($id)
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }
        
        $task = DB::table('intern_tasks')
            ->where('task_id', $id)
            ->where('eti_id', $intern->eti_id)
            ->first();
        
        if (!$task) {
            abort(404);
        }
        
        return view('pages.intern.tasks.show', compact('task'));
    }
}