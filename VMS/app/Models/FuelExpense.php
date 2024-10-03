<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuelExpense extends Model
{
    use HasFactory;

    protected $primaryKey = 'fuel_expenses_id'; // Specify primary key if different from 'id'

    protected $fillable = [
        'vehicle_id',
        'driver_id',
        'filling_date',
        'fuel_station_id',
        'filling_quantity',
        'filling_amount',
        'last_km_reading',
        'filling_bill',
        'entry_by',
        'approved_by',
        'other_vehicle_no',
        'other_owner_name'
    ];

    protected $casts = [
        'filling_amount' => 'float',
        'filling_quantity' => 'float',
    ];
    

    // protected $casts = [
    //     'filling_date' => 'date', // Casts filling_date as a date
    // ];
    // Define the relationship to the Vehicle model
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'vehicle_id');
    }

    // Define the relationship to the User model for the driver
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id', 'user_id');
    }
    public function entryBy()
    {
        return $this->belongsTo(User::class, 'entry_by', 'user_id');
    }

    // Define the relationship to the FuelStation model
    public function fuelStation()
    {
        return $this->belongsTo(FuelStation::class, 'fuel_station_id', 'fuel_station_id');
    }

    public function vehicleMovements()
{
    return $this->hasMany(VehicleMovement::class, 'vehicle_id', 'vehicle_id');
}



public function vehicleOwner()
{
    return $this->vehicle->owner(); // This uses the vehicle's owner relationship
}


}
