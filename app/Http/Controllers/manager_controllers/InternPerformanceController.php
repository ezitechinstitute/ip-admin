<?php

namespace App\Http\Controllers\manager_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InternPerformanceAnalytics;

class InternPerformanceController extends Controller
{
    // =====================================
    // LIST PAGE (ALL INTERNS)
    // =====================================
    public function index()
    {
        $data = InternPerformanceAnalytics::orderBy('id', 'desc')->get();

        return view('pages.manager.performance.index', compact('data'));
    }

    // =====================================
    // DETAIL PAGE (SINGLE INTERN)
    // =====================================
    public function show($id)
    {
        $intern = InternPerformanceAnalytics::findOrFail($id);

        return view('pages.manager.performance.detail', compact('intern'));
    }
}