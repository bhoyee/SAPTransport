<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalkinInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name', 'email', 'phone', 'issue_date', 'due_date', 'due_amount',
        'status', 'service_type', 'pickup_date', 'pickup_time', 'pickup_address',
        'dropoff_address', 'amount_paid', 'created_by', 'updated_by'
    ];
}
