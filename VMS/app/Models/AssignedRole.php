<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignedRole extends Model
{
    use HasFactory;

    protected $primaryKey = 'assigned_role_id';

    protected $fillable = [
        'user_id',
        'role_id',
        'entry_by',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function role()
    {
        return $this->belongsTo(Userrole::class, 'role_id', 'role_id');
    }
}
