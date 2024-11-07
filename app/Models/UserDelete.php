<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDelete extends Model
{
    use HasFactory;

    public $timestamps = false; // Disable automatic timestamps

    protected $fillable = [
        'user_id',
        'deleted_by',
        'deleted_at',
    ];
    // In UserDelete.php
    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by', 'email'); // Adjusted to fetch by 'email' in the User model
    }
}
