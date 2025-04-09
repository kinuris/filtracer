<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $table = 'chat_messages';
    protected $fillable = [
        'type',
        'content',
        'sender_id',
        'chat_group_id'
    ];

    public function getFileName()
    {
        if ($this->type === 'text') {
            throw new \Exception('Not a file or image');
        }

        return base64_decode(explode('.', $this->content)[1]);
    }

    public function isFile()
    {
        if ($this->type === 'text') {
            return false;
        }

        $file = mime_content_type('storage/chat/files/' . $this->content);

        return $file  == 'application/pdf';
    }


    public function isImage()
    {
        if ($this->type === 'text') {
            return false;
        }

        $file = mime_content_type('storage/chat/files/' . $this->content);

        return $file == 'image/png' || $file == 'image/jpg' || $file == 'image/jpeg';
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    use HasFactory;
}
