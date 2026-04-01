<?php

namespace App\Http\Controllers\supervisor_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupervisorKnowledgeBaseController extends Controller
{
    public function index()
{
    $articles = \Illuminate\Support\Facades\DB::table('knowledge_bases')
        ->select(
            'id',
            'title',
            'category',
            'content'
        )
        ->orderByDesc('id')
        ->limit(20)
        ->get();

    return view('content.supervisor.knowledge-base', compact('articles'));
}
}