<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartialPersonalRecord extends Model
{
    protected $table = 'partial_personal_records';
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'user_id',
        'email_address',
        'phone_number',
        'student_id',
    ];

    public function getFullnameAttribute()
    {
        return trim("{$this->first_name} " . ($this->middle_name ? "{$this->middle_name[0]}. " : "") . "{$this->last_name} {$this->suffix}");
    }

    public function philSMSNum()
    {
        return '63' . substr($this->phone_number, 1);
    }

    use HasFactory;
}
