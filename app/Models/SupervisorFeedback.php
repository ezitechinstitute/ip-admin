<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupervisorFeedback extends Model
{
    //
    protected $fillable = [
    'eti_id',
    'supervisor_id',
    'score',
    'remarks',
    'improvement_suggestions'
];
}
