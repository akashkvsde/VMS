<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Garage extends Model
{
    use HasFactory;

    protected $primaryKey = 'garage_id';

    protected $fillable = [
        'garage_name',
        'garage_owner',
        'location',
        'contact_person',
        'contact_no',
        'entry_by'
    ];


    public function vehicleMaintenances()
    {
        return $this->hasMany(VehicleMaintenance::class, 'maintenance_service_center_name', 'garage_id');
    }
}
