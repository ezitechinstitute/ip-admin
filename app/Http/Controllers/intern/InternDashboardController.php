<?php

namespace App\Http\Controllers\intern;
use App\Helpers\PortalFreezeHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\InternDashboardService;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class InternDashboardController extends Controller
{
    public function index()
{
    $intern = Auth::guard('intern')->user();
    if (!$intern) {
        return redirect()->route('login');
    }

    // Portal freeze check
    $freezeStatus = PortalFreezeHelper::getStatus($intern->email);
    $freezeWarning = $freezeStatus['frozen'] ? $freezeStatus['message'] : null;

    $internEtiId = $intern->eti_id;
    $internEmail = $intern->email;
    $internId = $intern->int_id;

    // Task counts
    $tasksQuery = DB::table('intern_tasks')->where('eti_id', $internEtiId);
    $totalTasks = (clone $tasksQuery)->count();
    $completedTasks = (clone $tasksQuery)->where('task_status', 'approved')->count();
    $pendingTasks = (clone $tasksQuery)->where('task_status', 'pending')->count();
    $submittedTasks = (clone $tasksQuery)->where('task_status', 'submitted')->count();

    // Project counts
    $totalProjects = DB::table('intern_projects')->where('eti_id', $internEtiId)->count();
    $completedProjects = DB::table('intern_projects')->where('eti_id', $internEtiId)->where('pstatus', 'approved')->count();
    $ongoingProjects = DB::table('intern_projects')->where('eti_id', $internEtiId)->where('pstatus', 'ongoing')->count();

    // Invoice status
    $invoices = DB::table('invoices')->where('intern_email', $internEmail)->get();
    $totalInvoices = $invoices->count();
    $paidInvoices = $invoices->where('remaining_amount', '<=', 0)->count();
    $pendingInvoices = $invoices->filter(fn($inv) => ($inv->remaining_amount ?? 0) > 0 && !empty($inv->due_date) && Carbon::parse($inv->due_date)->gte(Carbon::now()))->count();
    $overdueInvoices = $invoices->filter(fn($inv) => ($inv->remaining_amount ?? 0) > 0 && !empty($inv->due_date) && Carbon::parse($inv->due_date)->lt(Carbon::now()))->count();
    $isFrozen = $overdueInvoices > 0;

    // Supervisor name
    $supervisorName = 'Not Assigned';
    if ($intern->supervisor_id) {
        $supervisor = DB::table('manager_accounts')->where('manager_id', $intern->supervisor_id)->first();
        $supervisorName = $supervisor ? $supervisor->name : 'Not Assigned';
    }

    // Remaining duration & status
    $startDate = $intern->start_date ? Carbon::parse($intern->start_date) : Carbon::now();
    $endDate = $startDate->copy()->addMonths(6);
    $remainingDays = max(0, Carbon::now()->diffInDays($endDate, false));
    $internshipStatus = $isFrozen ? 'Frozen' : (Carbon::now()->greaterThan($endDate) ? 'Completed' : 'Active');

    // Progress percentages
    $taskPercentage = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
    $projectPercentage = $totalProjects > 0 ? round(($completedProjects / $totalProjects) * 100) : 0;
    $totalAmount = $invoices->sum('total_amount');
    $paidAmount = $invoices->sum('received_amount');
    $paymentPercentage = $totalAmount > 0 ? round(($paidAmount / $totalAmount) * 100) : 0;

    // Recent tasks & deadlines
    $recentTasks = DB::table('intern_tasks')
        ->where('eti_id', $internEtiId)
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

    $upcomingDeadlines = DB::table('intern_tasks')
        ->where('eti_id', $internEtiId)
        ->where('task_status', '!=', 'approved')
        ->where('task_end', '>=', Carbon::now())
        ->orderBy('task_end', 'asc')
        ->limit(5)
        ->get();

    // Timeline (convert array to Collection)
    $timeline = collect($this->getTimeline($intern));

    // Notifications & performance
    $notifications = $this->getNotifications($internId);
    $performance = $this->getPerformanceData($internEtiId);

    // Stats array
    $stats = [
        'internship_status' => $internshipStatus,
        'is_frozen' => $isFrozen,
        'technology' => $intern->int_technology ?? 'Not Assigned',
        'supervisor_name' => $supervisorName,
        'projects_assigned' => $totalProjects,
        'projects_completed' => $completedProjects,
        'projects_ongoing' => $ongoingProjects,
        'tasks_total' => $totalTasks,
        'tasks_completed' => $completedTasks,
        'tasks_pending' => $pendingTasks,
        'tasks_submitted' => $submittedTasks,
        'remaining_days' => $remainingDays,
        'total_invoices' => $totalInvoices,
        'paid_invoices' => $paidInvoices,
        'pending_invoices' => $pendingInvoices,
        'overdue_invoices' => $overdueInvoices,
    ];

    $progress = [
        'task_percentage' => $taskPercentage,
        'project_percentage' => $projectPercentage,
        'payment_percentage' => $paymentPercentage,
    ];

    // Prepare dashboard data via service
    $dashboardService = new InternDashboardService();
    $dashboardData = $dashboardService->prepareDashboardData(
        $intern, $stats, $progress, $performance,
        $recentTasks, $upcomingDeadlines, $notifications,
        $timeline, $freezeWarning
    );

    // Single return – all logic now in service
    return view('pages.intern.dashboard', ['dashboard' => $dashboardData]);
}

    /**
     * Get internship timeline
     */
    private function getTimeline($intern)
    {
        $startDate = $intern->start_date ? Carbon::parse($intern->start_date) : Carbon::now();
        $endDate = $startDate->copy()->addMonths(6);
        $today = Carbon::now();
        
        $timeline = [];
        
        // Internship start
        $timeline[] = [
            'date' => $startDate->format('d M Y'),
            'title' => 'Internship Started',
            'description' => 'Your internship journey began',
            'type' => 'start',
            'icon' => 'ti ti-rocket',
            'color' => 'success',
            'completed' => true
        ];
        
        // Projects milestones
        $projects = DB::table('intern_projects')
            ->where('eti_id', $intern->eti_id)
            ->orderBy('end_date')
            ->get();
        
        foreach ($projects as $project) {
            if ($project->end_date) {
                $projectEnd = Carbon::parse($project->end_date);
                $timeline[] = [
                    'date' => $projectEnd->format('d M Y'),
                    'title' => $project->title,
                    'description' => 'Project deadline',
                    'type' => 'project',
                    'icon' => 'ti ti-briefcase',
                    'color' => $project->pstatus === 'approved' ? 'success' : ($projectEnd->lt($today) ? 'danger' : 'warning'),
                    'completed' => $project->pstatus === 'approved'
                ];
            }
        }
        
        // Internship completion
        if ($today->lte($endDate)) {
            $timeline[] = [
                'date' => $endDate->format('d M Y'),
                'title' => 'Internship Completion',
                'description' => 'Expected completion date',
                'type' => 'end',
                'icon' => 'ti ti-flag',
                'color' => 'primary',
                'completed' => false
            ];
        }
        
        // Sort by date
        usort($timeline, function($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });
        
        return array_slice($timeline, 0, 10);
    }

    /**
     * Get notifications for intern
     */
    private function getNotifications($internId)
    {
        // First check if notifications table exists
        if (!Schema::hasTable('intern_notifications')) {
            return collect([]);
        }
        
        return DB::table('intern_notifications')
            ->where('intern_id', $internId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Get performance data
     */
   private function getPerformanceData($etiId)
{
    // Task completion over time (last 30 days)
    $taskCompletion = collect([]);
    try {
        $taskCompletion = DB::table('intern_tasks')
            ->select(DB::raw('DATE(updated_at) as date'), DB::raw('count(*) as count'))
            ->where('eti_id', $etiId)
            ->where('task_status', 'approved')
            ->where('updated_at', '>=', Carbon::now()->subDays(30))
            ->groupBy(DB::raw('DATE(updated_at)'))
            ->get();
    } catch (\Exception $e) {
        // Fallback if group by fails
        $taskCompletion = collect([]);
    }
    
    // Calculate average score safely - FIXED
    $averageScore = 0;
    try {
        // First try to get from grade column
        $avgGrade = DB::table('intern_tasks')
            ->where('eti_id', $etiId)
            ->whereNotNull('grade')
            ->avg('grade');
        
        if ($avgGrade && $avgGrade > 0) {
            $averageScore = $avgGrade;
        } else {
            // If no grade, calculate from task_obt_points
            $tasksWithPoints = DB::table('intern_tasks')
                ->where('eti_id', $etiId)
                ->whereNotNull('task_obt_points')
                ->where('task_obt_points', '>', 0)
                ->whereNotNull('task_points')
                ->where('task_points', '>', 0)
                ->get();
            
            if ($tasksWithPoints->count() > 0) {
                $totalPercentage = 0;
                foreach ($tasksWithPoints as $task) {
                    $totalPercentage += ($task->task_obt_points / $task->task_points) * 100;
                }
                $averageScore = $totalPercentage / $tasksWithPoints->count();
            }
        }
        
        $averageScore = round($averageScore ?? 0);
    } catch (\Exception $e) {
        $averageScore = 0;
    }
    
    return [
        'task_completion' => $taskCompletion,
        'total_tasks' => DB::table('intern_tasks')->where('eti_id', $etiId)->count(),
        'completed_tasks' => DB::table('intern_tasks')->where('eti_id', $etiId)->where('task_status', 'approved')->count(),
        'average_score' => $averageScore,
    ];
}

    /**
     * Mark a single notification as read
     */
    public function markNotificationRead($id, Request $request)
    {
        $intern = Auth::guard('intern')->user();
        
        if (Schema::hasTable('intern_notifications')) {
            $updated = DB::table('intern_notifications')
                ->where('id', $id)
                ->where('intern_id', $intern->int_id)
                ->update(['is_read' => true]);
                
            if ($updated) {
                return response()->json(['success' => true]);
            }
        }
        
        return response()->json(['success' => false], 404);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllRead(Request $request)
    {
        $intern = Auth::guard('intern')->user();
        
        if (Schema::hasTable('intern_notifications')) {
            $updated = DB::table('intern_notifications')
                ->where('intern_id', $intern->int_id)
                ->where('is_read', false)
                ->update(['is_read' => true]);
            
            return response()->json(['success' => true, 'count' => $updated]);
        }
        
        return response()->json(['success' => false], 404);
    }
}