<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;

    public function geo_pol_zone()
    {
        return $this->belongsTo(GeoPolZone::class, 'geo_pol_zone_id', 'id');
    }

    public function lgas()
    {
        return $this->hasMany(Lga::class, 'state_id', 'id');
    }
}
