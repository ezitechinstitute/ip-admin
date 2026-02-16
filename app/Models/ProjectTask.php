<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
}
