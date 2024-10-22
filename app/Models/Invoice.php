<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'generated_by',
        'invoice_number',
        'invoice_date',
        'amount',
        'status',
        'file_path',
    ];

    // Define the relationship with the Booking model
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    // Define the relationship with the User model for the person who generated the invoice
    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
    public function payment()
    {
        return $this->hasOne(Payment::class, 'booking_id', 'booking_id'); // Link payments through booking_id
    }
    

}
