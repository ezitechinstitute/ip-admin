<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupervisorPermission extends Model
{
    protected $table = 'supervisor_permissions';

    protected $primaryKey = 'sup_p_id';
    // public $timestamps = false;
    protected $fillable = [
        'manager_id',
        'tech_id',
        'internship_type'
    ];
}
