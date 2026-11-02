<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NextOfKin extends Model
{
    use \App\Models\Concerns\HasUuid;
    use HasFactory;

    protected $table = 'next_of_kin';

    protected $fillable = [
        'staff_id', 'surname', 'firstname', 'othernames',
        'state_id', 'city', 'address', 'phone', 'email',
    ];

    public function staff() { return $this->belongsTo(Staff::class); }
    public function state() { return $this->belongsTo(State::class); }
}