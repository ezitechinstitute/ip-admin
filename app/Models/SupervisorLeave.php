<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupervisorLeave extends Model
{
    protected $table = 'supervisor_leaves';

    protected $primaryKey = 'leave_id';

    protected $fillable = [
        'supervisor_id',
        'name',
        'email',
        'from_date',
        'to_date',
        'reason',
        'days',
        'leave_status'
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
    ];

    // ==================== SCOPES ====================

    public function scopePending($query)
    {
        return $query->where('leave_status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('leave_status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('leave_status', 'rejected');
    }

    public function scopeActive($query)
    {
        return $query->where('leave_status', 'approved')
            ->whereDate('from_date', '<=', now())
            ->whereDate('to_date', '>=', now());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('leave_status', 'approved')
            ->whereDate('from_date', '>', now());
    }

    public function scopeSupervisor($query, $supervisorId)
    {
        return $query->where('supervisor_id', $supervisorId);
    }

    // ==================== RELATIONSHIPS ====================

    public function supervisor()
    {
        return $this->belongsTo(ManagersAccount::class, 'supervisor_id', 'manager_id');
    }

    // ==================== HELPER METHODS ====================

    public function isPending()
    {
        return $this->leave_status === 'pending';
    }

    public function isApproved()
    {
        return $this->leave_status === 'approved';
    }

    public function isRejected()
    {
        return $this->leave_status === 'rejected';
    }

    public function isActive()
    {
        return $this->isApproved() && now()->between($this->from_date, $this->to_date);
    }

    public function approve()
    {
        return $this->update(['leave_status' => 'approved']);
    }

    public function reject()
    {
        return $this->update(['leave_status' => 'rejected']);
    }

    public function getDurationInDays()
    {
        return $this->from_date->diffInDays($this->to_date) + 1;
    }
}

