<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtherVehicle extends Model
{
    use HasFactory;
    protected $primaryKey='other_vehicle_id';
    protected $fillable=['other_vehicle_number','other_owner_name','entry_by','organization_id'];


    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'organization_id');
    }
    
}
