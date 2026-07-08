<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AperFormSeniorTrainingNeed extends Model
{
    protected $table = 'aper_form_senior_training_needs';

    protected $fillable = [
        'uuid', 'aper_form_id', 'staff_id',
        'need_training', 'other_needs', 'assessor',
    ];

    protected static function booted(): void
    {
        static::creating(fn($m) => $m->uuid ??= (string) Str::uuid());
    }

    public function aperForm()     { return $this->belongsTo(AperForm::class); }
    public function staff()        { return $this->belongsTo(Staff::class, 'staff_id'); }
    public function assessorStaff(){ return $this->belongsTo(Staff::class, 'assessor'); }
}
