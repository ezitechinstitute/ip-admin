<?php

namespace App\Http\Controllers\manager_controllers;

use App\Http\Controllers\Controller;
use App\Models\OfferLetterTemplate;
use App\Models\AdminSetting; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfferLetterTemplateController extends Controller
{
    

public function index(Request $request)
{
    // ⚙️ Fetch Pagination Limit
    $pageLimitSet = AdminSetting::first();
    $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);
    
   
    $currentManagerId = Auth::guard('manager')->user()->manager_id; 

    // 🔎 Start Query with strict conditions
    $query = OfferLetterTemplate::where('is_deleted', 0)
        ->where(function ($q) use ($currentManagerId) {
           
            $q->where('manager_id', $currentManagerId)
              ->orWhere('can_use_other_template', 1);
        });

    // 🔍 Search by Title
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where('title', 'like', "%{$search}%");
    }

    // 🔘 Status Filter
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // 📅 Latest Records
    $query->latest();

    // 📚 Pagination
    $templates = $query->paginate($perPage)->withQueryString();

    return view('pages.manager.offer-letter-template.offerLetterTemplate', compact('templates', 'perPage'));
}


    public function store(Request $request)
    {
        // 1. Validation
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|in:0,1',
        ]);

        // 2. Data Prepare
        $data = [
            'title' => $validatedData['title'],
            'content' => $validatedData['content'],
            'status' => $validatedData['status'],
            // Checkbox handle karna: agar checked hai toh 1, nahi toh 0
            'can_use_other_template' => $request->has('can_use_other_template') ? 1 : 0,
            'manager_id' => Auth::guard('manager')->user()->manager_id, 
        ];

        // 3. Save to database
        OfferLetterTemplate::create($data);

        // 4. Redirect with success message
        return redirect()->back()->with('success', 'Offer Letter Template created successfully!');
    }



    public function update(Request $request, $id)
    {
        $template = OfferLetterTemplate::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'status' => 'required',
        ]);

        $template->update([
            'title' => $request->title,
            'content' => $request->content, // DIRECT HTML UPDATE
            'can_use_other_template' => $request->has('can_use_other_template') ? 1 : 0,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Template updated successfully!');
    }




   public function destroy($id)
{
    try {
        $template = OfferLetterTemplate::findOrFail($id);
        
        // Update the flag instead of calling delete()
        $template->update(['is_deleted' => 1]);

        return redirect()->back()->with('success', 'Template removed successfully!');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Something went wrong while removing the template.');
    }
}


}