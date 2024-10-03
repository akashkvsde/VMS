<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Userrole extends Model
{
    use HasFactory;

    protected $primaryKey = 'role_id';

    // Add fillable property
    protected $fillable = [
        'role_name',
        'entry_by',
    ];


    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }


    public function assignedNavigations()
    {
        return $this->hasMany(AssignedNavigation::class, 'role_id', 'role_id');
    }

    public function assignedRoles()
    {
        return $this->hasMany(AssignedRole::class, 'role_id', 'role_id');
    }
}
