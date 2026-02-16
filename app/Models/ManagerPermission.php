<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManagerPermission extends Model
{
    protected $table = 'manager_permissions';

    protected $primaryKey = 'manager_p_id';
    public $timestamps = false;
    protected $fillable = [
        'manager_id',
        'tech_id',
        'interview_type'
    ];
}
