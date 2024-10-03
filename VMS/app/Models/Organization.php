<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $primaryKey = 'organization_id';

    protected $fillable = [
        'organization_name',
        'organization_location',
        'organization_inclusion_date',
        'organization_status',
        'entry_by',
    ];
}
