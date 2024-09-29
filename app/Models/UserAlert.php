<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAlert extends Model
{
    protected $table = 'user_alerts';
    protected $fillable = [
        'title',
        'action',
        'content',
        'user_id',
        'is_read',
    ];

    use HasFactory;
}
