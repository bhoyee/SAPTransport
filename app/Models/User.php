<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasRoles, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'gender',
        'status',   // Keep for tracking user status
        'created_by',
    ];

    // protected $dates = ['deleted_at'];  // Track soft deletes

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Override the default sendEmailVerificationNotification method to add logging.
     */
    public function sendEmailVerificationNotification()
    {
        Log::info('Sending email verification notification to ' . $this->email);

        // Send the default Laravel email verification notification
        $this->notify(new \Illuminate\Auth\Notifications\VerifyEmail);
    }

    /**
     * Example relationship: A user can have many bookings
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    
    public function payments()
    {
        return $this->hasMany(Payment::class, 'user_id');
    }
    
    /**
     * Accessor for gender: Capitalize the first letter.
     */
    public function getGenderAttribute($value)
    {
        return ucfirst($value);
    }

    /**
     * Mutator for gender: Store gender as lowercase.
     */
    public function setGenderAttribute($value)
    {
        $this->attributes['gender'] = strtolower($value);
    }

    public function creatorByEmail()
    {
        return $this->belongsTo(User::class, 'created_by', 'email');
    }
    

}
