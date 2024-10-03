<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $primaryKey = 'vehicle_id';
    protected $fillable = [
        'vehicle_category_id',
        'vehicle_owner_id',
        'vehicle_name',
        'vehicle_model',
        'vehicle_purchase_date',
        'vehicle_rc_no',
        'vehicle_rc_file',
        'vehicle_fastag_no',
        'vehicle_rto_no',
        'vehicle_fitness_end',
        'vehicle_chassis_no',
        'vehicle_engine_no',
        'vehicle_fuel_type',
        'is_active',
        'entry_by',
        'organization_id', // Ensure this field exists if you're filtering by it
    ];

    public function category()
    {
        return $this->belongsTo(VehiclesCategory::class, 'vehicle_category_id');
    }

    public function owner()
    {
        return $this->belongsTo(VehicleOwner::class, 'vehicle_owner_id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function maintenances()
    {
        return $this->hasMany(VehicleMaintenance::class, 'vehicle_id', 'vehicle_id');
    }
}
