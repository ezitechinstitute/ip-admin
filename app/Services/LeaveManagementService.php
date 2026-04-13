<?php

namespace App\Services;

use App\Models\Leave;
use App\Models\EmployeeLeave;
use App\Models\SupervisorLeave;
use App\Models\InternAccount;
use App\Models\ManagersAccount;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LeaveManagementService
{
    /**
     * Get all leave requests for an intern
     */
    public function getInternLeaves($internId, $filters = [])
    {
        try {
            $intern = InternAccount::where('int_id', $internId)->first();
            if (!$intern) {
                return collect([]);
            }

            $leaves = Leave::where('eti_id', $intern->eti_id)
                ->when(isset($filters['status']), function($q) use ($filters) {
                    return $q->where('leave_status', $filters['status']);
                })
                ->when(isset($filters['from_date']), function($q) use ($filters) {
                    return $q->whereDate('from_date', '>=', $filters['from_date']);
                })
                ->when(isset($filters['to_date']), function($q) use ($filters) {
                    return $q->whereDate('to_date', '<=', $filters['to_date']);
                })
                ->get()
                ->map(fn($l) => $this->formatLeave($l, 'intern'))
                ->sortByDesc('from_date');

            return $leaves;
        } catch (\Exception $e) {
            Log::error("Error getting intern leaves: {$e->getMessage()}");
            return collect([]);
        }
    }

    /**
     * Get all leave requests for a supervisor/manager
     */
    public function getSupervisorLeaves($supervisorId, $filters = [])
    {
        try {
            $leaves = SupervisorLeave::where('supervisor_id', $supervisorId)
                ->when(isset($filters['status']), function($q) use ($filters) {
                    return $q->where('leave_status', $filters['status']);
                })
                ->when(isset($filters['from_date']), function($q) use ($filters) {
                    return $q->whereDate('from_date', '>=', $filters['from_date']);
                })
                ->when(isset($filters['to_date']), function($q) use ($filters) {
                    return $q->whereDate('to_date', '<=', $filters['to_date']);
                })
                ->get()
                ->map(fn($l) => $this->formatLeave($l, 'supervisor'))
                ->sortByDesc('from_date');

            return $leaves;
        } catch (\Exception $e) {
            Log::error("Error getting supervisor leaves: {$e->getMessage()}");
            return collect([]);
        }
    }

    /**
     * Get all leave requests for an employee/manager
     */
    public function getEmployeeLeaves($employeeId, $filters = [])
    {
        try {
            $leaves = EmployeeLeave::where('employee_id', $employeeId)
                ->when(isset($filters['status']), function($q) use ($filters) {
                    return $q->where('leave_status', $filters['status']);
                })
                ->when(isset($filters['from_date']), function($q) use ($filters) {
                    return $q->whereDate('from_date', '>=', $filters['from_date']);
                })
                ->when(isset($filters['to_date']), function($q) use ($filters) {
                    return $q->whereDate('to_date', '<=', $filters['to_date']);
                })
                ->get()
                ->map(fn($l) => $this->formatLeave($l, 'employee'))
                ->sortByDesc('from_date');

            return $leaves;
        } catch (\Exception $e) {
            Log::error("Error getting employee leaves: {$e->getMessage()}");
            return collect([]);
        }
    }

    /**
     * Get pending leave requests that need approval
     */
    public function getPendingLeaves($type = null, $filters = [])
    {
        try {
            $leaves = [];

            if ($type === null || $type === 'intern') {
                $internLeaves = Leave::where('leave_status', 'pending')
                    ->get()
                    ->map(fn($l) => $this->formatLeave($l, 'intern'))
                    ->toArray();
                $leaves = array_merge($leaves, $internLeaves);
            }

            if ($type === null || $type === 'supervisor') {
                $supervisorLeaves = SupervisorLeave::where('leave_status', 'pending')
                    ->get()
                    ->map(fn($l) => $this->formatLeave($l, 'supervisor'))
                    ->toArray();
                $leaves = array_merge($leaves, $supervisorLeaves);
            }

            if ($type === null || $type === 'employee') {
                $employeeLeaves = EmployeeLeave::where('leave_status', 'pending')
                    ->get()
                    ->map(fn($l) => $this->formatLeave($l, 'employee'))
                    ->toArray();
                $leaves = array_merge($leaves, $employeeLeaves);
            }

            return collect($leaves)->sortByDesc('from_date');
        } catch (\Exception $e) {
            Log::error("Error getting pending leaves: {$e->getMessage()}");
            return collect([]);
        }
    }

    /**
     * Get approved/active leaves (currently on leave)
     */
    public function getActiveLeaves($type = null)
    {
        try {
            $today = now()->toDateString();
            $leaves = [];

            if ($type === null || $type === 'intern') {
                $internLeaves = Leave::where('leave_status', 'approved')
                    ->whereDate('from_date', '<=', $today)
                    ->whereDate('to_date', '>=', $today)
                    ->get()
                    ->map(fn($l) => $this->formatLeave($l, 'intern'))
                    ->toArray();
                $leaves = array_merge($leaves, $internLeaves);
            }

            if ($type === null || $type === 'supervisor') {
                $supervisorLeaves = SupervisorLeave::where('leave_status', 'approved')
                    ->whereDate('from_date', '<=', $today)
                    ->whereDate('to_date', '>=', $today)
                    ->get()
                    ->map(fn($l) => $this->formatLeave($l, 'supervisor'))
                    ->toArray();
                $leaves = array_merge($leaves, $supervisorLeaves);
            }

            if ($type === null || $type === 'employee') {
                $employeeLeaves = EmployeeLeave::where('leave_status', 'approved')
                    ->whereDate('from_date', '<=', $today)
                    ->whereDate('to_date', '>=', $today)
                    ->get()
                    ->map(fn($l) => $this->formatLeave($l, 'employee'))
                    ->toArray();
                $leaves = array_merge($leaves, $employeeLeaves);
            }

            return collect($leaves)->sortBy('from_date');
        } catch (\Exception $e) {
            Log::error("Error getting active leaves: {$e->getMessage()}");
            return collect([]);
        }
    }

    /**
     * Get leave statistics
     */
    public function getLeaveStatistics($type = null)
    {
        try {
            $stats = [
                'pending' => 0,
                'approved' => 0,
                'rejected' => 0,
                'total' => 0,
            ];

            if ($type === null || $type === 'intern') {
                $stats['intern_pending'] = Leave::where('leave_status', 'pending')->count();
                $stats['intern_approved'] = Leave::where('leave_status', 'approved')->count();
                $stats['intern_rejected'] = Leave::where('leave_status', 'rejected')->count();
                $stats['intern_total'] = Leave::count();
            }

            if ($type === null || $type === 'supervisor') {
                $stats['supervisor_pending'] = SupervisorLeave::where('leave_status', 'pending')->count();
                $stats['supervisor_approved'] = SupervisorLeave::where('leave_status', 'approved')->count();
                $stats['supervisor_rejected'] = SupervisorLeave::where('leave_status', 'rejected')->count();
                $stats['supervisor_total'] = SupervisorLeave::count();
            }

            if ($type === null || $type === 'employee') {
                $stats['employee_pending'] = EmployeeLeave::where('leave_status', 'pending')->count();
                $stats['employee_approved'] = EmployeeLeave::where('leave_status', 'approved')->count();
                $stats['employee_rejected'] = EmployeeLeave::where('leave_status', 'rejected')->count();
                $stats['employee_total'] = EmployeeLeave::count();
            }

            $stats['pending'] = ($stats['intern_pending'] ?? 0) + ($stats['supervisor_pending'] ?? 0) + ($stats['employee_pending'] ?? 0);
            $stats['approved'] = ($stats['intern_approved'] ?? 0) + ($stats['supervisor_approved'] ?? 0) + ($stats['employee_approved'] ?? 0);
            $stats['rejected'] = ($stats['intern_rejected'] ?? 0) + ($stats['supervisor_rejected'] ?? 0) + ($stats['employee_rejected'] ?? 0);
            $stats['total'] = ($stats['intern_total'] ?? 0) + ($stats['supervisor_total'] ?? 0) + ($stats['employee_total'] ?? 0);

            return $stats;
        } catch (\Exception $e) {
            Log::error("Error getting leave statistics: {$e->getMessage()}");
            return [];
        }
    }

    /**
     * Approve leave request
     */
    public function approveLeave($leaveId, $type, $approverNotes = null)
    {
        try {
            $leave = null;

            if ($type === 'intern') {
                $leave = Leave::where('leave_id', $leaveId)->first();
            } elseif ($type === 'supervisor') {
                $leave = SupervisorLeave::where('leave_id', $leaveId)->first();
            } elseif ($type === 'employee') {
                $leave = EmployeeLeave::where('leave_id', $leaveId)->first();
            }

            if (!$leave) {
                return false;
            }

            return $leave->update([
                'leave_status' => 'approved',
            ]);
        } catch (\Exception $e) {
            Log::error("Error approving leave: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Reject leave request
     */
    public function rejectLeave($leaveId, $type, $rejectReason = null)
    {
        try {
            $leave = null;

            if ($type === 'intern') {
                $leave = Leave::where('leave_id', $leaveId)->first();
            } elseif ($type === 'supervisor') {
                $leave = SupervisorLeave::where('leave_id', $leaveId)->first();
            } elseif ($type === 'employee') {
                $leave = EmployeeLeave::where('leave_id', $leaveId)->first();
            }

            if (!$leave) {
                return false;
            }

            return $leave->update([
                'leave_status' => 'rejected',
            ]);
        } catch (\Exception $e) {
            Log::error("Error rejecting leave: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Format leave to unified structure
     */
    private function formatLeave($leave, $type): array
    {
        $daysCalculated = $leave->days ?? $this->calculateLeaveDays($leave->from_date, $leave->to_date);

        return [
            'id' => $leave->leave_id ?? $leave->id,
            'leave_type' => $type,
            'requestor_name' => $leave->name ?? 'Unknown',
            'requestor_email' => $leave->email ?? null,
            'from_date' => $leave->from_date,
            'to_date' => $leave->to_date,
            'days' => $daysCalculated,
            'reason' => $leave->reason ?? 'Not provided',
            'status' => $leave->leave_status ?? 'pending',
            'created_at' => $leave->created_at ?? now(),
            'updated_at' => $leave->updated_at ?? now(),
        ];
    }

    /**
     * Calculate number of leave days
     */
    private function calculateLeaveDays($fromDate, $toDate)
    {
        try {
            $from = Carbon::createFromFormat('Y-m-d', $fromDate) ?? Carbon::parse($fromDate);
            $to = Carbon::createFromFormat('Y-m-d', $toDate) ?? Carbon::parse($toDate);
            return $from->diffInDays($to) + 1; // +1 to include both days
        } catch (\Exception $e) {
            return 0;
        }
    }
}
