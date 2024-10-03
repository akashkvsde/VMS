<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuelStation extends Model
{
    use HasFactory;
    protected $primaryKey = 'fuel_station_id';
    protected $fillable = [
        'fuel_station_name',
        'location',
        'entry_by'
    ];
}
