<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\InternAccount;
use App\Models\ProjectTask;

class InternProject extends Model
{
    protected $table = 'intern_projects';
    protected $primaryKey = 'project_id'; // IMPORTANT
    public $timestamps = false;

    protected $fillable = [
        'project_id',
        'eti_id',
        'email',
        'title',
        'start_date',
        'end_date',
        'duration',
        'days',
        'project_marks',
        'obt_marks',
        'description',
        'assigned_by',
        'pstatus',
        'createdat',
        'updatedat',
    ];

    // Intern relation
    public function intern()
    {
        return $this->belongsTo(
            InternAccount::class,
            'eti_id',
            'eti_id'
        );
    }

    // ✅ ADD THIS (TASKS RELATION)
    public function tasks()
    {
        return $this->hasMany(
            ProjectTask::class,
            'project_id',   // FK in project_tasks
            'project_id'    // PK in intern_projects
        );
    }
    public function chat()
    {
        // Points to the Chat/ProjectChat model using project_id as the foreign key
        return $this->hasOne(ProjectChat::class, 'project_id', 'project_id');
    }
}
