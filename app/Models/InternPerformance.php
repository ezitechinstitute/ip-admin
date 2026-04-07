<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InternPerformance extends Model
{
    protected $table = 'intern_performance';

    protected $fillable = [
        'intern_id',
        'name',
        'email',
        'technology',
        'task_completion',
        'deadline',
        'quality',
        'attendance',
        'overall'
    ];
}