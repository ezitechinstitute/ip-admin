<?php

namespace App\Http\Controllers\intern;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        $internEtiId = $intern->eti_id;
        $internEmail = $intern->email;
        $internId = $intern->int_id;

        // Task counts
        $totalTasks = DB::table('intern_tasks')->where('eti_id', $internEtiId)->count();
        $completedTasks = DB::table('intern_tasks')->where('eti_id', $internEtiId)->where('task_status', 'approved')->count();
        $pendingTasks = DB::table('intern_tasks')->where('eti_id', $internEtiId)->where('task_status', 'pending')->count();
        $submittedTasks = DB::table('intern_tasks')->where('eti_id', $internEtiId)->where('task_status', 'submitted')->count();

        // Project counts
        $totalProjects = DB::table('intern_projects')->where('eti_id', $internEtiId)->count();
        $completedProjects = DB::table('intern_projects')->where('eti_id', $internEtiId)->where('pstatus', 'approved')->count();
        $ongoingProjects = DB::table('intern_projects')->where('eti_id', $internEtiId)->where('pstatus', 'ongoing')->count();

        // Invoice status
        $invoices = DB::table('invoices')->where('intern_email', $internEmail)->get();
        $totalInvoices = $invoices->count();
        $paidInvoices = $invoices->where('remaining_amount', '<=', 0)->count();
        $pendingInvoices = $invoices->filter(function($inv) {
            return $inv->remaining_amount > 0 && $inv->due_date >= Carbon::now();
        })->count();
        $overdueInvoices = $invoices->filter(function($inv) {
            return $inv->remaining_amount > 0 && $inv->due_date < Carbon::now();
        })->count();

        $isFrozen = $overdueInvoices > 0;

        // Supervisor name
        $supervisorName = 'Not Assigned';
        if ($intern->supervisor_id) {
            $supervisor = DB::table('manager_accounts')->where('manager_id', $intern->supervisor_id)->first();
            $supervisorName = $supervisor ? $supervisor->name : 'Not Assigned';
        }

        // Remaining duration
        $startDate = $intern->start_date ? Carbon::parse($intern->start_date) : Carbon::now();
        $endDate = $startDate->copy()->addMonths(6);
        $remainingDays = max(0, Carbon::now()->diffInDays($endDate, false));

        // Internship status
        if ($isFrozen) {
            $internshipStatus = 'Frozen';
        } elseif (Carbon::now()->greaterThan($endDate)) {
            $internshipStatus = 'Completed';
        } else {
            $internshipStatus = 'Active';
        }

        // Progress percentages
        $taskPercentage = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
        $projectPercentage = $totalProjects > 0 ? round(($completedProjects / $totalProjects) * 100) : 0;
        $totalAmount = $invoices->sum('total_amount');
        $paidAmount = $invoices->sum('received_amount');
        $paymentPercentage = $totalAmount > 0 ? round(($paidAmount / $totalAmount) * 100) : 0;

        // Recent tasks
        $recentTasks = DB::table('intern_tasks')
            ->where('eti_id', $internEtiId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Upcoming deadlines
        $upcomingDeadlines = DB::table('intern_tasks')
            ->where('eti_id', $internEtiId)
            ->where('task_status', '!=', 'approved')
            ->where('task_end', '>=', Carbon::now())
            ->orderBy('task_end', 'asc')
            ->limit(5)
            ->get();

        // Timeline
        $timeline = $this->getTimeline($intern);

        // Notifications
        $notifications = $this->getNotifications($internId);

        // Performance data
        $performance = $this->getPerformanceData($internEtiId);

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

        return view('pages.intern.dashboard', compact(
            'intern', 'stats', 'progress', 'recentTasks', 'upcomingDeadlines',
            'timeline', 'notifications', 'performance'
        ));
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
        $taskCompletion = DB::table('intern_tasks')
            ->select(DB::raw('DATE(updated_at) as date'), DB::raw('count(*) as count'))
            ->where('eti_id', $etiId)
            ->where('task_status', 'approved')
            ->where('updated_at', '>=', Carbon::now()->subDays(30))
            ->groupBy(DB::raw('DATE(updated_at)'))
            ->get();
        
        // Calculate average score safely
        $averageScore = 0;
        try {
            $averageScore = DB::table('intern_tasks')
                ->where('eti_id', $etiId)
                ->whereNotNull('grade')
                ->avg('grade');
            $averageScore = round($averageScore ?? 0, 2);
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