<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Models\Intern;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;


class AllInternsController extends Controller
{
    public function allInterns(Request $request)
{
    // English: Fetch pagination limit from settings
    $pageLimitSet = AdminSetting::first();
    $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);

    // English: Using select() to only fetch required columns (massive performance boost)
    $query = Intern::select('id', 'name', 'email', 'city', 'phone', 'join_date', 'technology', 'status', 'intern_type', 'interview_type', 'package', 'created_at','image');

    // Hide duplicate phone numbers, sirf latest ID show karo
$query->whereIn('id', function($sub) {
    $sub->selectRaw('MAX(id)')->from('intern_table')->groupBy('phone');
});

//remove intern from the table
      $query->where('status', '!=', 'removed');

    // 🔍 Optimized Search
    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            // English: Use prefix search (search%) instead of double wildcard (%search%) 
            // to allow MySQL to use B-Tree indexes effectively.
            $q->where('name', 'like', "{$search}%")
              ->orWhere('email', 'like', "{$search}%")
              ->orWhere('city', 'like', "{$search}%")
              ->orWhere('technology', 'like', "{$search}%");
        });
    }

    // 🔘 Status filter
    if ($request->filled('status')) {
        // English: Avoid strtolower() on columns in WHERE clause to maintain index usage
        $query->where('status', $request->status);
    }

    // 📦 Package filter
    if ($request->filled('package')) {
        $query->where('package', $request->package);
    }

    // English: Use orderBy('id', 'desc') instead of latest() for better index performance on large tables
    $query->orderBy('id', 'desc');

    // 📚 Pagination
    // English: For 2 lakh+ data, paginate() is okay but simplePaginate() is faster 
    // if you don't need exact total page numbers.
    $allInterns = $query->paginate($perPage)->withQueryString();

    return view('pages.admin.all-interns.allInterns', compact('allInterns', 'perPage'));
}

   public function interviewIntern(Request $request)
{
    $pageLimitSet = AdminSetting::first();
    $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);

    $query = Intern::select('id', 'name', 'email', 'city', 'phone', 'join_date', 'package',
                            'technology', 'interview_type', 'status', 'created_at', 'image');

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "{$search}%")
              ->orWhere('email', 'like', "{$search}%")
              ->orWhere('city', 'like', "{$search}%")
              ->orWhere('technology', 'like', "{$search}%");
        });
    }

    // ✅ Fixed status filter
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    } else {
        $query->where('status', 'interview');
    }

    $query->orderBy('id', 'desc');
    $interview = $query->paginate($perPage)->withQueryString();

    return view('pages.admin.all-interns.interview', compact('interview', 'perPage'));
}


public function removeIntern($id)
{
    $intern = Intern::findOrFail($id);
    $intern->status = 'Removed';
    $intern->save();

    return redirect()->back()->with('success', 'Intern removed successfully');
}


public function contactIntern(Request $request)
{
    $pageLimitSet = AdminSetting::first();
    $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);

    $query = Intern::select('id', 'name', 'email', 'city', 'phone', 'join_date', 'package',
                            'technology', 'interview_type', 'status', 'created_at', 'image');

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "{$search}%")
              ->orWhere('email', 'like', "{$search}%")
              ->orWhere('city', 'like', "{$search}%")
              ->orWhere('technology', 'like', "{$search}%");
        });
    }

    // ✅ Fixed status filter
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    } else {
        $query->where('status', 'contact');
    }

    $query->orderBy('id', 'desc');
    $contact = $query->paginate($perPage)->withQueryString();

    return view('pages.admin.all-interns.contactIntern', compact('contact', 'perPage'));
}

public function testIntern(Request $request)
{
    $pageLimitSet = AdminSetting::first();
    $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);

    $query = Intern::select('id', 'name', 'email', 'city', 'phone', 'join_date', 'package',
                            'technology', 'interview_type', 'status', 'created_at', 'image');

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "{$search}%")
              ->orWhere('email', 'like', "{$search}%")
              ->orWhere('city', 'like', "{$search}%")
              ->orWhere('technology', 'like', "{$search}%");
        });
    }

    // ✅ Fixed status filter
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    } else {
        $query->where('status', 'test');
    }

    $query->orderBy('id', 'desc');
    $test = $query->paginate($perPage)->withQueryString();

    return view('pages.admin.all-interns.testIntern', compact('test', 'perPage'));
}


public function completedIntern(Request $request)
{
    $pageLimitSet = AdminSetting::first();
    $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);

    $query = Intern::select('id', 'name', 'email', 'city', 'phone', 'join_date', 'package',
                            'technology', 'interview_type', 'status', 'created_at', 'image');

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "{$search}%")
              ->orWhere('email', 'like', "{$search}%")
              ->orWhere('city', 'like', "{$search}%")
              ->orWhere('technology', 'like', "{$search}%");
        });
    }

    // ✅ Fixed status filter
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    } else {
        $query->where('status', 'completed');
    }

    $query->orderBy('id', 'desc');
    $completed = $query->paginate($perPage)->withQueryString();

    return view('pages.admin.all-interns.completedIntern', compact('completed', 'perPage'));
}


public function activeIntern(Request $request)
{
    $pageLimitSet = AdminSetting::first();
    $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);

    $query = Intern::select('id', 'name', 'email', 'city', 'phone', 'join_date', 'package',
                            'technology', 'interview_type', 'status', 'created_at', 'image');
    
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "{$search}%")
              ->orWhere('email', 'like', "{$search}%")
              ->orWhere('city', 'like', "{$search}%")
              ->orWhere('technology', 'like', "{$search}%");
        });
    }

    
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    } else {
        $query->where('status', 'active');
    }

    $query->orderBy('id', 'desc');
    $active = $query->paginate($perPage)->withQueryString();

    return view('pages.admin.all-interns.activeIntern', compact('active', 'perPage'));
}

  public function viewProfileInternee($id){
    $interneeDetails = intern::where('id', $id)->first();

    // Agar package null ya empty hai to 'Training' set karo
    if(empty($interneeDetails->package)) {
    $interneeDetails->package = 'Training';
    }
    $packages = \App\Services\PackageService::getAll();  // ← ADD THIS LINE
    return view('pages.admin.all-interns.viewProfile', compact('interneeDetails', 'packages'));
}

public function updateIntern(Request $request)
{
    $request->validate([
        'id' => 'required',
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'technology' => 'required|string',
        'status' => 'required'
    ]);

    $intern = Intern::findOrFail($request->id);
    
    $intern->update([
        'name' => $request->name ?? '',
        'email' => $request->email ?? '',
        'phone' => $request->phone ?? '',
        'cnic' => $request->cnic ?? '',
        'gender' => $request->gender ?? '',
        'birth_date' => $request->birth_date ?? null,
        'country' => $request->country ?? '',
        'city' => $request->city ?? '',
        'university' => $request->university ?? '',
        'technology' => $request->technology ?? '',
        'duration' => $request->duration ?? '',
        'intern_type' => $request->intern_type ?? '',
        'status' => $request->status ?? 'active',
        'bio' => $request->bio ?? '',
    ]);
    
    // Always return JSON
    return response()->json(['success' => true, 'message' => 'Intern updated successfully']);
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
    // English: Remove execution time limit for large exports
    set_time_limit(0);
    ini_set('memory_limit', '512M');

    $fileName = 'interview_interns_' . date('d-m-Y_His') . '.csv';

    // English: Selection of specific columns to keep the cursor light
    $query = Intern::select([
        'id', 'name', 'email', 'country', 'city', 'phone', 'cnic', 
        'gender', 'birth_date', 'interview_type', 'university', 
        'technology', 'duration', 'intern_type', 'join_date', 'status'
    ]);

    // 🔍 Search Optimization
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            // English: Trailing wildcard for better index utilization
            $q->where('name', 'like', "{$search}%")
              ->orWhere('email', 'like', "{$search}%")
              ->orWhere('city', 'like', "{$search}%")
              ->orWhere('technology', 'like', "{$search}%");
        });
    }

    // 🔘 Status Filter
    $status = $request->status ?: 'interview';
    $query->where('status', $status);

    // English: Headers for streaming CSV download
    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = ['ID', 'Name', 'Email', 'Country', 'City', 'Phone', 'CNIC', 'Gender', 'DOB', 'Interview Type', 'University', 'Technology', 'Duration', 'Intern Type', 'Join Date', 'Status'];

    // English: Using a stream response to handle 270k+ records without memory issues
    $callback = function() use ($query, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        // English: cursor() fetches one row at a time instead of all at once (get())
        
        foreach ($query->orderBy('id', 'desc')->cursor() as $intern) {
            fputcsv($file, [
                $intern->id,
                $intern->name,
                $intern->email,
                $intern->country,
                $intern->city,
                " " . $intern->phone, // English: Space prevents Excel from formatting as scientific number
                " " . $intern->cnic,
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
            
            // English: Periodically clear the buffer to keep memory low
            flush();
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}




public function exportCSVContact(Request $request)
{
    // English: Disable time limit and increase memory for heavy export
    set_time_limit(0);
    ini_set('memory_limit', '512M');

    $fileName = 'contact_interns_' . date('d-m-Y_His') . '.csv';

    // English: Select only required columns to minimize data overhead
    $query = Intern::select([
        'id', 'name', 'email', 'country', 'city', 'phone', 'cnic', 
        'gender', 'birth_date', 'interview_type', 'university', 
        'technology', 'duration', 'intern_type', 'join_date', 'status'
    ]);

    // 🔍 Search Optimization
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            // English: Use prefix search for better index performance
            $q->where('name', 'like', "{$search}%")
              ->orWhere('email', 'like', "{$search}%")
              ->orWhere('city', 'like', "{$search}%")
              ->orWhere('technology', 'like', "{$search}%");
        });
    }

    // 🔘 Status Filter
    $status = $request->status ?: 'contact';
    $query->where('status', $status);

    // English: CSV Download Headers
    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = ['ID', 'Name', 'Email', 'Country', 'City', 'Phone', 'CNIC', 'Gender', 'DOB', 'Interview Type', 'University', 'Technology', 'Duration', 'Intern Type', 'Join Date', 'Status'];

    // English: Stream response to handle large datasets (270k+) without crashing
    $callback = function() use ($query, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        // English: cursor() processes one record at a time, keeping RAM usage extremely low
        
        foreach ($query->orderBy('id', 'desc')->cursor() as $intern) {
            fputcsv($file, [
                $intern->id,
                $intern->name,
                $intern->email,
                $intern->country,
                $intern->city,
                " " . $intern->phone, // Space avoids scientific notation in Excel
                " " . $intern->cnic,
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
            
            // English: Force output to browser to avoid server timeout
            flush();
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}




public function exportCSVTest(Request $request)
{
    // English: Remove time limits and increase memory for high-volume data
    set_time_limit(0);
    ini_set('memory_limit', '512M');

    $fileName = 'test_interns_' . date('d-m-Y_His') . '.csv';

    // English: Select only required columns to keep the cursor memory footprint low
    $query = Intern::select([
        'id', 'name', 'email', 'country', 'city', 'phone', 'cnic', 
        'gender', 'birth_date', 'interview_type', 'university', 
        'technology', 'duration', 'intern_type', 'join_date', 'status'
    ]);

    // 🔍 Search Optimization
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            // English: Trailing wildcard only for better index usage
            $q->where('name', 'like', "{$search}%")
              ->orWhere('email', 'like', "{$search}%")
              ->orWhere('city', 'like', "{$search}%")
              ->orWhere('technology', 'like', "{$search}%");
        });
    }

    // 🔘 Status Filter
    $status = $request->status ?: 'test';
    $query->where('status', $status);

    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = ['ID', 'Name', 'Email', 'Country', 'City', 'Phone', 'CNIC', 'Gender', 'DOB', 'Interview Type', 'University', 'Technology', 'Duration', 'Intern Type', 'Join Date', 'Status'];

    // English: Using stream response to handle 2.7 Lakh+ records efficiently
    $callback = function() use ($query, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        // English: cursor() processes one row at a time, preventing memory exhaustion
        
        foreach ($query->orderBy('id', 'desc')->cursor() as $intern) {
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
            
            // English: Periodically flush buffer to send data to browser immediately
            flush();
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}




public function exportCSVCompleted(Request $request)
{
    // English: Remove time limit and increase memory for massive data export
    set_time_limit(0);
    ini_set('memory_limit', '512M');

    $fileName = 'completed_interns_' . date('d-m-Y_His') . '.csv';

    // English: Selecting only required columns for performance
    $query = Intern::select([
        'id', 'name', 'email', 'country', 'city', 'phone', 'cnic', 
        'gender', 'birth_date', 'interview_type', 'university', 
        'technology', 'duration', 'intern_type', 'join_date', 'status'
    ]);

    // 🔍 Search Optimization
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            // English: Use prefix search for index optimization
            $q->where('name', 'like', "{$search}%")
              ->orWhere('email', 'like', "{$search}%")
              ->orWhere('city', 'like', "{$search}%")
              ->orWhere('technology', 'like', "{$search}%");
        });
    }

    // 🔘 Status Filter
    $status = $request->status ?: 'completed';
    // English: Exact case match is faster in SQL indexed columns
    $query->where('status', $status);

    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = ['ID', 'Name', 'Email', 'Country', 'City', 'Phone', 'CNIC', 'Gender', 'DOB', 'Interview Type', 'University', 'Technology', 'Duration', 'Intern Type', 'Join Date', 'Status'];

    // English: Stream response to handle large datasets efficiently
    $callback = function() use ($query, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        // English: cursor() processes records one by one, keeping memory usage minimal
        
        foreach ($query->orderBy('id', 'desc')->cursor() as $intern) {
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
            
            // English: Flush the output buffer to send data to the browser immediately
            flush();
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}



public function exportCSVActive(Request $request)
{
    // English: Essential for large scale data (270k+ records)
    set_time_limit(0);
    ini_set('memory_limit', '512M');

    $fileName = 'active_interns_' . date('d-m-Y_His') . '.csv';

    // English: Select only required columns to reduce data processing overhead
    $query = Intern::select([
        'id', 'name', 'email', 'country', 'city', 'phone', 'cnic', 
        'gender', 'birth_date', 'interview_type', 'university', 
        'technology', 'duration', 'intern_type', 'join_date', 'status'
    ]);

    // 🔍 Search Optimization
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            // English: Prefix search for index efficiency
            $q->where('name', 'like', "{$search}%")
              ->orWhere('email', 'like', "{$search}%")
              ->orWhere('city', 'like', "{$search}%")
              ->orWhere('technology', 'like', "{$search}%");
        });
    }

    // 🔘 Status Filter
    $status = $request->status ?: 'active';
    $query->where('status', $status);

    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = ['ID', 'Name', 'Email', 'Country', 'City', 'Phone', 'CNIC', 'Gender', 'DOB', 'Interview Type', 'University', 'Technology', 'Duration', 'Intern Type', 'Join Date', 'Status'];

    // English: Stream response to prevent server from running out of RAM
    $callback = function() use ($query, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        // English: cursor() is a game changer—it fetches one row at a time from DB
        
        foreach ($query->orderBy('id', 'desc')->cursor() as $intern) {
            fputcsv($file, [
                $intern->id,
                $intern->name,
                $intern->email,
                $intern->country,
                $intern->city,
                ' ' . $intern->phone, // Prevents scientific notation
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
            
            // English: Flush the buffer to send data chunks to the browser immediately
            flush();
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}




/**
 * Step 2.1: Get invoices for specific intern (AJAX call from profile page)
 * 
 * @param int $id - Intern ID from intern_table
 * @return \Illuminate\Http\JsonResponse
 */
public function getInternInvoices($id)
{
    try {
        // Find the intern
        $intern = Intern::findOrFail($id);
        
        // Get all invoices for this intern using their email
        $invoices = \App\Models\Invoice::where('intern_email', $intern->email)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'invoices' => $invoices
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

/**
 * Step 2.2: Remove intern via AJAX (from profile page remove button)
 * 
 * @param int $id - Intern ID from intern_table
 * @return \Illuminate\Http\JsonResponse
 */
public function removeInternAjax($id)
{
    try {
        $intern = Intern::findOrFail($id);
        $intern->status = 'removed';
        $intern->save();
        
        // Also update InternAccount if exists (freeze portal access)
        $internAccount = \App\Models\InternAccount::where('email', $intern->email)->first();
        if ($internAccount) {
            $internAccount->portal_status = 'frozen';
            $internAccount->save();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Intern removed successfully'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}



/**
 * Get intern payment history
 */
public function getInternPayments($id)
{
    $intern = Intern::findOrFail($id);
    $payments = \App\Models\Invoice::where('intern_email', $intern->email)
        ->where('received_amount', '>', 0)
        ->orderBy('updated_at', 'desc')
        ->get()
        ->map(function($invoice) {
            return [
                'date' => $invoice->updated_at->format('Y-m-d'),
                'inv_id' => $invoice->inv_id,
                'amount' => $invoice->received_amount,
                'method' => $invoice->payment_method ?? 'Cash',
                'received_by' => $invoice->received_by
            ];
        });
    
    return response()->json(['success' => true, 'payments' => $payments]);
}

/**
 * Get intern status (Freeze/Ongoing based on payment)
 */
public function getInternStatus($id)
{
    $intern = Intern::findOrFail($id);
    $invoices = \App\Models\Invoice::where('intern_email', $intern->email)->get();
    
    $totalAmount = $invoices->sum('total_amount');
    $totalPaid = $invoices->sum('received_amount');
    $totalRemaining = $totalAmount - $totalPaid;
    $overdueCount = $invoices->filter(function($inv) {
        return $inv->due_date && now()->greaterThan($inv->due_date) && $inv->remaining_amount > 0;
    })->count();
    
    // Portal Freeze Logic
    $isFrozen = false;
    $status = 'ongoing';
    
    foreach ($invoices as $invoice) {
        // Overdue invoice = Freeze
        if ($invoice->due_date && now()->greaterThan($invoice->due_date) && $invoice->remaining_amount > 0) {
            $isFrozen = true;
            $status = 'frozen';
            break;
        }
        // Unpaid for >30 days
        if ($invoice->remaining_amount > 0 && $invoice->created_at && now()->diffInDays($invoice->created_at) > 30) {
            $isFrozen = true;
            $status = 'frozen';
            break;
        }
        // Partial payment = Limited access
        if ($invoice->remaining_amount > 0 && $invoice->remaining_amount < $invoice->total_amount) {
            $status = 'partial';
        }
    }
    
    if ($totalRemaining == 0 && $totalAmount > 0) {
        $status = 'ongoing';
        $isFrozen = false;
    }
    
    // Update InternAccount portal_status
    $internAccount = \App\Models\InternAccount::where('email', $intern->email)->first();
    if ($internAccount) {
        $internAccount->portal_status = $isFrozen ? 'frozen' : 'active';
        $internAccount->save();
    }
    
    $percentage = $totalAmount > 0 ? round(($totalPaid / $totalAmount) * 100) : 0;
    
    return response()->json([
        'success' => true,
        'status' => $status,
        'is_frozen' => $isFrozen,
        'total_amount' => $totalAmount,
        'total_paid' => $totalPaid,
        'total_remaining' => $totalRemaining,
        'payment_percentage' => $percentage,
        'overdue_invoices' => $overdueCount
    ]);
}  


/**
 * Bulk status update for selected interns
 */
public function bulkStatusUpdate(Request $request)
{
    try {
        // CHANGE THIS: Accept comma-separated string, not JSON
        $internIds = explode(',', $request->intern_ids);
        $internIds = array_map('trim', $internIds);
        $internIds = array_filter($internIds, 'is_numeric');
        
        $newStatus = $request->new_status; // CHANGE: match form field name
        
        if (empty($internIds)) {
            // BETTER: Return redirect with toast for non-AJAX
            return redirect()->back()->with('toast_error', 'No interns selected');
        }
        
        $count = Intern::whereIn('id', $internIds)->update(['status' => $newStatus]);
        
        // BETTER: Return redirect with toast message
        return redirect()->back()->with('toast_success', $count . ' intern(s) status changed to ' . ucfirst($newStatus));
        
    } catch (\Exception $e) {
        return redirect()->back()->with('toast_error', 'Error: ' . $e->getMessage());
    }
}

/**
 * Export selected interns to CSV
 */
public function exportSelectedCSV(Request $request)
{
    $ids = explode(',', $request->ids);
    $interns = Intern::whereIn('id', $ids)->get();
    
    $fileName = 'selected_interns_' . date('Y-m-d') . '.csv';
    
    $headers = [
        "Content-type" => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
    ];
    
    $callback = function() use ($interns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, ['ID', 'Name', 'Email', 'Phone', 'City', 'Technology', 'Status']);
        foreach ($interns as $intern) {
            fputcsv($file, [
                $intern->id, $intern->name, $intern->email, 
                $intern->phone ?? 'N/A', $intern->city ?? 'N/A',
                $intern->technology, $intern->status
            ]);
        }
        fclose($file);
    };
    
    return response()->stream($callback, 200, $headers);
}

}
