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
     */
    protected $fillable = [  
        'fileno',      
        'surname',
        'firstname',
        'middlename',
        'email',
        'password',
        'role'
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
    ];

    public function staff()
    {
        return $this->hasOne(Staff::class);
    }

    public function contributor()
    {
        return $this->hasMany(FlowContributor::class, 'user_id', 'id');
    }

    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id', 'id');
    }

    public function announcement_permission()
    {
        return $this->hasOne(AnnouncementPermissions::class, 'user_id', 'id');
    }

    
}
