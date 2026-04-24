<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatParticipant extends Model
{
    protected $fillable = ['chat_id', 'user_id'];

    public function chat() {
        return $this->belongsTo(ProjectChat::class);
    }
}