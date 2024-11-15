<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $table = 'contact';


    protected $fillable = [
        'ticket_num', 'status', 'department', 'priority', 'attachment',
        'fullname', 'email_address', 'phone_number', 'subject', 'message', 'category' // added 'category'
    ];
    
    

    // Relationship with TicketReply
    public function replies()
    {
        return $this->hasMany(TicketReply::class, 'ticket_id');
    }

    // Relationship with User based on email address
    public function user()
    {
        return $this->belongsTo(User::class, 'email_address', 'email');
    }
}
