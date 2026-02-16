<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Notifications\Notifiable;

class AdminAccount extends Authenticatable
{
    use Notifiable;

    protected $table = 'admin_accounts';

    protected $fillable = [
        'name', 
        'email', 
        'password', 
        'loginas', 
        'image'
    ];

    public $timestamps = false;
}
