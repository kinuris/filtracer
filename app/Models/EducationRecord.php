<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class EducationRecord extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function major()
    {
        return $this->belongsTo(Major::class, 'major_id');
    }

    public function getCourse()
    {
        return Course::find($this->course_id);
    }

    protected static function booted()
    {
        static::updated(function ($model) {
            UserAlert::query()->create([
                'title' => $model->user->getPersonalBio()->getFullname() . ' has updated their rofile',
                'action' => '/user/view' . $model->user->id,
                'content' => 'Alumni ' . $model->user->getPersonalBio()->getFullname() . ' has updated their educational profile',
                'user_id' => 1,
            ]);
        });
    }

    use HasFactory;
}
