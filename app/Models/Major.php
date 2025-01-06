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

    public function departmentThroughCourse()
    {
        return $this->hasOneThrough(Department::class, Course::class, 'id', 'id', 'course_id', 'department_id');
    }
    use HasFactory;
}
