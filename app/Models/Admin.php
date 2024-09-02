<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $table = 'admins';
    protected $fillable = [
        'user_id',
        'fullname',
        'office',
        'email_address',
        'phone_number',
        'profile_picture',
        'is_deleted',
    ];

    use HasFactory;
}
