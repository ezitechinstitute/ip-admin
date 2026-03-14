<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupervisorAttendance extends Model
{
    protected $table = 'supervisor_attendance';

    protected $fillable = [
        'supervisor_id',
        'date',
        'check_in',
        'check_out'
    ];
}