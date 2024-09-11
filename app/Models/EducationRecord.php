<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EducationRecord extends Model
{
    protected $table = 'education_records';
    protected $fillable = [
        'user_id',
        'school',
        'school_location', 
        'degree_type', 
        'course_id', 
        'major_id', 
        'start', 
        'end',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getCourse() {
        return Course::find($this->course_id);
    }

    use HasFactory;
}
