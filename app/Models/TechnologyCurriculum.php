<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnologyCurriculum extends Model
{
    use HasFactory;

    protected $table = 'technology_curriculum';
    protected $primaryKey = 'curriculum_id';
    public $timestamps = true;

    protected $fillable = [
        'tech_id',
        'curriculum_name',
        'description',
        'total_projects',
        'total_duration_weeks',
        'status',
        'created_by',
    ];

    public function technology()
    {
        return $this->belongsTo(Technology::class, 'tech_id', 'tech_id');
    }

    public function projects()
    {
        return $this->hasMany(CurriculumProject::class, 'curriculum_id', 'curriculum_id')->orderBy('sequence_order');
    }

    public function creator()
    {
        return $this->belongsTo(ManagersAccount::class, 'created_by', 'manager_id');
    }
}
