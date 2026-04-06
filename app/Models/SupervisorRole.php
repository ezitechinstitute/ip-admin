<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupervisorRole extends Model
{
    protected $table = 'supervisor_roles';

    protected $fillable = [
        'supervisor_id',
        'permission_key'
    ];
}