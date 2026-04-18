<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectChat extends Model
{
    protected $table = 'project_chats';
    protected $fillable = ['project_id'];

    // Link back to your existing InternProject
    public function project()
    {
        return $this->belongsTo(InternProject::class, 'project_id', 'project_id');
    }

    // A chat has many messages
    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'chat_id', 'id');
    }
}