<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedPost extends Model
{
    protected $table = 'saved_posts';
    protected $fillable = ['user_id', 'post_id'];

    use HasFactory;
}
