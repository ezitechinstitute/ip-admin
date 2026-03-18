<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternCurriculumAssignment extends Model
{
    use HasFactory;

    protected $table = 'intern_curriculum_assignment';
    protected $primaryKey = 'assignment_id';
    public $timestamps = true;

    protected $fillable = [
        'eti_id',
        'curriculum_id',
        'assigned_by',
        'current_project_index',
        'assigned_date',
        'start_date',
        'expected_end_date',
        'actual_end_date',
        'status',
        'completion_percentage',
        'notes',
    ];

    public function curriculum()
    {
        return $this->belongsTo(TechnologyCurriculum::class, 'curriculum_id', 'curriculum_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(ManagersAccount::class, 'assigned_by', 'manager_id');
    }
}
