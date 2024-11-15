<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Message extends Model
{
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'subject',
        'message',
        'status'
    ];

    // Define the sender relationship
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Define the recipients relationship using a pivot table
    public function recipients(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'message_recipients', 'message_id', 'user_id')
                    ->withPivot('status');
    }
}
