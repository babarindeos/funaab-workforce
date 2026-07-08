<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'designation',
        'phone',
        'avatar',
        'home_address'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function staff()
    {
        return $this->hasOneThrough(
            Staff::class, // destination class
            User::class,  // through class
            'fileno',  // Foreign key on User class
            'staff_no', // Foreign key on Staff class
            'user_id', // local Key on Profile            
            'id', // Local key on User
            );
    }
}
