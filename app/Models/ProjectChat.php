<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectChat extends Model
{
    protected $table = 'chats'; // Repurposed table
    protected $fillable = ['project_id', 'created_by', 'chat_type'];

    public function project() {
        return $this->belongsTo(InternProject::class, 'project_id', 'project_id');
    }

    public function messages() {
        return $this->hasMany(ChatMessage::class, 'chat_id', 'id');
    }

    // New: Participants logic
    public function participants() {
        return $this->hasMany(ChatParticipant::class, 'chat_id');
    }
}