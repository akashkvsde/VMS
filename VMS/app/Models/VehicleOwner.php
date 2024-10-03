<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleOwner extends Model
{
    use HasFactory; // Ensure this is the correct table name
    protected $primaryKey = 'vehicle_owner_id'; // Primary key should be set
  protected $fillable = [
        'vehicle_owner_name',
        'organization_id',
        'vehicle_owner_mobile_no_1',
        'vehicle_owner_mobile_no_2',
        'entry_by'
    ];

    public function setVehicleOwnerNameAttribute($value)
    {
        $this->attributes['vehicle_owner_name'] = ucwords(strtolower($value));
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'organization_id');
    }

    // Define the relationship to the User model for the entry_by field
    public function entryBy()
    {
        return $this->belongsTo(User::class, 'entry_by', 'user_id')
                    ->select(['user_id', 'user_name']);
    }
}
