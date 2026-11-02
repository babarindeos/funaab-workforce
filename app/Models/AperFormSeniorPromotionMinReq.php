<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AperFormSeniorPromotionMinReq extends Model
{
    use \App\Models\Concerns\HasUuid;
    protected $table = 'aper_form_senior_promotion_min_reqs';

    // All columns except the key are mass-assignable; controllers only pass validated data.
    protected $guarded = ['id'];

    protected static function booted(): void
    {
        static::creating(fn($m) => $m->uuid ??= (string) Str::uuid());
    }

    public function salaryLevel() { return $this->belongsTo(SalaryLevel::class); }
}
