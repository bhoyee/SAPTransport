<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    // Specify the table if the default table name isn't "payments"
    protected $table = 'payments';

    // Allow mass assignment of the following fields
    protected $fillable = [
        'booking_id',
        'user_id',
        'amount',
        'status',
        'payment_method',
        'payment_reference',  // Ensure this field is included
        'payment_date',
    ];
    

    // Define the relationship to the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Define the relationship to the Booking model
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
