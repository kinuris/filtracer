<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatAssociation extends Model
{
    protected $table = 'chat_associations';
    protected $fillable = [
        'chat_group_id',
        'user_id',
        'status',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    use HasFactory;
}
