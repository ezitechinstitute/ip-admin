<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Models\Intern;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;


class AllInternsController extends Controller
{
    public function allInterns(Request $request){
        $pageLimitSet = AdminSetting::first();
        $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);

    $query = Intern::query();

    // 🔍 Search
    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('city', 'like', "%{$search}%")
              ->orWhere('technology', 'like', "%{$search}%");
        });
    }

    // 🔘 Status filter with default 'interview'
    $status = $request->status; // raw status from request

   
    if(!empty($status)){
        $query->where('status', strtolower($status));
    }
    
    //get latest record
    $query->latest();
    
    $allInterns = $query->paginate($perPage)->withQueryString();

    return view('pages.admin.all-interns.allInterns', compact('allInterns', 'perPage', 'status'));
    }
















    public function interviewIntern(Request $request)
{
    $pageLimitSet = AdminSetting::first();
        $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);

    $query = Intern::query();

    // 🔍 Search
    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('city', 'like', "%{$search}%")
              ->orWhere('technology', 'like', "%{$search}%");
        });
    }

    // 🔘 Status filter with default 'interview'
    $status = $request->status; // raw status from request

    if (empty($status)) {
        $status = 'interview'; // default
    }

    $query->where('status', strtolower($status));
    //get latest record
    $query->latest();
    // 🔢 Pagination
    $interview = $query->paginate($perPage)->withQueryString();

    return view('pages.admin.all-interns.interview', compact('interview', 'perPage', 'status'));
}


public function removeIntern($id)
{
    $intern = Intern::findOrFail($id);
    $intern->status = 'Removed';
    $intern->save();

    return redirect()->back()->with('success', 'Intern removed successfully');
}


public function contactIntern(Request $request){

    $pageLimitSet = AdminSetting::first();
        $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);

    $query = intern::query();

    if($request->filled('search')){
        $search = $request->search;
        $query->where(function ($q) use ($search){
            $q->where('name', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->orWhere('city', 'like', "%{$search}%")
            ->orWhere('technology', 'like', "%{$search}%");
        });
    }

    $status = $request->status;
    if(empty($status)){
        $status = 'contact';
    }

    $query->where('status', strtolower($status));
    //get latest record
    $query->latest();
    $contact = $query->paginate($perPage)->withQueryString();

    return view('pages.admin.all-interns.contactIntern', compact('contact', 'perPage', 'status'));
}


public function testIntern(Request $request){

    $pageLimitSet = AdminSetting::first();
        $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);

    $query = intern::query();

    if($request->filled('search')){
        $search = $request->search;
        $query->where(function ($q) use ($search){
            $q->where('name', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->orWhere('city', 'like', "%{$search}%")
            ->orWhere('technology', 'like', "%{$search}%");
        });
    }

    $status = $request->status;
    if(empty($status)){
        $status = 'test';
    }

    $query->where('status', strtolower($status));
    //get latest record
    $query->latest();
    $test = $query->paginate($perPage)->withQueryString();

    return view('pages.admin.all-interns.testIntern', compact('test', 'perPage', 'status'));
}



public function completedIntern(Request $request){

    $pageLimitSet = AdminSetting::first();
        $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);

    $query = intern::query();

    if($request->filled('search')){
        $search = $request->search;
        $query->where(function ($q) use ($search){
            $q->where('name', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->orWhere('city', 'like', "%{$search}%")
            ->orWhere('technology', 'like', "%{$search}%");
        });
    }

    $status = $request->status;
    if(empty($status)){
        $status = 'completed';
    }

    $query->where('status', strtolower($status));
    //get latest record
    $query->latest();
    $completed = $query->paginate($perPage)->withQueryString();

    return view('pages.admin.all-interns.completedIntern', compact('completed', 'perPage', 'status'));
}



public function activeIntern(Request $request){

    $pageLimitSet = AdminSetting::first();
        $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);

    $query = intern::query();

    if($request->filled('search')){
        $search = $request->search;
        $query->where(function ($q) use ($search){
            $q->where('name', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->orWhere('city', 'like', "%{$search}%")
            ->orWhere('technology', 'like', "%{$search}%");
        });
    }

    $status = $request->status;
    if(empty($status)){
        $status = 'active';
    }

    $query->where('status', strtolower($status));
    //get latest record
    $query->latest();
    $active = $query->paginate($perPage)->withQueryString();

    return view('pages.admin.all-interns.activeIntern', compact('active', 'perPage', 'status'));
}

    public function viewProfileInternee($id){
        $interneeDetails = intern::where('id', $id)->first();
        return view('pages.admin.all-interns.viewProfile', compact('interneeDetails'));
    }


    public function updateIntern(Request $request){
        $request->validate([
        'id' => 'required',
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'technology' => 'required|string',
        'status' => 'required'
    ]);

    intern::where('id', $request->id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'technology' => $request->technology,
            'status' => $request->status
    ]);
        
        return redirect()->back()->with('success', 'Intern updated successfully!');
    }




    public function exportCSVAllInterns(Request $request)
{
    // English comments: Set infinite execution time and high memory for large exports
    set_time_limit(0); 
    ini_set('memory_limit', '1024M');

    $fileName = 'all_interns_data_' . date('d-m-Y_His') . '.csv';

    // 1. Query build karein (Lekin get() nahi karna)
    $query = Intern::query();

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('city', 'like', "%{$search}%")
              ->orWhere('technology', 'like', "%{$search}%");
        });
    }

    if ($request->filled('status')) {
        $query->where('status', strtolower($request->status));
    }

    // 2. CSV Headers
    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = ['ID', 'Name', 'Email', 'Country', 'City', 'Phone', 'CNIC', 'Gender', 'DOB', 'University', 'Technology', 'Join Date', 'Status'];

    // 3. Streaming Callback
    $callback = function() use ($query, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        // English comments: cursor() is the key change. It fetches 1 row at a time.
        foreach ($query->latest()->cursor() as $intern) {
            fputcsv($file, [
                $intern->id,
                $intern->name,
                $intern->email,
                $intern->country,
                $intern->city,
                ' ' . $intern->phone, // Force string for Excel
                ' ' . $intern->cnic,
                $intern->gender,
                $intern->birth_date,
                $intern->university,
                $intern->technology,
                $intern->join_date ? date('Y-m-d', strtotime($intern->join_date)) : '',
                ucfirst($intern->status),
            ]);
            
            // English comments: Every 1000 rows, flush the buffer to keep memory low
            // Flush helps sending data to the browser in chunks
            if (connection_aborted()) {
                break;
            }
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}



public function exportCSVInterview(Request $request)
{
    $fileName = 'interview_interns_' . date('d-m-Y_His') . '.csv';

    $query = Intern::query();

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('city', 'like', "%{$search}%")
              ->orWhere('technology', 'like', "%{$search}%");
        });
    }

    $status = $request->status ?: 'interview';
    $query->where('status', strtolower($status));

    $interns = $query->latest()->get();

    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = ['ID', 'Name', 'Email', 'Country', 'City', 'Phone', 'CNIC', 'Gender', 'DOB', 'Interview Type', 'University', 'Technology', 'Duration', 'Intern Type', 'Join Date', 'Status'];

    $callback = function() use ($interns, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($interns as $intern) {
            fputcsv($file, [
                $intern->id,
                $intern->name,
                $intern->email,
                $intern->country,
                $intern->city,
                ' ' . $intern->phone, 
                ' ' . $intern->cnic,
                $intern->gender,
                $intern->birth_date,
                $intern->interview_type,
                $intern->university,
                $intern->technology,
                $intern->duration,
                $intern->intern_type,
                $intern->join_date ? date('Y-m-d', strtotime($intern->join_date)) : '',
                ucfirst($intern->status),
            ]);
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}




public function exportCSVContact(Request $request)
{
    $fileName = 'contact_interns_' . date('d-m-Y_His') . '.csv';

    $query = Intern::query();

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('city', 'like', "%{$search}%")
              ->orWhere('technology', 'like', "%{$search}%");
        });
    }

    $status = $request->status ?: 'contact';
    $query->where('status', strtolower($status));

    $interns = $query->latest()->get();

    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = ['ID', 'Name', 'Email', 'Country', 'City', 'Phone', 'CNIC', 'Gender', 'DOB', 'Interview Type', 'University', 'Technology', 'Duration', 'Intern Type', 'Join Date', 'Status'];

    $callback = function() use ($interns, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($interns as $intern) {
            fputcsv($file, [
                $intern->id,
                $intern->name,
                $intern->email,
                $intern->country,
                $intern->city,
                ' ' . $intern->phone, 
                ' ' . $intern->cnic,
                $intern->gender,
                $intern->birth_date,
                $intern->interview_type,
                $intern->university,
                $intern->technology,
                $intern->duration,
                $intern->intern_type,
                $intern->join_date ? date('Y-m-d', strtotime($intern->join_date)) : '',
                ucfirst($intern->status),
            ]);
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}




public function exportCSVTest(Request $request)
{
    $fileName = 'test_interns_' . date('d-m-Y_His') . '.csv';

    $query = Intern::query();

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('city', 'like', "%{$search}%")
              ->orWhere('technology', 'like', "%{$search}%");
        });
    }

    $status = $request->status ?: 'test';
    $query->where('status', strtolower($status));

    $interns = $query->latest()->get();

    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = ['ID', 'Name', 'Email', 'Country', 'City', 'Phone', 'CNIC', 'Gender', 'DOB', 'Interview Type', 'University', 'Technology', 'Duration', 'Intern Type', 'Join Date', 'Status'];

    $callback = function() use ($interns, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($interns as $intern) {
            fputcsv($file, [
                $intern->id,
                $intern->name,
                $intern->email,
                $intern->country,
                $intern->city,
                ' ' . $intern->phone, 
                ' ' . $intern->cnic,
                $intern->gender,
                $intern->birth_date,
                $intern->interview_type,
                $intern->university,
                $intern->technology,
                $intern->duration,
                $intern->intern_type,
                $intern->join_date ? date('Y-m-d', strtotime($intern->join_date)) : '',
                ucfirst($intern->status),
            ]);
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}




public function exportCSVCompleted(Request $request)
{
    $fileName = 'completed_interns_' . date('d-m-Y_His') . '.csv';

    $query = Intern::query();

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('city', 'like', "%{$search}%")
              ->orWhere('technology', 'like', "%{$search}%");
        });
    }

    $status = $request->status ?: 'completed';
    $query->where('status', strtolower($status));

    $interns = $query->latest()->get();

    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = ['ID', 'Name', 'Email', 'Country', 'City', 'Phone', 'CNIC', 'Gender', 'DOB', 'Interview Type', 'University', 'Technology', 'Duration', 'Intern Type', 'Join Date', 'Status'];

    $callback = function() use ($interns, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($interns as $intern) {
            fputcsv($file, [
                $intern->id,
                $intern->name,
                $intern->email,
                $intern->country,
                $intern->city,
                ' ' . $intern->phone, 
                ' ' . $intern->cnic,
                $intern->gender,
                $intern->birth_date,
                $intern->interview_type,
                $intern->university,
                $intern->technology,
                $intern->duration,
                $intern->intern_type,
                $intern->join_date ? date('Y-m-d', strtotime($intern->join_date)) : '',
                ucfirst($intern->status),
            ]);
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}



public function exportCSVActive(Request $request)
{
    $fileName = 'active_interns_' . date('d-m-Y_His') . '.csv';

    $query = Intern::query();

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('city', 'like', "%{$search}%")
              ->orWhere('technology', 'like', "%{$search}%");
        });
    }

    $status = $request->status ?: 'active';
    $query->where('status', strtolower($status));

    $interns = $query->latest()->get();

    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = ['ID', 'Name', 'Email', 'Country', 'City', 'Phone', 'CNIC', 'Gender', 'DOB', 'Interview Type', 'University', 'Technology', 'Duration', 'Intern Type', 'Join Date', 'Status'];

    $callback = function() use ($interns, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($interns as $intern) {
            fputcsv($file, [
                $intern->id,
                $intern->name,
                $intern->email,
                $intern->country,
                $intern->city,
                ' ' . $intern->phone, 
                ' ' . $intern->cnic,
                $intern->gender,
                $intern->birth_date,
                $intern->interview_type,
                $intern->university,
                $intern->technology,
                $intern->duration,
                $intern->intern_type,
                $intern->join_date ? date('Y-m-d', strtotime($intern->join_date)) : '',
                ucfirst($intern->status),
            ]);
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
}
