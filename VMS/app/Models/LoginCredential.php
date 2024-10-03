<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginCredential extends Model
{
    use HasFactory;

    protected $primaryKey = 'login_credential_id';

    protected $fillable = [
        'user_id',
        'login_id',
        'user_password',
        'is_active',
        'entry_by',
    ];
}
