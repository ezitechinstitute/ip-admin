<?php

namespace App\Models;

// English comments: Crucial change! Extend Authenticatable instead of base Model
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Notifications\Notifiable;

class InternAccount extends Authenticatable
{
    use Notifiable;

    protected $table = 'intern_accounts';

    protected $primaryKey = 'int_id';

    public $incrementing = true;

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
        'supervisor_id',
        'manager_id',
        'profile_photo',
        'city',
        'university',
        'bio',
        'skills',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public $timestamps = false;

    // Relationships
    public function supervisor()
    {
        return $this->belongsTo(ManagersAccount::class, 'supervisor_id', 'manager_id');
    }

    public function manager()
    {
        return $this->belongsTo(ManagersAccount::class, 'manager_id', 'manager_id');
    }
}