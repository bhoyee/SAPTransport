<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications'; // Specify the table name

    // Mass assignable attributes
    protected $fillable = [
        'user_id', 'message', 'type', 'status','related_user_name'
    ];

    // Relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope to get unread notifications
    public function scopeUnread($query)
    {
        return $query->where('status', 'unread');
    }

    // Mark a notification as read
    public function markAsRead()
    {
        $this->status = 'read';
        $this->save();
    }
}
