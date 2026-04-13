<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\InternProject;
use App\Models\InternAccount;

class ProjectTask extends Model
{
    protected $table = 'project_tasks';
    protected $primaryKey = 'task_id';

    protected $fillable = [
        'task_id',
        'project_id',
        'eti_id',
        'task_title',
        't_start_date',
        't_end_date',
        'task_days',
        'task_duration',
        'task_obt_mark',
        'task_mark',
        'assigned_by',
        'task_status',
        'approved',
        'review',
        'task_screenshot',
        'task_live_url',
        'task_git_url',
        'description',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        't_start_date' => 'date',
        't_end_date' => 'date',
    ];

    // ==================== RELATIONSHIPS ====================

    // ✅ Intern Name
    public function intern()
    {
        return $this->belongsTo(
            InternAccount::class,
            'eti_id',
            'eti_id'
        );
    }

    // ✅ Project Title
    public function project()
    {
        return $this->belongsTo(
            InternProject::class,
            'project_id',
            'project_id'
        );
    }

    // ✅ Supervisor
    public function supervisor()
    {
        return $this->belongsTo(
            ManagersAccount::class,
            'assigned_by',
            'manager_id'
        );
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
        return $query->where('approved', 'yes');
    }

    public function scopeRejected($query)
    {
        return $query->where('approved', 'no');
    }

    public function scopeOverdue($query)
    {
        return $query->whereDate('t_end_date', '<', now())
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

    public function scopeForProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
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
        return $this->approved === 'yes';
    }

    public function isRejected()
    {
        return $this->approved === 'no';
    }

    public function isOverdue()
    {
        return $this->t_end_date && $this->t_end_date < now() && in_array($this->task_status, ['pending', 'submitted']);
    }

    public function approve($marks = null, $review = null)
    {
        return $this->update([
            'approved' => 'yes',
            'task_obt_mark' => $marks ?? $this->task_mark,
            'review' => $review,
            'task_status' => 'approved',
        ]);
    }

    public function reject($review = null)
    {
        return $this->update([
            'approved' => 'no',
            'review' => $review,
            'task_status' => 'rejected',
        ]);
    }

    public function submit($screenshot = null, $liveUrl = null, $gitUrl = null, $description = null)
    {
        return $this->update([
            'task_status' => 'submitted',
            'task_screenshot' => $screenshot,
            'task_live_url' => $liveUrl,
            'task_git_url' => $gitUrl,
            'description' => $description,
        ]);
    }

    public function getDaysUntilDeadline()
    {
        if (!$this->t_end_date) {
            return null;
        }
        return max(0, now()->diffInDays($this->t_end_date));
    }

    public function getProgressPercentage()
    {
        if ($this->task_mark === 0) {
            return 0;
        }
        return round(($this->task_obt_mark / $this->task_mark) * 100, 2);
    }
}

