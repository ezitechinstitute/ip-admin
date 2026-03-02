<?php

namespace App\Models;

use App\Models\ManagersAccount;
use Illuminate\Database\Eloquent\Model;

class OfferLetterTemplate extends Model
{
    protected $fillable = [
        'title',
        'content',
        'manager_id',
        'can_use_other_template',
        'status',
        'is_deleted',
    ];


    protected $casts = [
        'content' => 'array', 
        'can_use_other_template' => 'boolean',
        'status' => 'boolean',
        'is_deleted' => 'boolean',
    ];


    public function manager()
    {
        return $this->belongsTo(ManagersAccount::class, 'manager_id', 'manager_id');
    }
}
