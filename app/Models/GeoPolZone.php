<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeoPolZone extends Model
{
    use HasFactory;

    protected $fillable = ['zone'];

    public function states()
    {
        return $this->hasMany(State::class, 'geo_pol_zone_id', 'id');
    }

    public function lgas()
    {
        return $this->hasManyThrough(
            LGA::class, // destination class
            State::class,  //  through class
            'geo_pol_zone_id',  // foreign key on state
            'state_id', // foreign key on LGA
            'id', // local key on GeoPolZone
            'id' // local key on State
        );
    }
}
