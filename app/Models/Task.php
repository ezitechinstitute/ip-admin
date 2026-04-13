<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Task extends Model
{
    protected $table = 'tasks';
    
    protected $fillable = [
        'title',
        'description',
        'supervisor_id',
        'intern_id',
        'project_id',
        'deadline',
        'points',
        'status',
        'submission_notes',
        'supervisor_remarks',
        'grade',
        'submitted_at',
        'reviewed_at'
    ];

    protected $casts = [
        'deadline' => 'date',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime'
    ];

    // ==================== RELATIONSHIPS ====================

    public function supervisor()
    {
        return $this->belongsTo(ManagersAccount::class, 'supervisor_id', 'manager_id');
    }

    public function intern()
    {
        return $this->belongsTo(InternAccount::class, 'intern_id', 'int_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    // ==================== SCOPES ====================

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeOverdue($query)
    {
        return $query->where('deadline', '<', now())
                     ->whereIn('status', ['pending', 'submitted']);
    }

    public function scopeForManager($query, $managerId)
    {
        return $query->where('supervisor_id', $managerId);
    }

    public function scopeForIntern($query, $internId)
    {
        return $query->where('intern_id', $internId);
    }

    public function scopeForProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeExpired($query)
    {
        return $query->where('deadline', '<', now())
                     ->where('status', 'pending');
    }

    // ==================== HELPER METHODS ====================

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isSubmitted()
    {
        return $this->status === 'submitted';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function isOverdue()
    {
        return $this->deadline < now() && in_array($this->status, ['pending', 'submitted']);
    }

    public function approve($grade = null, $remarks = null)
    {
        return $this->update([
            'status' => 'approved',
            'grade' => $grade,
            'supervisor_remarks' => $remarks,
            'reviewed_at' => now(),
        ]);
    }

    public function reject($remarks = null)
    {
        return $this->update([
            'status' => 'rejected',
            'supervisor_remarks' => $remarks,
            'reviewed_at' => now(),
        ]);
    }

    public function submit($notes = null)
    {
        return $this->update([
            'status' => 'submitted',
            'submission_notes' => $notes,
            'submitted_at' => now(),
        ]);
    }

    public function getDaysUntilDeadline()
    {
        return max(0, now()->diffInDays($this->deadline));
    }

    public function isExpired()
    {
        return $this->deadline < now() && $this->status === 'pending';
    }
}
