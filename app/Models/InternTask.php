<?php

namespace App\Models;

use App\Models\InternAccount;
use App\Models\InternProject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;


    

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
        'task_approve',
        'review',
        'task_screenshot',
        'task_live_url',
        'task_git_url',
        'submit_description',
    ];

    // Intern Relationship
    public function intern()
    {
        return $this->belongsTo(InternAccount::class, 'eti_id', 'eti_id');
    }

    // Project Relationship (Only if project_id exists in intern_tasks table)
    public function project()
    {
        return $this->belongsTo(InternProject::class, 'project_id', 'project_id');
    }



    
}
