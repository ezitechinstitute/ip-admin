<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $table = 'intern_leaves';

    protected $primaryKey = 'leave_id';

    protected $fillable = [
        'eti_id',
        'name',
        'email',
        'from_date',
        'to_date',
        'reason',
        'technology',
        'intern_type',
        'days',
        'leave_status'
    ];
}
