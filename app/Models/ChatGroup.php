<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ChatGroup extends Model
{
    protected $table = 'chat_groups';
    protected $fillable = [
        'internal_id',
        'name',
        'image_link',
        'creator_id',
    ];

    public static function genInternalNoCollision()
    {
        while (true) {
            $i = random_int(0, 999999);
            $internal_id = '#' . $i;
            if (ChatGroup::query()->where('internal_id', '=', $internal_id)->count() === 0) {
                return $internal_id;
            }
        }
    }

    public function getNameAttribute()
    {
        $users = $this->users();
        if ($users->count() === 2) {
            $user = $users->firstWhere('users.id', '!=', Auth::id());

            return $user->name;
        }

        return $this->attributes['name'];
    }

    public function initiateLink()
    {
        $users = $this->users();
        if ($users->count() === 2) {
            $user = $users->firstWhere('users.id', '!=', Auth::id());

            return $user->id;
        }

        return urlencode($this->attributes['internal_id']);
    }

    public function image()
    {
        $users = $this->users();
        if ($users->count() === 2) {
            $user = $users->firstWhere('users.id', '!=', Auth::id());

            return $user->image();
        }

        if ($this->image_link) {
            return asset('storage/chat/images/' . $this->image_link);
        }

        return fake()->imageUrl();
    }

    public function users()
    {
        return $this->hasManyThrough(User::class, ChatAssociation::class, 'chat_group_id', 'id', 'id', 'user_id');
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'chat_group_id');
    }

    use HasFactory;
}
