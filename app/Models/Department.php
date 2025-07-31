<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'division_id',
        'parent_department',
        'department_name',
        'short_name'
    ];

   

    public function staff()
    {
        return $this->hasMany(Staff::class, 'department_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(Department::class, 'parent_department', 'id');
    }

   
}
