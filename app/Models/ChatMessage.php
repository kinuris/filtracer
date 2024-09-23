<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $table = 'chat_messages';
    protected $fillable = [
        'type',
        'content',
        'sender_id',
        'chat_group_id'
    ];

    public function sender() {
        return $this->belongsTo(User::class, 'sender_id');
    }

    use HasFactory;
}
