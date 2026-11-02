<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use \App\Models\Concerns\HasUuid;
    use HasFactory;

    protected $fillable = [
        'uuid', 'division_id', 'parent_department', 'department_name', 'short_name',
    ];

    public function division()       { return $this->belongsTo(Division::class); }
    public function parent()         { return $this->belongsTo(Department::class, 'parent_department', 'id'); }
    public function children()       { return $this->hasMany(Department::class, 'parent_department'); }
    public function employments()    { return $this->hasMany(Employment::class); }
    public function staff()          { return $this->hasManyThrough(Staff::class, Employment::class, 'department_id', 'id', 'id', 'staff_id'); }
    public function deployments()    { return $this->hasMany(Deployment::class); }
}