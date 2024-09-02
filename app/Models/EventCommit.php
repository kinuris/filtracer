<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventCommit extends Model
{
    protected $table = 'event_commits';
    protected $fillable = [
        'event_id',
        'user_id',
    ];

    use HasFactory;
}
