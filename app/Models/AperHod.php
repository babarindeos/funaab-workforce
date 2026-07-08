<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AperHod extends Model
{
    protected $table = 'aper_hods';

    protected $fillable = ['uuid', 'department_id', 'staff_category_id', 'staff_id', 'is_current'];

    protected $casts = ['is_current' => 'boolean'];

    protected static function booted(): void
    {
        static::creating(function (self $m) {
            if (empty($m->uuid)) $m->uuid = (string) Str::uuid();
        });
    }

    public function getRouteKeyName(): string { return 'uuid'; }

    public function department()   { return $this->belongsTo(Department::class); }
    public function staffCategory(){ return $this->belongsTo(StaffCategory::class); }
    public function staff()        { return $this->belongsTo(Staff::class); }

    public function scopeSearch($query, string $term)
    {
        return $query->whereHas('staff', fn($q) =>
            $q->where('surname',   'like', "%$term%")
              ->orWhere('firstname','like', "%$term%")
              ->orWhere('staff_no', 'like', "%$term%")
        )->orWhereHas('department', fn($q) =>
            $q->where('department_name', 'like', "%$term%")
        );
    }
}
