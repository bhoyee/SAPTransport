<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;



class Booking extends Model

{

    use HasFactory;



    protected $fillable = [

        'user_id', 

        'service_type', 

        'trip_type', 

        'status', 

        'airport_name', 

        'vehicle_type', 

        'pickup_address', 

        'dropoff_address', 

        'pickup_date', 

        'pickup_time', 

        'number_adults', 

        'number_children',

        'booking_reference', // Include booking_reference

        'return_pickup_date', 

         'return_pickup_time', 

         'created_by',

         'updated_by',



    ];

    

        // Automatically generate a booking reference

    protected static function boot()

    {

        parent::boot();



        static::creating(function ($booking) {

            $booking->booking_reference = self::generateBookingReference();

        });

    }

    

        // Function to generate a random 8-digit booking reference

    public static function generateBookingReference()

    {

        return str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);

    }



    // Define relationship with User model

    public function user()

    {

        return $this->belongsTo(User::class);

    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'email');  // Assuming 'updated_by' stores the email
    }



    

    // Define the relationship to the Payment model
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }



    // Accessor for Service Type

    public function getServiceTypeAttribute($value)

    {

        return $value === 'AirportTransfer' ? 'Airport Transfer' : 'Charter Service';

    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }


   
    

    // Accessor for Trip Type

    public function getTripTypeAttribute($value)

    {

        switch ($value) {

            case 'airport_pickup':

                return 'Airport Pickup';

            case 'airport_dropoff':

                return 'Airport Drop-Off';

            case 'oneway':

                return 'One Way';

            case 'round_trip':

                return 'Round Trip';

            default:

                return ucfirst($value); // Default fallback

        }

    }



    // Accessor for Vehicle Type

    public function getVehicleTypeAttribute($value)

    {

        switch ($value) {

            case 'car':

                return 'SAP Car';

            case 'hilux':

                return 'SAP Hilux (4 Seats)';

            case 'hiace':

                return 'SAP Hiace (14 Seats)';

            case 'coaster':

                return 'SAP Coaster (32 Seats)';

            default:

                return ucfirst($value); // Default fallback

        }

    }



    // Accessor for Airport Name

    public function getAirportNameAttribute($value)

    {

        switch ($value) {

            case 'murtala-intl':

                return 'Murtala Muhammed International Airport';

            case 'murtala-domestic-mm1':

                return 'Murtala Muhammed Domestic MM1';

            case 'murtala-domestic-mm2':

                return 'Murtala Muhammed Domestic MM2';

            default:

                return ucfirst($value); // Default fallback

        }

    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'email');  // Assuming created_by stores the creator's email
    }

    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    

}

