<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverDetail extends Model
{
    use HasFactory;
    protected $primaryKey = 'driver_details_id';
    protected $fillable = [
        'user_id',
        'dl_no',
        'dl_file',
        'entry_by',
    ];
}
