<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InternFeedback extends Model
{
    use HasFactory;

    // Specify the table name (if it doesn't follow Laravel's pluralization)
    protected $table = 'intern_feedback';

    // Primary key (optional if 'id')
    protected $primaryKey = 'id';

    // Mass assignable fields
    protected $fillable = [
        'eti_id',
        'feedback_text',
        'status',
        'resolved_at',
    ];

    // Optional: Cast resolved_at to datetime
    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    // Disable timestamps if your table already has created_at
    public $timestamps = false; // will handle created_at automatically
}
