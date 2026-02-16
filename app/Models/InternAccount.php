<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InternAccount extends Model
{
    protected $table = 'intern_accounts';

    public $timestamps = false;

    protected $fillable = [
    'int_id',
    'eti_id',
    'name',
    'email',
    'phone',
    'password',
    'int_technology',
    'start_date',
    'int_status',
    'review',
    'reset_token',
];

}
