<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Models\Technologies;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TechnologyController extends Controller
{
    public function technologyData(Request $request){

       $pageLimitSet = AdminSetting::first();
        $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);

    $query = Technologies::query();

    // 🔍 Search
    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('technology', 'like', "%{$search}%");
        });
    }

    // 🔘 Status filter with default 'interview'
    $status = $request->status; // raw status from request

   
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }
    //get latest record
    $query->latest();
    
    $allTechnologies = $query->paginate($perPage)->withQueryString();

        return view('pages.admin.technology.technology', compact('allTechnologies', 'perPage'));
    }

    public function addTechnology(Request $request){
        $request->validate(['technology'=> 'required|unique:technologies,technology', 'status'=>'required|in:0,1']);

        Technologies::create([
            'technology' => $request->technology,
            'status' => $request->status
        ]);


        return back()->with('success', 'Technology added successfully!');
    }
    public function editTechnology(Request $request)
{
    $request->validate([
        'id' => 'required|exists:technologies,tech_id',
        'technology' => 'required|unique:technologies,technology,' . $request->id . ',tech_id',
        'status' => 'required|in:0,1',
    ]);

    Technologies::where('tech_id', $request->id)->update([
        'technology' => $request->technology,
        'status' => $request->status,
    ]);

    return back()->with('success', 'Technology updated successfully!');
}

    public function activeTechnologies(Request $request){
        $technologiesActive = Technologies::where('status', 1)->get();
        return response()->json([
        'success' => true,
        'data' => $technologiesActive
    ]);
    }


public function downloadTechnologiesCSV(Request $request)
{
    $query = Technologies::query();

    // Apply the same filters used in the table view
    if ($request->filled('search')) {
        $query->where('technology', 'like', "%{$request->search}%");
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    $technologies = $query->latest()->get();

    $fileName = 'technologies_export_' . date('Y-m-d') . '.csv';
    
    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = ['#ID', 'Technology Name', 'Status'];

    $callback = function() use($technologies, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($technologies as $tech) {
            fputcsv($file, [
                $tech->tech_id,
                $tech->technology,
                $tech->status == 1 ? 'Active' : 'Freeze'
            ]);
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
}
