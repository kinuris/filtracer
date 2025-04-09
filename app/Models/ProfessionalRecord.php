<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ProfessionalRecord extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attachments()
    {
        return $this->hasMany(ProfessionalRecordAttachments::class, 'professional_record_id');
    }

    public function hardSkills()
    {
        return $this->hasMany(ProfessionalRecordHardSkill::class, 'professional_record_id');
    }

    public function softSkills()
    {
        return $this->hasMany(ProfessionalRecordSoftSkill::class, 'professional_record_id');
    }

    public function methods()
    {
        return $this->hasMany(ProfessionalRecordMethod::class, 'professional_record_id');
    }

    protected static function booted()
    {
        static::updated(function ($model) {
            UserAlert::query()->create([
                'title' => $model->user->getPersonalBio()->getFullname() . ' has updated their profile',
                'action' => '/user/view/' . $model->user->id,
                'content' => 'Alumni ' . $model->user->getPersonalBio()->getFullname() . ' has updated their professional profile',
                'user_id' => 1,
            ]);

            foreach ($model->user->department->admins as $admin) {
                useralert::query()->create([
                    'title' => $model->getfullname() . ' has updated their profile',
                    'action' => '/user/view/' . $model->user->id,
                    'content' => 'alumni ' . $model->getfullname() . ' has updated their professional profile',
                    'user_id' => $admin->id,
                ]);
            }
        });
    }

    use HasFactory;
}
