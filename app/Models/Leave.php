<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Leave extends Model
{
    protected $table = 'intern_leaves';

    protected $primaryKey = 'leave_id';

    protected $fillable = [
        'eti_id',
        'name',
        'email',
        'from_date',
        'to_date',
        'reason',
        'technology',
        'intern_type',
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

    public function scopeIntern($query, $intId)
    {
        return $query->whereHas('internAccount', function($q) use ($intId) {
            $q->where('int_id', $intId);
        });
    }

    // ==================== RELATIONSHIPS ====================

    public function internAccount()
    {
        return $this->belongsTo(InternAccount::class, 'eti_id', 'eti_id');
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

