<?php

namespace App\Http\Controllers\intern;
use App\Models\InternAccount;
use App\Http\Controllers\Controller;
use App\Services\PortfolioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class InternProfileController extends Controller
{
    protected $portfolioService;

    public function __construct(PortfolioService $portfolioService)
    {
        $this->portfolioService = $portfolioService;
    }

    public function index()
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }

    //    Fresh data from DB
    $intern = InternAccount::find($intern->int_id);
        
       // $profileImage = $this->portfolioService->getProfileImage($intern);
        $stats = $this->portfolioService->getPortfolioStats($intern);
        $skills = $this->portfolioService->getInternSkills($intern->int_id);
        
        return view('pages.intern.profile.index', compact(
            'intern', 
           // 'profileImage', 
            'stats', 
            'skills'
        ));
    }
    
    public function edit()
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }

          // ✅ Fresh data from DB
    $intern = InternAccount::find($intern->int_id);

     // ❌ REMOVE - Service se cached image mat lo
    // $profileImage = $this->portfolioService->getProfileImage($intern);
    
        
        $skills = $this->portfolioService->getInternSkills($intern->int_id);
        $skillsArray = $skills->toArray();
        
        return view('pages.intern.profile.edit', compact('intern', 'skills', 'skillsArray'));
    }
    
    public function update(Request $request)
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:100',
            'university' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'skills' => 'nullable|string',
            'current_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:8|confirmed',
        ]);
        
        // Update basic info
        $updateData = ['name' => $validated['name']];
        $fields = ['phone', 'city', 'university', 'bio'];
        foreach ($fields as $field) {
            if (Schema::hasColumn('intern_accounts', $field) && isset($validated[$field])) {
                $updateData[$field] = $validated[$field];
            }
        }
        
        DB::table('intern_accounts')
            ->where('int_id', $intern->int_id)
            ->update($updateData);
        
        // Update password if provided
        if (!empty($validated['new_password'])) {
            if ($validated['current_password'] != $intern->password) {
                return back()->withErrors(['current_password' => 'Current password is incorrect'])->withInput();
            }
            
            DB::table('intern_accounts')
                ->where('int_id', $intern->int_id)
                ->update(['password' => $validated['new_password']]);
        }
        
        // Update skills
        $this->updateInternSkills($intern->int_id, $request->skills);
        

        session()->forget('image_time');
    session()->put('image_time', time());  

    //Refresh session
        return redirect()->route('intern.profile')
            ->with('success', 'Profile updated successfully!');
    }
    
    private function updateInternSkills(int $internId, ?string $skillsJson): void
    {
        if (!Schema::hasTable('intern_skills')) {
            return;
        }
        
        $skillsArray = [];
        
        if (!empty($skillsJson)) {
            $decoded = json_decode($skillsJson, true);
            
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $skillsArray = array_filter($decoded, function($skill) {
                    $skill = trim($skill);
                    return !empty($skill);
                });
                $skillsArray = array_values(array_unique($skillsArray));
            }
        }
        
        // Delete old skills
        DB::table('intern_skills')
            ->where('intern_id', $internId)
            ->delete();
        
        // Insert new skills
        foreach ($skillsArray as $skill) {
            $skill = trim($skill);
            if (!empty($skill)) {
                DB::table('intern_skills')->insert([
                    'intern_id' => $internId,
                    'skill' => $skill,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
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
            
            $path = $image->storeAs('uploads/interns', $imageName, 'public');
            $imagePath = 'storage/' . $path;
            
            if ($intern->image && !str_contains($intern->image, 'ezitech.png')) {
                $oldPath = str_replace('storage/', '', $intern->image);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            
            DB::table('intern_accounts')
                ->where('int_id', $intern->int_id)
                ->update(['image' => $imagePath]);

                //Clear session cache
                    session()->forget('image_time');
    session()->put('image_time', time());
            
            return redirect()->back()->with('success', 'Profile image updated successfully!');
        }
        
        return redirect()->back()->with('error', 'Failed to upload image.');
    }
    
    public function publicProfile($identifier = null)
    {
        if ($identifier) {
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
        
        // Prepare portfolio data using service
       // $profileImage = $this->portfolioService->getProfileImage($intern);
        $stats = $this->portfolioService->getPortfolioStats($intern);
        $statItems = $this->portfolioService->getStatItems($stats);
        $internshipData = $this->portfolioService->calculateInternshipProgress($intern);
        $taskRate = $this->portfolioService->calculateTaskRate($stats);
        $projectRate = $this->portfolioService->calculateProjectRate($stats);
        $skills = $this->portfolioService->getInternSkills($intern->int_id);
        $badgesData = $this->portfolioService->getAchievementBadges($stats);
        $projects = $this->portfolioService->getApprovedProjects($intern->eti_id);
        $certificates = $this->portfolioService->getApprovedCertificates($intern->int_id);
        
        return view('pages.intern.profile.public', compact(
            'intern',
            'stats',
            'statItems',
            'internshipData',
            'taskRate',
            'projectRate',
            'skills',
            'badgesData',
            'projects',
            'certificates'
        ));
    }
    
    public function portfolio()
    {
        return $this->publicProfile();
    }
}