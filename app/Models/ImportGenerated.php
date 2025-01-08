<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportGenerated extends Model
{
    protected $table = 'import_generateds';

    protected $fillable = ['user_id', 'default_password', 'import_history_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function importHistory() {
        return $this->belongsTo(ImportHistory::class, 'import_history_id');
    }

    use HasFactory;
}
