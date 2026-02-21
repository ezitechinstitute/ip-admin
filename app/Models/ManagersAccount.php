<?php

namespace App\Models;

// English comments: Crucial change! Extend Authenticatable instead of base Model
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Notifications\Notifiable;

class ManagersAccount extends Authenticatable
{
    use Notifiable;

    protected $table = 'manager_accounts'; 

    protected $primaryKey = 'manager_id';

    public $incrementing = true; 

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

    protected $hidden = [
        'password',
        'remember_token',
    ];
}