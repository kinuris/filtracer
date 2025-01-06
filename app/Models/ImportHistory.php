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

    use HasFactory;
}
