<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DivisionType extends Model
{
    use \App\Models\Concerns\HasUuid;
    use HasFactory;

    protected $fillable = ['division_type_name'];

    public function divisions()
    {
        return $this->hasMany(Division::class, 'division_type_id', 'id');
    }
}
