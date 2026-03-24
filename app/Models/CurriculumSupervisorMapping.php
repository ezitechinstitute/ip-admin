<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurriculumSupervisorMapping extends Model
{
    use HasFactory;

    protected $table = 'curriculum_supervisor_mapping';
    protected $primaryKey = 'mapping_id';
    public $timestamps = true;

    protected $fillable = [
        'cp_id',
        'supervisor_id',
        'assigned_date',
        'assigned_by',
        'is_primary',
        'status',
    ];

    public function supervisor()
    {
        return $this->belongsTo(ManagersAccount::class, 'supervisor_id', 'manager_id');
    }

    public function project()
    {
        return $this->belongsTo(CurriculumProject::class, 'cp_id', 'cp_id');
    }
}
