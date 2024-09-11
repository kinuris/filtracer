<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfessionalRecordSoftSkill extends Model
{
    protected $table = 'professional_record_soft_skills';
    protected $fillable = [
        'professional_record_id',
        'skill',
    ];

    use HasFactory;
}
