<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InternResourceProgress extends Model
{
    use HasFactory;

    protected $table = 'intern_resource_progress';

    protected $fillable = [
        'intern_id',
        'resource_id',
        'is_completed',
        'completed_at',
        'time_spent'
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'time_spent' => 'integer'
    ];

    // Relationship with intern
    public function intern()
    {
        return $this->belongsTo(InternTable::class, 'intern_id', 'id');
    }

    // Relationship with knowledge base resource
    public function resource()
    {
        return $this->belongsTo(KnowledgeBase::class, 'resource_id');
    }

    // Scope for completed resources
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    // Scope for pending resources
    public function scopePending($query)
    {
        return $query->where('is_completed', false);
    }
}