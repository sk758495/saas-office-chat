<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'chat_id', 'group_id', 'sender_id', 'message', 'file_path', 
        'file_name', 'file_type', 'type', 'is_read', 'read_at',
        'folder_contents', 'is_folder', 'original_folder_name'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'folder_contents' => 'array',
        'is_folder' => 'boolean',
    ];

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}