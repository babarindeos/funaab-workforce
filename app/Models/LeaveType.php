<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use \App\Models\Concerns\HasUuid;
    use HasFactory;

    protected $fillable = ['leave_type'];

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }
}
