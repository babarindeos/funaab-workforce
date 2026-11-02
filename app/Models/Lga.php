<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lga extends Model
{
    use \App\Models\Concerns\HasUuid;
    use HasFactory;

    protected $fillable = ['state_id', 'lga_name'];

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id', 'id');
    }
}
