<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternProjectProgress extends Model
{
    use HasFactory;

    protected $table = 'intern_project_progress';
    protected $primaryKey = 'progress_id';
    public $timestamps = false;

    protected $fillable = [
        'assignment_id',
        'cp_id',
        'start_date',
        'end_date',
        'status',
        'progress_percentage',
        'supervisor_id',
        'supervisor_remarks',
        'marks_obtained',
        'completed_at',
    ];

    protected $dates = ['start_date', 'end_date', 'completed_at'];

    public function supervisor()
    {
        return $this->belongsTo(ManagersAccount::class, 'supervisor_id', 'manager_id');
    }

    public function assignment()
    {
        return $this->belongsTo(InternCurriculumAssignment::class, 'assignment_id', 'assignment_id');
    }
}
