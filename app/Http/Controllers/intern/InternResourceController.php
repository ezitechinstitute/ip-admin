<?php

namespace App\Http\Controllers\intern;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InternResourceController extends Controller
{
    /**
     * Display list of learning resources with filters and progress tracking
     */
    public function index(Request $request)
    {
        try {
            $intern = Auth::guard('intern')->user();
            
            if (!$intern) {
                return redirect()->route('login');
            }
            
            $query = DB::table('knowledge_bases')
                ->where('status', 'active')
                ->where(function($q) {
                    $q->whereJsonContains('visibility', 'all')
                      ->orWhereJsonContains('visibility', 'interns');
                });
            
            // Search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%")
                      ->orWhere('content', 'LIKE', "%{$search}%");
                });
            }
            
            // Category filter
            if ($request->filled('category') && $request->category != 'all') {
                $query->where('category', $request->category);
            }
            
            $resources = $query->orderBy('is_featured', 'desc')
                              ->orderBy('order_position', 'asc')
                              ->orderBy('created_at', 'desc')
                              ->paginate(12);
            
            // Add helper attributes to each resource
            foreach ($resources as $resource) {
                $resource->categoryName = $this->getCategoryName($resource->category);
                $resource->categoryIcon = $this->getCategoryIcon($resource->category);
                $resource->categoryColor = $this->getCategoryColor($resource->category);
            }
            
            // Get categories with counts
            $categoriesData = DB::table('knowledge_bases')
                ->where('status', 'active')
                ->select('category', DB::raw('count(*) as total'))
                ->groupBy('category')
                ->get();
            
            $categories = [];
            $categoryNames = [
                'internship_rules' => 'Internship Rules',
                'coding_standards' => 'Coding Standards',
                'learning_material' => 'Learning Materials',
                'guide' => 'Guides & Tutorials',
                'video_tutorial' => 'Video Tutorials',
                'documentation' => 'Documentation'
            ];
            $categoryIcons = [
                'internship_rules' => 'ti ti-file-description',
                'coding_standards' => 'ti ti-code',
                'learning_material' => 'ti ti-book',
                'guide' => 'ti ti-file-text',
                'video_tutorial' => 'ti ti-video',
                'documentation' => 'ti ti-file-pdf'
            ];
            
            foreach ($categoriesData as $cat) {
                $categories[$cat->category] = [
                    'name' => $categoryNames[$cat->category] ?? ucfirst(str_replace('_', ' ', $cat->category)),
                    'count' => $cat->total,
                    'icon' => $categoryIcons[$cat->category] ?? 'ti ti-folder'
                ];
            }
            
            // Get featured resources
            $featured = DB::table('knowledge_bases')
                ->where('status', 'active')
                ->where('is_featured', 1)
                ->where(function($q) {
                    $q->whereJsonContains('visibility', 'all')
                      ->orWhereJsonContains('visibility', 'interns');
                })
                ->orderBy('order_position', 'asc')
                ->limit(4)
                ->get();
            
            // Add helper attributes to featured resources
            foreach ($featured as $resource) {
                $resource->categoryName = $this->getCategoryName($resource->category);
                $resource->categoryIcon = $this->getCategoryIcon($resource->category);
                $resource->categoryColor = $this->getCategoryColor($resource->category);
            }
            
            // FIXED: Use int_id instead of id
            $completedResourceIds = DB::table('intern_resource_progress')
                ->where('intern_id', $intern->int_id)
                ->where('is_completed', 1)
                ->pluck('resource_id')
                ->toArray();
            
            // Calculate completion percentage
            $totalResources = DB::table('knowledge_bases')
                ->where('status', 'active')
                ->count();
            $completedCount = count($completedResourceIds);
            $completionPercentage = $totalResources > 0 ? round(($completedCount / $totalResources) * 100) : 0;
            
            return view('pages.intern.resources.index', compact(
                'resources', 'categories', 'featured', 
                'completedResourceIds', 'completionPercentage'
            ));
            
        } catch (\Exception $e) {
            $resources = collect([]);
            $categories = [];
            $featured = collect([]);
            $completedResourceIds = [];
            $completionPercentage = 0;
            
            return view('pages.intern.resources.index', compact(
                'resources', 'categories', 'featured', 
                'completedResourceIds', 'completionPercentage'
            ))->with('error', 'Unable to load resources. Please try again.');
        }
    }
    
    /**
     * Display single resource details
     */
    public function show($id)
    {
        try {
            $intern = Auth::guard('intern')->user();
            
            if (!$intern) {
                return redirect()->route('login');
            }
            
            $resource = DB::table('knowledge_bases')
                ->where('id', $id)
                ->where('status', 'active')
                ->where(function($q) {
                    $q->whereJsonContains('visibility', 'all')
                      ->orWhereJsonContains('visibility', 'interns');
                })
                ->first();
            
            if (!$resource) {
                abort(404, 'Resource not found');
            }
            
            // Add helper attributes to resource
            $resource->categoryName = $this->getCategoryName($resource->category);
            $resource->categoryIcon = $this->getCategoryIcon($resource->category);
            $resource->categoryColor = $this->getCategoryColor($resource->category);
            
            // Increment view count
            DB::table('knowledge_bases')->where('id', $id)->increment('views');
            
            // FIXED: Use int_id instead of id
            $progress = DB::table('intern_resource_progress')
                ->where('intern_id', $intern->int_id)
                ->where('resource_id', $id)
                ->first();
            
            if (!$progress) {
                $progressId = DB::table('intern_resource_progress')->insertGetId([
                    'intern_id' => $intern->int_id,
                    'resource_id' => $id,
                    'is_completed' => 0,
                    'time_spent' => 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $progress = DB::table('intern_resource_progress')->where('id', $progressId)->first();
            }
            
            // Get related resources (same category)
            $relatedResources = DB::table('knowledge_bases')
                ->where('status', 'active')
                ->where('category', $resource->category)
                ->where('id', '!=', $id)
                ->where(function($q) {
                    $q->whereJsonContains('visibility', 'all')
                      ->orWhereJsonContains('visibility', 'interns');
                })
                ->limit(4)
                ->get();
            
            // Add helper attributes to related resources
            foreach ($relatedResources as $related) {
                $related->categoryName = $this->getCategoryName($related->category);
                $related->categoryIcon = $this->getCategoryIcon($related->category);
                $related->categoryColor = $this->getCategoryColor($related->category);
            }
            
            return view('pages.intern.resources.show', compact('resource', 'progress', 'relatedResources'));
            
        } catch (\Exception $e) {
            abort(404, 'Resource not found');
        }
    }
    
    /**
     * Mark resource as completed
     */
    public function markComplete(Request $request, $id)
    {
        try {
            $intern = Auth::guard('intern')->user();
            
            if (!$intern) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Session expired. Please login again.'], 401);
                }
                return redirect()->route('login');
            }
            
            // FIXED: Use int_id instead of id
            $internId = $intern->int_id;
            
            if (!$internId) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Invalid intern ID.'], 400);
                }
                return back()->with('error', 'Invalid intern ID.');
            }
            
            // Check if resource exists
            $resource = DB::table('knowledge_bases')->where('id', $id)->first();
            if (!$resource) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Resource not found'], 404);
                }
                return back()->with('error', 'Resource not found.');
            }
            
            // Check if progress already exists - FIXED: Use $internId
            $existing = DB::table('intern_resource_progress')
                ->where('intern_id', $internId)
                ->where('resource_id', $id)
                ->first();
            
            if ($existing) {
                if (!$existing->is_completed) {
                    DB::table('intern_resource_progress')
                        ->where('id', $existing->id)
                        ->update([
                            'is_completed' => 1,
                            'completed_at' => now(),
                            'updated_at' => now()
                        ]);
                }
            } else {
                // Create new progress record - FIXED: Use $internId
                DB::table('intern_resource_progress')->insert([
                    'intern_id' => $internId,
                    'resource_id' => $id,
                    'is_completed' => 1,
                    'completed_at' => now(),
                    'time_spent' => 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Resource marked as completed!'
                ]);
            }
            
            return back()->with('success', 'Resource marked as completed!');
            
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Failed to mark as completed: ' . $e->getMessage());
        }
    }
    
    /**
     * Download resource file
     */
    public function download($id)
    {
        try {
            $intern = Auth::guard('intern')->user();
            
            if (!$intern) {
                return redirect()->route('login');
            }
            
            $resource = DB::table('knowledge_bases')
                ->where('id', $id)
                ->where('status', 'active')
                ->first();
            
            if (!$resource) {
                return back()->with('error', 'Resource not found.');
            }
            
            if (!$resource->file_path) {
                return back()->with('error', 'No file attached to this resource.');
            }
            
            // Check if file exists in storage
            if (!Storage::disk('public')->exists($resource->file_path)) {
                return back()->with('error', 'File not found.');
            }
            
            // Increment download count
            DB::table('knowledge_bases')->where('id', $id)->increment('downloads');
            
            return Storage::disk('public')->download($resource->file_path, $resource->title . '.pdf');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Unable to download file.');
        }
    }
    
    /**
     * Get category display name
     */
    private function getCategoryName($category)
    {
        $names = [
            'internship_rules' => 'Internship Rules',
            'coding_standards' => 'Coding Standards',
            'learning_material' => 'Learning Materials',
            'guide' => 'Guides & Tutorials',
            'video_tutorial' => 'Video Tutorials',
            'documentation' => 'Documentation'
        ];
        return $names[$category] ?? ucfirst(str_replace('_', ' ', $category));
    }
    
    /**
     * Get category icon class
     */
    private function getCategoryIcon($category)
    {
        $icons = [
            'internship_rules' => 'ti ti-file-description',
            'coding_standards' => 'ti ti-code',
            'learning_material' => 'ti ti-book',
            'guide' => 'ti ti-file-text',
            'video_tutorial' => 'ti ti-video',
            'documentation' => 'ti ti-file-pdf'
        ];
        return $icons[$category] ?? 'ti ti-folder';
    }
    
    /**
     * Get category color
     */
    private function getCategoryColor($category)
    {
        $colors = [
            'internship_rules' => 'primary',
            'coding_standards' => 'info',
            'learning_material' => 'success',
            'guide' => 'warning',
            'video_tutorial' => 'danger',
            'documentation' => 'secondary'
        ];
        return $colors[$category] ?? 'dark';
    }
}