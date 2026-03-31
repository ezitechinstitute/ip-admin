<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InternEvaluation extends Model
{
    protected $table = 'intern_evaluations';

    protected $fillable = [
        'eti_id',
        'supervisor_id',
        'month',
        'technical_skills',
        'problem_solving',
        'communication',
        'task_completion',
        'overall_score',
        'remarks',
    ];

    // Intern Relationship
    public function intern()
    {
        return $this->belongsTo(InternAccount::class, 'eti_id', 'eti_id');
    }

    // Supervisor Relationship
    public function supervisor()
    {
        return $this->belongsTo(ManagersAccount::class, 'supervisor_id', 'manager_id');
    }
}
