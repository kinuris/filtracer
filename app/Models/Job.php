<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = 'jobs';
    protected $fillable = [
        'job_title',
        'employment_type',
        'company',
        'work_location',
        'description',
        'source_link',
        'status',
        'user_id',
        'is_deleted'
    ];

    use HasFactory;
}
