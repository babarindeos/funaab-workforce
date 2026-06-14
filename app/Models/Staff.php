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

    protected $casts = ['dob' => 'date'];

    public function title()          { return $this->belongsTo(StaffTitle::class, 'staff_title_id'); }
    public function gender()         { return $this->belongsTo(Gender::class); }
    public function lga()            { return $this->belongsTo(Lga::class); }
    public function maritalStatus()  { return $this->belongsTo(MaritalStatus::class); }
    public function staffStatus()    { return $this->belongsTo(StaffStatus::class); }
    public function employment()     { return $this->hasOne(Employment::class); }
    public function leaves()         { return $this->hasMany(Leave::class); }
    public function nextOfKin()      { return $this->hasOne(NextOfKin::class); }
    public function contacts()       { return $this->hasMany(Contact::class); }
    public function qualifications() { return $this->hasMany(Qualification::class); }
    public function deployments()    { return $this->hasMany(Deployment::class); }
    public function offEmployments() { return $this->hasMany(OffEmployment::class); }
    public function profile()        { return $this->hasOne(Profile::class, 'user_id', 'user_id'); }

    public function getFullNameAttribute()
    {
        return trim($this->surname . ', ' . $this->firstname . ' ' . $this->othernames);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('surname',   'like', "%$term%")
              ->orWhere('firstname', 'like', "%$term%")
              ->orWhere('staff_no',  'like', "%$term%");
        });
    }
}