<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deployment extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id', 'department_id', 'deployment_post_id',
        'start_date', 'end_date', 'expected_end_date', 'remark',
    ];

    protected $casts = [
        'start_date'        => 'date',
        'end_date'          => 'date',
        'expected_end_date' => 'date',
    ];

    public function staff()          { return $this->belongsTo(Staff::class); }
    public function department()     { return $this->belongsTo(Department::class); }
    public function deploymentPost() { return $this->belongsTo(DeploymentPost::class); }
}