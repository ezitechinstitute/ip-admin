<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManagersAccount extends Model
{
    protected $table = 'manager_accounts';

    protected $primaryKey = 'manager_id';

    protected $fillable = [
        'eti_id',
        'image',
        'name',
        'email',
        'contact',
        'join_date',
        'password',
        'comission',
        'department',
        'status',
        'loginas',
        'emergency_contact',
    ];
}
