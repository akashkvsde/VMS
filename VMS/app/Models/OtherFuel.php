<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtherFuel extends Model
{
    use HasFactory;

    protected $primaryKey='other_fuel_id';
    protected $fillable=['other_vehicle_id','amount','quantity','entry_by','filling_station','last_km_reading','approved_by','filling_date','filling_bill','organization_id'];

    public function otherVehicle()
    {
        return $this->belongsTo(OtherVehicle::class, 'other_vehicle_id', 'other_vehicle_id');
    }

    public function fillingstations()
    {
        return $this->belongsTo(FuelStation::class, 'filling_station', 'fuel_station_id');
    }
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'organization_id');
    }
    public function entry_by()
    {
        return $this->belongsTo(User::class, 'entry_by', 'user_id');
    }
}
