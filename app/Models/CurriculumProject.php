<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurriculumProject extends Model
{
    use HasFactory;

    protected $table = 'curriculum_projects';
    protected $primaryKey = 'cp_id';
    public $timestamps = true;

    protected $fillable = [
        'curriculum_id',
        'project_title',
        'project_description',
        'sequence_order',
        'duration_weeks',
        'assigned_supervisor',
        'learning_objectives',
        'deliverables',
        'status',
    ];

    public function curriculum()
    {
        return $this->belongsTo(TechnologyCurriculum::class, 'curriculum_id', 'curriculum_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(ManagersAccount::class, 'assigned_supervisor', 'manager_id');
    }
}
