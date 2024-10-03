<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Overtime extends Model
{
    use HasFactory;
    protected $primaryKey='overtime_id';
    protected $fillable = [
        'driver_id',
        'start_date',
        'end_date',
        'check_in_time',
        'check_out_time',
        'entry_by',
    ];


    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id', 'user_id');
    }

    /**
     * Get the user who created the overtime.
     */
    public function entryBy()
    {
        return $this->belongsTo(User::class, 'entry_by', 'user_id');
    }
}
