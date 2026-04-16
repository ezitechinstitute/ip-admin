<?php

namespace App\Http\Controllers;

use App\Models\EscalationTracking;
use App\Services\EscalationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EscalationController extends Controller
{
    protected $escalationService;

    public function __construct(EscalationService $escalationService)
    {
        $this->escalationService = $escalationService;
    }

    /**
     * Display all escalations (Admin only)
     */
    public function index()
    {
        $user = Auth::guard('admin')->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Unauthorized');
        }

        $escalations = $this->escalationService->getPendingEscalations();
        $summary = $this->escalationService->getEscalationSummary();

        return view('pages.admin.escalations.index', [
            'escalations' => $escalations,
            'summary' => $summary,
        ]);
    }

    /**
     * Display escalations for manager
     */
    public function managerEscalations()
    {
        $manager = Auth::guard('manager')->user();
        if (!$manager) {
            return redirect()->route('login')->with('error', 'Please login as manager');
        }

        $escalations = $this->escalationService->getPendingEscalationsForManager($manager->manager_id);

        return view('pages.manager.escalations.index', [
            'escalations' => $escalations,
        ]);
    }

    /**
     * Display escalations for supervisor (supervisors see escalations for assigned interns)
     */
    public function supervisorEscalations()
    {
        $supervisor = Auth::guard('supervisor')->user();
        if (!$supervisor) {
            return redirect()->route('login')->with('error', 'Please login as supervisor');
        }

        try {
            // Get escalations for interns assigned to this supervisor
            $escalations = EscalationTracking::whereHas('intern', function ($query) use ($supervisor) {
                // Assuming supervisor is linked via SupervisorInternAssignment or similar
                $query->whereIn('int_id', function ($subQuery) use ($supervisor) {
                    $subQuery->select('int_id')
                        ->from('supervisor_intern_assignments')
                        ->where('supervisor_id', $supervisor->supervisor_id);
                });
            })->pending()->get();

            return view('pages.supervisor.escalations.index', [
                'escalations' => $escalations,
            ]);
        } catch (\Exception $e) {
            Log::error("Error fetching supervisor escalations: {$e->getMessage()}");
            return redirect()->route('supervisor.dashboard')
                ->with('error', 'Failed to load escalations');
        }
    }

    /**
     * Show escalation details
     */
    public function show($id)
    {
        $escalation = EscalationTracking::with(['intern', 'manager'])->findOrFail($id);

        // Authorization check
        $manager = Auth::guard('manager')->user();
        if ($manager && $escalation->manager_id !== $manager->manager_id) {
            return redirect()->route('escalations.manager')
                ->with('error', 'Unauthorized access');
        }

        return view('pages.escalations.show', [
            'escalation' => $escalation,
        ]);
    }

    /**
     * Resolve escalation (Manager action)
     */
    public function resolve(Request $request, $id)
    {
        $manager = Auth::guard('manager')->user();
        if (!$manager) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $escalation = EscalationTracking::findOrFail($id);

        // Check authorization
        if ($escalation->manager_id !== $manager->manager_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Validate resolution
        $request->validate([
            'resolution_notes' => 'required|string|min:10|max:1000',
        ]);

        try {
            $this->escalationService->resolveEscalation($escalation, $request->resolution_notes);

            Log::info("Manager resolved escalation", [
                'escalation_id' => $id,
                'manager_id' => $manager->manager_id,
                'notes' => $request->resolution_notes,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Escalation resolved successfully',
                ]);
            }

            return redirect()->route('escalations.manager')
                ->with('success', 'Escalation resolved successfully!');

        } catch (\Exception $e) {
            Log::error("Error resolving escalation: {$e->getMessage()}");

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Failed to resolve escalation',
                ], 500);
            }

            return back()->with('error', 'Failed to resolve escalation: ' . $e->getMessage());
        }
    }

    /**
     * Admin resolve escalation
     */
    public function adminResolve(Request $request, $id)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $escalation = EscalationTracking::findOrFail($id);

        $request->validate([
            'resolution_notes' => 'required|string|min:10|max:1000',
            'action' => 'required|in:resolved,reopened,escalate',
        ]);

        try {
            if ($request->action === 'resolved') {
                $this->escalationService->resolveEscalation($escalation, $request->resolution_notes);
            } elseif ($request->action === 'reopened') {
                $escalation->update([
                    'resolved_at' => null,
                    'resolution_notes' => $request->resolution_notes,
                ]);
            }

            Log::info("Admin processed escalation", [
                'escalation_id' => $id,
                'action' => $request->action,
                'notes' => $request->resolution_notes,
            ]);

            return redirect()->route('escalations.index')
                ->with('success', "Escalation {$request->action} successfully!");

        } catch (\Exception $e) {
            Log::error("Error processing escalation: {$e->getMessage()}");
            return back()->with('error', 'Failed to process escalation');
        }
    }

    /**
     * Get escalation statistics (API)
     */
    public function statistics(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $days = $request->query('days', 30);
        $stats = $this->escalationService->getStatistics($days);

        return response()->json($stats);
    }

    /**
     * Get intern escalation history (API)
     */
    public function internHistory($internId)
    {
        try {
            $history = $this->escalationService->getInternEscalationHistory($internId);
            return response()->json([
                'success' => true,
                'data' => $history,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch history',
            ], 500);
        }
    }

    /**
     * List unnotified admin escalations (API)
     */
    public function unnotifiedAdminEscalations()
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $escalations = $this->escalationService->getUnnotifiedAdminEscalations();

        return response()->json([
            'count' => $escalations->count(),
            'escalations' => $escalations->map(fn($e) => $e->format()),
        ]);
    }

    /**
     * Mark admin escalation as notified
     */
    public function markAdminNotified($id)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $escalation = EscalationTracking::findOrFail($id);
            $escalation->markAdminNotified();

            return response()->json([
                'success' => true,
                'message' => 'Marked as notified',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update',
            ], 500);
        }
    }

    /**
     * Auto-check and resolve escalations based on status changes
     */
    public function autoResolveCheck($internId)
    {
        try {
            $this->escalationService->checkAutoResolution($internId);

            return response()->json([
                'success' => true,
                'message' => 'Auto-resolution check completed',
            ]);
        } catch (\Exception $e) {
            Log::error("Auto-resolution check failed: {$e->getMessage()}");
            return response()->json([
                'error' => 'Failed',
            ], 500);
        }
    }
}
