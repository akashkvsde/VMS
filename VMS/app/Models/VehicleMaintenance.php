<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleMaintenance extends Model
{
    use HasFactory;

    protected $primaryKey = 'vehicle_maintenance_id';

    protected $fillable = [
        'vehicle_id',
        'driver_id',
        'manager_id',
        'authority_id',
        'maintenance_problems_id',
        'maintenance_problems_other_details',
        'maintenance_start_fuel_level',
        'maintenance_end_fuel_level',
        'maintenance_amount',
        'maintenance_service_center_name',
        'maintenance_start_km_reading_by_manager',
        'maintenance_end_km_reading_by_manager',
        'maintenance_start_date',
        'maintenance_start_time',
        'maintenance_end_date',
        'maintenance_end_time',
        'maintenance_service_center_recept_file',
        'maintenance_approve_status',
        'maintenance_status',
        'entry_by',
        'exp_amt',
    ];


    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    // Relationship to User model (for manager)
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    // Relationship to User model (for driver)
    public function driver()
    {
        return $this->belongsTo(User::class,'driver_id');
    }

    // Relationship to VehicleProblem model


    // Relationship to User model (for admin)
    public function authority()
    {
        return $this->belongsTo(User::class, 'authority_id');
    }

    // Relationship to User model (for entry user)
    public function entryBy()
    {
        return $this->belongsTo(User::class, 'entry_by');
    }
    public function garage()
    {
        return $this->belongsTo(Garage::class, 'maintenance_service_center_name', 'garage_id');
    }
    
    
   
       public function maintenanceProblem()
    {
        return $this->belongsTo(VehicleProblem::class, 'maintenance_problems_id', 'vehicle_problems_id');
    }
}
