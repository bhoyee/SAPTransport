<?php

// app/Models/Message.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = ['sender_id', 'receiver_id', 'subject', 'message', 'status'];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
