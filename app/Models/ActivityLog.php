<?php


// app/Models/ActivityLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'action', 'description', 'ip_address'];

    /**
     * Relationship to get the user associated with the activity log.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}


