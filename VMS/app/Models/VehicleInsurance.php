<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleInsurance extends Model
{
    use HasFactory;

    protected $primaryKey = 'vehicle_insurance_id';

    protected $fillable = [
        'vehicle_id',
        'insurance_company_name',
        'vehicle_insurance_agent_name',
        'vehicle_insurance_agent_mobile_no',
        'vehicle_insurance_no',
        'vehicle_insurance_file',
        'vehicle_insurance_start_date',
        'vehicle_insurance_end_date',
        'entry_by',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'vehicle_id');
    }
}
