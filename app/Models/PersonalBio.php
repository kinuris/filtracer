<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalBio extends Model
{
    protected $table = 'personal_bio';
    protected $fillable = [
        'student_id',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'age',
        'gender',
        'birthdate',
        'civil_status',
        'permanent_address',
        'current_address',
        'email_address',
        'phone_number',
        'social_link',
        'profile_picture',
        'status'
    ];
    
    public function getFullname() {
        if ($this->middle_name) {
            return $this->first_name . ' ' . $this->middle_name[0] . '. ' . $this->last_name;
        } else {
            return $this->first_name . ' ' . $this->last_name;
        }
    }

    public function getAge() {
        // TODO: Change this garbage 

        return date_diff(date_create($this->birthdate), date_create('now'))->y;
    }

    public function casts() {
        return [
            'birthdate' => 'date',
        ];
    }

    use HasFactory;
}
