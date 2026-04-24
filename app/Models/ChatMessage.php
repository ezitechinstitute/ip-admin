<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    // 🔥 FIX: Point to the correct table name
    protected $table = 'chat_messages'; 
    
    protected $fillable = [
        'chat_id',
        'sender_type',
        'sender_id',
        'message'
    ];

    // Keep your existing relationships
    public function chat() {
        return $this->belongsTo(ProjectChat::class, 'chat_id', 'id');
    }

    public function sender()
    {
        return $this->morphTo();
    }
}