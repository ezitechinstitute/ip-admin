<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Intern extends Model
{
    protected $table = 'intern_table';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'email',
        'city',
        'phone',
        'cnic',
        'gender',
        'image',
        'join_date',
        'birth_date',
        'university',
        'country',
        'interview_type',
        'technology',
        'duration',
        'status',
        'intern_type',
        'interview_date',
        'interview_time',
        'created_at'

    ];
}
