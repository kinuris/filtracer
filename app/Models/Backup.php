<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Backup extends Model
{
    protected $table = 'backups';

    protected $fillable = [
        'student_id',
        'backup_id',
        'partialPersonalBio',
        'personalBio',
        'educationalBios',
        'professionalBio',
        'professionalBioFiles',
        'professionalBioSoftSkills',
        'professionalBioHardSkills',
    ];

    public static function generateUniqueBackupId()
    {
        do {
            $backupId = 'BP-' . str_pad(mt_rand(0, 999), 3, '0', STR_PAD_LEFT);
        } while (self::where('backup_id', $backupId)->exists());

        return $backupId;
    }
    
    use HasFactory;
}
