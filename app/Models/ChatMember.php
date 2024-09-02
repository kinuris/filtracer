<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMember extends Model
{
    protected $table = 'chat_members';
    protected $fillable = [
        'chatroom_id',
        'user_id',
    ];

    use HasFactory;
}
