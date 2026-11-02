<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class College extends Model
{
    use \App\Models\Concerns\HasUuid;
    use HasFactory;

    protected $fillable = [
        'college_name',
        'college_code'
    ];
}
