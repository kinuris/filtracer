<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartialPersonalRecord extends Model
{
    protected $table = 'partial_personal_records';
    protected $fillable = [
        'user_id',
        'email_address',
        'phone_number',
        'student_id',
    ];

    use HasFactory;
}
