<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfessionalRecordMethod extends Model
{
    protected $table = 'professional_record_methods';
    protected $fillable = [
        'professional_record_id',
        'method',
    ];

    use HasFactory;
}
