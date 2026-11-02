<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AperFormSeniorHodRecommend extends Model
{
    use \App\Models\Concerns\HasUuid;
    protected $table = 'aper_form_senior_hod_recommends';

    // All columns except the key are mass-assignable; controllers only pass validated data.
    protected $guarded = ['id'];

    protected static function booted(): void
    {
        static::creating(fn($m) => $m->uuid ??= (string) Str::uuid());
    }

    public function aperForm()    { return $this->belongsTo(AperForm::class); }
    public function staff()       { return $this->belongsTo(Staff::class, 'staff_id'); }
    public function hodStaff()    { return $this->belongsTo(Staff::class, 'hod'); }
    public function salaryLevel() { return $this->belongsTo(SalaryLevel::class, 'promotion_level_id'); }
    public function salaryStep()  { return $this->belongsTo(SalaryStep::class, 'salary_increment_id'); }
}
