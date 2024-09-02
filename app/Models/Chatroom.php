<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chatroom extends Model
{
    protected $table = 'chatroom';
    protected $fillable = [
        'chat_name',
        'user_id',
    ];

    use HasFactory;
}
