<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';
    protected $fillable = [
        'title',
        'content',
        'source',
        'post_category',
        'post_status',
        'attached_image',
        'status',
        'user_id',
    ];

    public function isPinnedBy(User $user) {
        return $user->pinnedPosts()->where('post_id', '=', $this->id)->exists();
    }

    public function isSavedBy(User $user) {
        return $user->savedPosts()->where('post_id', '=', $this->id)->exists();
    }

    public function creator() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function image() {
        return asset('storage/post/attachments/' . $this->attached_image);
    }

    use HasFactory;
}
