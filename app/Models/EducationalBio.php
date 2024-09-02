<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EducationalBio extends Model
{
    protected $table = 'educational_bio';
    protected $fillable = [
        'school_name',
        'other_school',
        'school_location',
        'degree_type',
        'other_type',
        'course_id',
        'other_course',
        'major_id',
        'other_major',
        'batch',
    ];

    public function getCourse() {
        return Course::query()->find($this->course_id);
    }

    use HasFactory;
}
