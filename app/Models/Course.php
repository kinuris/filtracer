<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $table = 'courses';
    protected $fillable = [
        'name',
        'department_id'
    ];

    public function educations() {
        return $this->hasMany(EducationRecord::class, 'course_id');
    }

    public function department() {
        return $this->belongsTo(Department::class, 'department_id');
    }

    use HasFactory;
}
