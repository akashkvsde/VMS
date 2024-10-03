<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleMovement extends Model
{
    use HasFactory;
    protected $primaryKey = 'vehicle_movement_id';

    protected $fillable = [
        'vehicle_id',
        'driver_id',
        'manager_id',
        'movement_start_from',
        'movement_destination',
        'purpose_of_visit',
        'purpose',
        'taken_by',
        'movement_start_date',
        'movement_start_time',
        'movement_end_date',
        'movement_end_time',
        'movement_start_km_reading_by_manager',
        'movement_end_km_reading_by_manager',
        'movement_start_km_reading_by_driver',
        'movement_end_km_reading_by_driver',
        'movement_distance_covered',
        'movement_status',
        'entry_by',
        'movement_start_time_by_driver',
        'fuel_expenses_id'
    ];

    public function vehicle()
{
    return $this->belongsTo(Vehicle::class, 'vehicle_id');
}

public function driver()
{
    return $this->belongsTo(User::class, 'driver_id');
}

public function manager()
{
    return $this->belongsTo(User::class, 'manager_id');
}

public function entryBy()
{
    return $this->belongsTo(User::class, 'entry_by');
}




}
