<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     * 
     * 
     */

     protected $primaryKey = 'user_id';
    protected $fillable = [
        'role_id',
        'user_organization_id',
        'user_login_id',
        'user_name',
        'user_1st_mobile_no',
        'user_2nd_mobile_no',
        'user_wp_no',
        'doj',
        'dob',
        'gender',
        'aadhar_no',
        'address',
        'photo',
        'status',
        'entry_by',
        'org_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public function assignedRole()
    {
        return $this->hasOne(AssignedRole::class, 'user_id', 'user_id');
    }
    public function assignedRoles()
    {
        return $this->hasMany(AssignedRole::class, 'user_id', 'user_id');
    }

    public function role()
    {
        return $this->belongsTo(Userrole::class, 'role_id', 'role_id');
    }


    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'organization_id');
    }
    public function organizationByUserId()
    {
        return $this->belongsTo(Organization::class, 'user_organization_id', 'organization_id');
    }
    
    

    public function vehicleMaintenances()
    {
        return $this->hasMany(VehicleMaintenance::class, 'driver_id');
    }


    public function driverDetails()
    {
        return $this->belongsTo(DriverDetail::class,'user_id', 'user_id');
    }





    
}
