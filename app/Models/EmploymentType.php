<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmploymentType extends Model
{
    use \App\Models\Concerns\HasUuid;
    use HasFactory;

    protected $fillable = ['employment_type'];
}
