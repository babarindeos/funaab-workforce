<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AperFormJuniorPerformance extends Model
{
    use HasFactory;

    protected $table = 'aper_form_junior_performances';

    protected $fillable = [
        'uuid', 'aper_form_id', 'staff_id',
        'punctuality', 'initiative', 'interpersonal',
        'responsibility', 'organisational_ability', 'work_quality',
        'general_remarks', 'assessor',
    ];

    protected static function booted(): void
    {
        static::creating(fn($m) => $m->uuid ??= (string) Str::uuid());
    }

    public function aperForm()    { return $this->belongsTo(AperForm::class); }
    public function staff()       { return $this->belongsTo(Staff::class, 'staff_id'); }
    public function assessorStaff(){ return $this->belongsTo(Staff::class, 'assessor'); }
}
