<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeLeave extends Model

{
    protected $table = 'employee_leaves';

    protected $primaryKey = 'leave_id';
     public $timestamps = true;

    protected $fillable = [
        'employee_id',
        'name',
        'email',
        'from_date',
        'to_date',
        'reason',
        'days',
        'leave_status'
    ];
}
