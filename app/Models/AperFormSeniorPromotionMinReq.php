<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AperFormSeniorPromotionMinReq extends Model
{
    protected $table = 'aper_form_senior_promotion_min_reqs';

    protected $fillable = ['uuid', 'salary_level_id', 'percentage'];

    protected static function booted(): void
    {
        static::creating(fn($m) => $m->uuid ??= (string) Str::uuid());
    }

    public function salaryLevel() { return $this->belongsTo(SalaryLevel::class); }
}
