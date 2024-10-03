<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleProblem extends Model
{
    use HasFactory;

    protected $primaryKey = 'vehicle_problems_id';


    protected $fillable = [
        'vehicle_problems_name',
        'entry_by',
    ];
}
