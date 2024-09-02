<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'events';
    protected $fillable = [
        'title',
        'content',
        'schedule',
        'duration',
        'status',
        'event_details_link',
        'is_deleted',
    ];

    use HasFactory;
}
