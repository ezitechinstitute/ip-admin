<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InternTimeline extends Model
{
    protected $table = 'intern_timeline';
    
    protected $fillable = [
        'intern_id', 'title', 'description', 'event_date', 'type',
        'color', 'completed', 'link', 'reference_id', 'reference_type'
    ];
    
    protected $casts = [
        'event_date' => 'date',
        'completed' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    public function intern()
    {
        return $this->belongsTo(InternAccount::class, 'intern_id', 'int_id');
    }
}