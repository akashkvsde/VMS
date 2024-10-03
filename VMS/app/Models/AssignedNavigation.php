<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignedNavigation extends Model
{
    use HasFactory;


    protected $primaryKey = 'assign_nav_id';
    protected $fillable = [
        'nav_id',
        'role_id',
        'entry_by',
    ];


    public function navigation()
    {
        return $this->belongsTo(Navigation::class, 'nav_id', 'nav_id');
    }

    // Define the relationship with the UserRole model
    public function userRole()
    {
        return $this->belongsTo(Userrole::class, 'role_id', 'role_id');
    }

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class, 'entry_by', 'user_id');
    }
}
