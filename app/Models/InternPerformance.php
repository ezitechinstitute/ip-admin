<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InternPerformance extends Model
{
    protected $table = 'intern_performance';
    
    protected $fillable = [
        'intern_id', 'week_number', 'month_number', 'year',
        'tasks_completed', 'tasks_total', 'task_completion_rate',
        'projects_completed', 'projects_total', 'project_completion_rate',
        'average_task_score', 'attendance_percentage',
        'points_earned', 'points_total', 'grade', 'supervisor_feedback'
    ];
    
    protected $casts = [
        'task_completion_rate' => 'decimal:2',
        'project_completion_rate' => 'decimal:2',
        'average_task_score' => 'decimal:2',
        'attendance_percentage' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    public function intern()
    {
        return $this->belongsTo(InternAccount::class, 'intern_id', 'int_id');
    }
    
    // Calculate grade based on percentage
    public function calculateGrade()
    {
        $percentage = $this->task_completion_rate;
        
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B';
        if ($percentage >= 60) return 'C';
        if ($percentage >= 50) return 'D';
        return 'F';
    }
}