<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportHistory extends Model
{
    protected $table = 'import_histories';

    protected $fillable = [
        'filename',
        'data',
        'user_id',
    ];

    public function uploader() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getSizeAttribute() {
        return strlen($this->data) / 1024;
    }

    public function importGenerateds() {
        return $this->hasMany(ImportGenerated::class, 'import_history_id');
    } 

    use HasFactory;
}
