<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortalNotification extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'is_read'
    ];
}
