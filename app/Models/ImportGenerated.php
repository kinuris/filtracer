<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportGenerated extends Model
{
    protected $table = 'import_generateds';

    protected $fillable = ['user_id', 'default_password'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    use HasFactory;
}
