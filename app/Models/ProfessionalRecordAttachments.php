<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfessionalRecordAttachments extends Model
{
    protected $table = 'professional_record_attachments';
    protected $fillable = [
        'link',
        'type',
        'name',
        'professional_record_id',
    ];

    use HasFactory;
}
