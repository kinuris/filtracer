<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatabaseSnapshot extends Model
{
    protected $table = 'database_snapshots';

    protected $fillable = ['sql', 'files_path'];

    public function getFileSize()
    {
        $filePath = storage_path($this->files_path);
        if (file_exists($filePath)) {
            return filesize($filePath);
        }

        return 0;
    }

    public function getSqlSize()
    {
        return strlen($this->sql);
    }

    use HasFactory;
}
