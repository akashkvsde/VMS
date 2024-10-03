<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehiclesCategory extends Model
{
    use HasFactory;

    protected $primaryKey = 'vehicle_category_id';

    protected $fillable = [
        'vehicle_category_name',
        'entry_by',
    ];


    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'vehicle_category_id');
    }
    public function users()
    {
        return $this->belongsTo(User::class, 'entry_by', 'user_id')
                    ->select(['user_id', 'user_name']);
    }
}
