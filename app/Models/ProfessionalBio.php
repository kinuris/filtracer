<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfessionalBio extends Model
{
    protected $table = 'professional_bio';
    protected $fillable = [
        'employment_status',
        'business_name',
        'business_type',
        'business_role',
        'contact_number',
        'employment_type1',
        'employment_type2',
        'monthly_salary',
        'job_title',
        'company_name',
        'industry',
        'work_location',
        'job_search_methods',
        'waiting_time',
        'hard_skills',
        'other_hard_skills',
        'soft_skills',
        'other_soft_skills',
    ];

    use HasFactory;
}
