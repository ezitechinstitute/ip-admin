<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $table = 'chat_messages';
    
    protected $fillable = [
        'chat_id',
        'sender_type',
        'sender_id',
        'message'
    ];

    // Link back to the chat room
    public function chat()
    {
        return $this->belongsTo(ProjectChat::class, 'chat_id', 'id');
    }

    // Polymorphic relationship to get the sender (Admin, Manager, or Intern)
    public function sender()
    {
        return $this->morphTo();
    }
}