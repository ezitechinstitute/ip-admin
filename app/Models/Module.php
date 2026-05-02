<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    // This allows Laravel to save data into these columns
    protected $fillable = [
        'name',
        'slug',
        'role_access'
    ];
}