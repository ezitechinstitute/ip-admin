<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupervisorLeave extends Model
{
    protected $table = 'supervisor_leaves';

    protected $primaryKey = 'leave_id';

    protected $fillable = [
        'supervisor_id',
        'name',
        'email',
        'from_date',
        'to_date',
        'reason',
        'days',
        'leave_status'
    ];
}
