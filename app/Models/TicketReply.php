<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Contact;
use App\Models\User;

class TicketReply extends Model
{
    use HasFactory;

    protected $fillable = ['ticket_id', 'user_id', 'message'];

    // Relationship with the ticket (Contact)
    public function ticket()
    {
        return $this->belongsTo(Contact::class, 'ticket_id');
    }

    // Relationship with the user (who replied)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
