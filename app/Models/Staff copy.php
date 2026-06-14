<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staff';

    protected $fillable = [
        'staff_no', 'staff_title_id', 'surname', 'firstname', 'othernames',
        'dob', 'gender_id', 'lga_id', 'senatorial_district_id',
        'marital_status_id', 'staff_status_id',
    ];

    public function employment()
    {
        return $this->hasOne(Employment::class);
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    public function getFullNameAttribute()
    {
        return $this->surname . ', ' . $this->firstname .
               ($this->othernames ? ' ' . $this->othernames : '');
    }
}
