<?php

namespace App\Http\Controllers\intern;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class InternProfileController extends Controller
{
    public function index()
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }
        
        $profileImage = Helpers::getProfileImage($intern);
        
        // Get statistics for profile
        $stats = $this->getProfileStats($intern->eti_id);
        
        // Get skills
        $skills = $this->getInternSkills($intern->int_id);
        
        return view('pages.intern.profile.index', compact('intern', 'profileImage', 'stats', 'skills'));
    }
    
    public function edit()
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }
        
        $profileImage = Helpers::getProfileImage($intern);
        $skills = $this->getInternSkills($intern->int_id);
        
        return view('pages.intern.profile.edit', compact('intern', 'profileImage', 'skills'));
    }
    
    public function update(Request $request)
    {
        $intern = Auth::guard('intern')->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:100',
            'university' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'github' => 'nullable|string|max:255',
            'linkedin' => 'nullable|string|max:255',
            'portfolio_url' => 'nullable|string|max:255',
            'skills' => 'nullable', // JSON string from hidden field
        ]);
        
        // Prepare update data - only update fields that exist
        $updateData = [
            'name' => $validated['name'],
        ];
        
        // Check each column before updating
        if (Schema::hasColumn('intern_accounts', 'phone')) {
            $updateData['phone'] = $validated['phone'] ?? $intern->phone;
        }
        
        if (Schema::hasColumn('intern_accounts', 'city')) {
            $updateData['city'] = $validated['city'] ?? null;
        }
        
        if (Schema::hasColumn('intern_accounts', 'university')) {
            $updateData['university'] = $validated['university'] ?? null;
        }
        
        if (Schema::hasColumn('intern_accounts', 'bio')) {
            $updateData['bio'] = $validated['bio'] ?? null;
        }
        
        if (Schema::hasColumn('intern_accounts', 'github')) {
            $updateData['github'] = $validated['github'] ?? null;
        }
        
        if (Schema::hasColumn('intern_accounts', 'linkedin')) {
            $updateData['linkedin'] = $validated['linkedin'] ?? null;
        }
        
        if (Schema::hasColumn('intern_accounts', 'portfolio_url')) {
            $updateData['portfolio_url'] = $validated['portfolio_url'] ?? null;
        }
        
        if (Schema::hasColumn('intern_accounts', 'updated_at')) {
            $updateData['updated_at'] = now();
        }
        
        // Update basic info
        DB::table('intern_accounts')
            ->where('int_id', $intern->int_id)
            ->update($updateData);
        
        // Update skills if table exists
        if (Schema::hasTable('intern_skills')) {
            
            $skillsArray = [];
            
            if ($request->filled('skills')) {
                $decoded = json_decode($request->skills, true);
                
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    // Filter out any email addresses from skills
                    $skillsArray = array_filter($decoded, function($skill) {
                        return !filter_var($skill, FILTER_VALIDATE_EMAIL);
                    });
                }
            }
            
            DB::beginTransaction();
            
            try {
                // Delete old skills
                DB::table('intern_skills')
                    ->where('intern_id', $intern->int_id)
                    ->delete();
                
                // Insert new skills
                foreach ($skillsArray as $skill) {
                    $skill = trim($skill);
                    
                    if (!empty($skill)) {
                        DB::table('intern_skills')->insert([
                            'intern_id' => $intern->int_id,
                            'skill' => $skill,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
                
                DB::commit();
                
            } catch (\Exception $e) {
                DB::rollBack();
                
                return back()->with('error', 'Failed to update skills: ' . $e->getMessage());
            }
        }
        
        return redirect()->route('intern.profile')
            ->with('success', 'Profile updated successfully!');
    }
    
    public function updateProfileImage(Request $request)
    {
        $intern = Auth::guard('intern')->user();
        
        $request->validate([
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        
        if ($request->hasFile('profile_image') && Schema::hasColumn('intern_accounts', 'image')) {
            $image = $request->file('profile_image');
            $imageName = time() . '_' . $intern->int_id . '.' . $image->getClientOriginalExtension();
            
            // Store in public/storage/uploads/interns
            $path = $image->storeAs('uploads/interns', $imageName, 'public');
            $imagePath = 'storage/' . $path;
            
            // Delete old image if exists and not default
            if ($intern->image && !str_contains($intern->image, 'ezitech.png')) {
                $oldPath = str_replace('storage/', '', $intern->image);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            
            $updateData = ['image' => $imagePath];
            if (Schema::hasColumn('intern_accounts', 'updated_at')) {
                $updateData['updated_at'] = now();
            }
            
            DB::table('intern_accounts')
                ->where('int_id', $intern->int_id)
                ->update($updateData);
            
            return redirect()->back()->with('success', 'Profile image updated successfully!');
        }
        
        return redirect()->back()->with('error', 'Failed to upload image.');
    }
    
    public function updatePassword(Request $request)
    {
        $intern = Auth::guard('intern')->user();
        
        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);
        
        // Verify current password
        if (!Hash::check($validated['current_password'], $intern->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect']);
        }
        
        // Update password
        $updateData = [
            'password' => Hash::make($validated['new_password']),
        ];
        
        if (Schema::hasColumn('intern_accounts', 'updated_at')) {
            $updateData['updated_at'] = now();
        }
        
        DB::table('intern_accounts')
            ->where('int_id', $intern->int_id)
            ->update($updateData);
        
        return redirect()->back()->with('success', 'Password updated successfully!');
    }
    
    public function publicProfile($identifier = null)
    {
        if ($identifier) {
            // Try to find by eti_id or name
            $intern = DB::table('intern_accounts')
                ->where('eti_id', $identifier)
                ->orWhere('name', 'LIKE', "%{$identifier}%")
                ->first();
        } else {
            $intern = Auth::guard('intern')->user();
        }
        
        if (!$intern) {
            abort(404, 'Intern profile not found');
        }
        
        $profileImage = Helpers::getProfileImage($intern);
        $stats = $this->getProfileStats($intern->eti_id);
        $skills = $this->getInternSkills($intern->int_id);
        
        // Get completed projects - with safe ordering
        $projects = collect([]);
        if (Schema::hasTable('intern_projects')) {
            $query = DB::table('intern_projects')
                ->where('eti_id', $intern->eti_id)
                ->where('pstatus', 'approved');
            
            // Check if created_at column exists before ordering by it
            if (Schema::hasColumn('intern_projects', 'created_at')) {
                $query->orderBy('created_at', 'desc');
            } elseif (Schema::hasColumn('intern_projects', 'id')) {
                $query->orderBy('id', 'desc');
            } elseif (Schema::hasColumn('intern_projects', 'end_date')) {
                $query->orderBy('end_date', 'desc');
            }
            
            $projects = $query->limit(10)->get();
        }
        
        // Get certificates
        $certificates = collect([]);
        if (Schema::hasTable('generated_certificates')) {
            $query = DB::table('generated_certificates')
                ->where('intern_id', $intern->int_id)
                ->where('status', 'approved');
            
            // Check if created_at column exists before ordering
            if (Schema::hasColumn('generated_certificates', 'created_at')) {
                $query->orderBy('created_at', 'desc');
            } elseif (Schema::hasColumn('generated_certificates', 'id')) {
                $query->orderBy('id', 'desc');
            }
            
            $certificates = $query->get();
        }
        
        return view('pages.intern.profile.public', compact(
            'intern', 'profileImage', 'stats', 'skills', 'projects', 'certificates'
        ));
    }
    
    /**
     * Get profile statistics
     */
    private function getProfileStats($etiId)
    {
        $stats = [
            'total_tasks' => 0,
            'completed_tasks' => 0,
            'total_projects' => 0,
            'completed_projects' => 0,
        ];
        
        try {
            if (Schema::hasTable('intern_tasks')) {
                $stats['total_tasks'] = DB::table('intern_tasks')
                    ->where('eti_id', $etiId)
                    ->count();
                    
                // Check if task_status column exists
                if (Schema::hasColumn('intern_tasks', 'task_status')) {
                    $stats['completed_tasks'] = DB::table('intern_tasks')
                        ->where('eti_id', $etiId)
                        ->where('task_status', 'approved')
                        ->count();
                }
            }
            
            if (Schema::hasTable('intern_projects')) {
                $stats['total_projects'] = DB::table('intern_projects')
                    ->where('eti_id', $etiId)
                    ->count();
                    
                // Check if pstatus column exists
                if (Schema::hasColumn('intern_projects', 'pstatus')) {
                    $stats['completed_projects'] = DB::table('intern_projects')
                        ->where('eti_id', $etiId)
                        ->where('pstatus', 'approved')
                        ->count();
                }
            }
        } catch (\Exception $e) {
            // Tables might not exist yet
        }
        
        return $stats;
    }
    
    /**
 * Get intern skills
 */
private function getInternSkills($internId)
{
    if (!Schema::hasTable('intern_skills')) {
        return collect([]);
    }
    
    try {
        $skills = DB::table('intern_skills')
            ->where('intern_id', $internId)
            ->pluck('skill');
        
        // Filter out any email addresses from skills
        $filteredSkills = $skills->filter(function($skill) {
            return !filter_var($skill, FILTER_VALIDATE_EMAIL);
        });
        
        return $filteredSkills;
    } catch (\Exception $e) {
        return collect([]);
    }
}

    /**
     * Show intern's own portfolio (authenticated view)
     * This is the same as public profile but for the intern to preview
     */
    public function portfolio()
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }
        
        $profileImage = Helpers::getProfileImage($intern);
        $stats = $this->getProfileStats($intern->eti_id);
        $skills = $this->getInternSkills($intern->int_id);
        
        // Get completed projects - with safe ordering
        $projects = collect([]);
        if (Schema::hasTable('intern_projects')) {
            $query = DB::table('intern_projects')
                ->where('eti_id', $intern->eti_id)
                ->where('pstatus', 'approved');
            
            // Check if created_at column exists before ordering by it
            if (Schema::hasColumn('intern_projects', 'created_at')) {
                $query->orderBy('created_at', 'desc');
            } elseif (Schema::hasColumn('intern_projects', 'id')) {
                $query->orderBy('id', 'desc');
            } elseif (Schema::hasColumn('intern_projects', 'end_date')) {
                $query->orderBy('end_date', 'desc');
            }
            
            $projects = $query->limit(10)->get();
        }
        
        // Get certificates
        $certificates = collect([]);
        if (Schema::hasTable('generated_certificates')) {
            $query = DB::table('generated_certificates')
                ->where('intern_id', $intern->int_id)
                ->where('status', 'approved');
            
            if (Schema::hasColumn('generated_certificates', 'created_at')) {
                $query->orderBy('created_at', 'desc');
            } elseif (Schema::hasColumn('generated_certificates', 'id')) {
                $query->orderBy('id', 'desc');
            }
            
            $certificates = $query->get();
        }
        
        return view('pages.intern.profile.public', compact(
            'intern', 'profileImage', 'stats', 'skills', 'projects', 'certificates'
        ));
    }
}