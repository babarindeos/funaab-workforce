<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffCategory extends Model
{
    use HasFactory;

    protected $fillable = ['parent_staff_category', 'category'];

    public function parent()
    {
        return $this->belongsTo(StaffCategory::class, 'parent_staff_category', 'id');
    }
}
