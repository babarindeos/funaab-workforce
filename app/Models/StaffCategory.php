<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employment;

class StaffCategory extends Model
{
    use HasFactory;

    protected $fillable = ['parent_staff_category', 'category'];

    public function parent()
    {
        return $this->belongsTo(StaffCategory::class, 'parent_staff_category', 'id');
    }

    public function employments()
    {
        return $this->hasMany(Employment::class, 'staff_category_id');
    }
}
