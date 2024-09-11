<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    protected $table = 'majors';
    protected $fillable = [
        'name',
        'course_id',
        'description',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    use HasFactory;
}
