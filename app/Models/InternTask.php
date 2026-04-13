<?php

namespace App\Models;

use App\Models\InternAccount;
use App\Models\InternProject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Carbon\Carbon;

class InternTask extends Model
{
    protected $table = 'intern_tasks';
    protected $primaryKey = 'task_id'; // Add this because your PK is not 'id'

    protected $fillable = [
        'eti_id',
        'task_title',
        'task_description',
        'task_start',
        'task_end',
        'task_duration',
        'task_days',
        'task_points',
        'task_obt_points',
        'assigned_by',
        'task_status',
        'penalty_flag',
        'code_quality_score',
        'task_approve',
        'review',
        'remarks',
        'task_screenshot',
        'task_live_url',
        'task_git_url',
        'submit_description',
    ];

    protected $casts = [
        'task_start' => 'date',
        'task_end' => 'date',
    ];

    // ==================== RELATIONSHIPS ====================

    // Intern Relationship
    public function intern()
    {
        return $this->belongsTo(InternAccount::class, 'eti_id', 'eti_id');
    }

    // Supervisor Relationship
    public function supervisor()
    {
        return $this->belongsTo(ManagersAccount::class, 'assigned_by', 'manager_id');
    }

    // Project Relationship (Only if project_id exists in intern_tasks table)
    public function project()
    {
        return $this->belongsTo(InternProject::class, 'project_id', 'project_id');
    }

    // ==================== SCOPES ====================

    public function scopePending($query)
    {
        return $query->where('task_status', 'pending');
    }

    public function scopeSubmitted($query)
    {
        return $query->where('task_status', 'submitted');
    }

    public function scopeApproved($query)
    {
        return $query->whereIn('task_status', ['approved', 'completed', 'Completed']);
    }

    public function scopeRejected($query)
    {
        return $query->where('task_status', 'rejected');
    }

    public function scopeOverdue($query)
    {
        return $query->whereDate('task_end', '<', now())
                     ->whereIn('task_status', ['pending', 'submitted']);
    }

    public function scopeForManager($query, $managerId)
    {
        return $query->where('assigned_by', $managerId);
    }

    public function scopeForIntern($query, $etiId)
    {
        return $query->where('eti_id', $etiId);
    }

    // ==================== HELPER METHODS ====================

    public function isPending()
    {
        return $this->task_status === 'pending';
    }

    public function isSubmitted()
    {
        return $this->task_status === 'submitted';
    }

    public function isApproved()
    {
        return in_array($this->task_status, ['approved', 'completed', 'Completed']);
    }

    public function isRejected()
    {
        return $this->task_status === 'rejected';
    }

    public function isOverdue()
    {
        return $this->task_end && $this->task_end < now() && in_array($this->task_status, ['pending', 'submitted']);
    }

    public function approve($points = null, $remarks = null)
    {
        return $this->update([
            'task_status' => 'approved',
            'task_obt_points' => $points ?? $this->task_points,
            'review' => $remarks,
            'task_approve' => 'yes',
        ]);
    }

    public function reject($remarks = null)
    {
        return $this->update([
            'task_status' => 'rejected',
            'review' => $remarks,
        ]);
    }

    public function submit($notes = null, $screenshot = null, $liveUrl = null, $gitUrl = null)
    {
        return $this->update([
            'task_status' => 'submitted',
            'submit_description' => $notes,
            'task_screenshot' => $screenshot,
            'task_live_url' => $liveUrl,
            'task_git_url' => $gitUrl,
        ]);
    }

    public function getDaysUntilDeadline()
    {
        if (!$this->task_end) {
            return null;
        }
        return max(0, now()->diffInDays($this->task_end));
    }

    public function getQualityScore()
    {
        return $this->code_quality_score ?? round(($this->task_obt_points / $this->task_points * 100), 2);
    }
}

