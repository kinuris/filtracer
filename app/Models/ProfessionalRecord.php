<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfessionalRecord extends Model
{
    protected $table = 'professional_records';
    protected $fillable = [
        'user_id',
        'employment_status',
        'employment_type1',
        'employment_type2',
        'monthly_salary',
        'job_title',
        'company_name',
        'industry',
        'work_location',
        'waiting_time',
    ];

    public function attachments() {
        return $this->hasMany(ProfessionalRecordAttachments::class, 'professional_record_id');
    }

    public function hardSkills() {
        return $this->hasMany(ProfessionalRecordHardSkill::class, 'professional_record_id');
    }

    public function softSkills() {
        return $this->hasMany(ProfessionalRecordSoftSkill::class, 'professional_record_id');
    }

    public function methods() {
        return $this->hasMany(ProfessionalRecordMethod::class, 'professional_record_id');
    }

    use HasFactory;
}
