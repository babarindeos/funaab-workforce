<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AperFormSeniorPerformance extends Model
{
    protected $table = 'aper_form_senior_performances';

    protected $fillable = [
        'uuid', 'aper_form_id', 'staff_id',
        'report_agreement', 'report_agreement_comment',
        'punctuality', 'initiative', 'expression',
        'interpersonal', 'responsibility', 'competence',
        'organisational_ability', 'work_quality', 'special_responsibility',
        'general_remarks', 'assessor',
    ];

    protected $casts = ['report_agreement' => 'boolean'];

    protected static function booted(): void
    {
        static::creating(fn($m) => $m->uuid ??= (string) Str::uuid());
    }

    public function aperForm()     { return $this->belongsTo(AperForm::class); }
    public function staff()        { return $this->belongsTo(Staff::class, 'staff_id'); }
    public function assessorStaff(){ return $this->belongsTo(Staff::class, 'assessor'); }
}
