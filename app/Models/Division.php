<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid', 'division_type_id', 'parent_division', 'division_name', 'short_name',
    ];

    public function division_type()  { return $this->belongsTo(DivisionType::class, 'division_type_id'); }
    public function parent()         { return $this->belongsTo(Division::class, 'parent_division', 'id'); }
    public function children()       { return $this->hasMany(Division::class, 'parent_division'); }
    public function departments()    { return $this->hasMany(Department::class); }
}