<?php

namespace App\Models;

// English comments: Crucial change! Extend Authenticatable instead of base Model
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Notifications\Notifiable;
use App\Models\SupervisorAttendance;
use App\Models\InternCurriculumAssignment;
use App\Models\CurriculumSupervisorMapping;

class ManagersAccount extends Authenticatable
{
    use Notifiable;

    protected $table = 'manager_accounts'; 

    protected $primaryKey = 'manager_id';

    public $incrementing = true; 

    protected $fillable = [
        'eti_id',
        'image',
        'name',
        'email',
        'contact',
        'join_date',
        'password',
        'comission',
        'department',
        'status',
        'loginas',
        'emergency_contact',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    public function attendance()
    {
        return $this->hasMany(SupervisorAttendance::class, 'supervisor_id', 'manager_id');
    }

    /**
     * Supervisor has many interns through curriculum assignment mapping.
     * This is an approximated relationship since intern assignments are tied to curriculum.
     */
    public function interns()
    {
        return $this->hasMany(InternCurriculumAssignment::class, 'assigned_by', 'manager_id');
    }

    /**
     * Supervisor has many activity logs (curriculum supervisor mappings used as log entries).
     */
    public function activityLogs()
    {
        return $this->hasMany(CurriculumSupervisorMapping::class, 'supervisor_id', 'manager_id');
    }
}
