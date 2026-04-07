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
        // New columns added
        'file_path',
        'video_url',
        'external_link',
        'thumbnail',
        'tags',
        'views',
        'downloads',
        'is_featured',
        'order_position'
    ];

    protected $casts = [
        'visibility' => 'array',
        'tags' => 'array',
        'is_featured' => 'boolean',
        'views' => 'integer',
        'downloads' => 'integer',
        'order_position' => 'integer'
    ];

    // Categories constant as per document
    const CATEGORIES = [
        'internship_rules' => 'Internship Rules',
        'coding_standards' => 'Coding Standards',
        'learning_material' => 'Learning Materials',
        'guide' => 'Guides & Tutorials',
        'video_tutorial' => 'Video Tutorials',
        'documentation' => 'Documentation'
    ];

    // Relationship with creator
    public function creator()
    {
        return $this->belongsTo(InternTable::class, 'created_by', 'id');
    }

    // Relationship with intern progress
    public function internProgress()
    {
        return $this->hasOne(InternResourceProgress::class, 'resource_id');
    }

    // Scope for active resources only
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Scope for featured resources
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // Scope to filter by category
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Scope for resources visible to interns
    public function scopeVisibleToIntern($query)
    {
        return $query->where(function($q) {
            $q->where('visibility', 'all')
              ->orWhere('visibility', 'interns')
              ->orWhereJsonContains('visibility', 'interns');
        });
    }

    // Accessor for category name
    public function getCategoryNameAttribute()
    {
        return self::CATEGORIES[$this->category] ?? ucfirst(str_replace('_', ' ', $this->category));
    }

    // Accessor for category icon
    public function getCategoryIconAttribute()
    {
        $icons = [
            'internship_rules' => 'ti ti-file-description',
            'coding_standards' => 'ti ti-code',
            'learning_material' => 'ti ti-book',
            'guide' => 'ti ti-file-text',
            'video_tutorial' => 'ti ti-video',
            'documentation' => 'ti ti-file-pdf'
        ];
        return $icons[$this->category] ?? 'ti ti-folder';
    }

    // Accessor for category color
    public function getCategoryColorAttribute()
    {
        $colors = [
            'internship_rules' => 'primary',
            'coding_standards' => 'info',
            'learning_material' => 'success',
            'guide' => 'warning',
            'video_tutorial' => 'danger',
            'documentation' => 'secondary'
        ];
        return $colors[$this->category] ?? 'dark';
    }

    // Helper to increment views
    public function incrementViews()
    {
        $this->increment('views');
    }

    // Helper to increment downloads
    public function incrementDownloads()
    {
        $this->increment('downloads');
    }
}