<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    // Relationships
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

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeOverdue($query)
    {
        return $query->where('deadline', '<', now())
                     ->whereIn('status', ['pending', 'submitted']);
    }

    public function scopeForManager($query, $managerId)
    {
        return $query->whereHas('intern', function($q) use ($managerId) {
            $q->where('manager_id', $managerId);
        });
    }
}