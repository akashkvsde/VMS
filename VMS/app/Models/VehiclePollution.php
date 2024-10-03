<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehiclePollution extends Model
{
    use HasFactory;

    protected $primaryKey = 'vehicle_pollution_id';

    protected $fillable = [
        'vehicle_id',
        'vehicle_pollution_puc_no',
        'vehicle_pollution_puc_file',
        'vehicle_pollution_start_date',
        'vehicle_pollution_end_date',
        'entry_by',
    ];


    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'vehicle_id');
    }

    public function entryBy()
    {
        return $this->belongsTo(User::class, 'entry_by', 'user_id');
    }
}
