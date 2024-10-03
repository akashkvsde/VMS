<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Navigation extends Model
{
    use HasFactory;
    protected $primaryKey = 'nav_id';
    protected $fillable = [
        'nav_name',
        'nav_url',
        'nav_title',
        'nav_icon',
        'entry_by',
    ];
    
    public function assignedNavigations()
    {
        return $this->hasMany(AssignedNavigation::class, 'nav_id', 'nav_id');
    }
}
