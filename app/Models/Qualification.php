<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Qualification extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id', 'degree_id', 'institution',
        'start_date', 'end_date', 'degree_class_id', 'remark',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function staff()       { return $this->belongsTo(Staff::class); }
    public function degree()      { return $this->belongsTo(Degree::class); }
    public function degreeClass() { return $this->belongsTo(DegreeClass::class); }
}