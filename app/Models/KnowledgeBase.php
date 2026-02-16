<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeBase extends Model
{
    protected $fillable = [
        'title',
        'category',
        'content',
        'visibility',
        'status',
        'created_by',
    ];

    protected $casts = [
        'visibility' => 'array',
    ];
}
