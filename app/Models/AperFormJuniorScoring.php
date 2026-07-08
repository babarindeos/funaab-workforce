<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AperFormJuniorScoring extends Model
{
    use HasFactory;

    protected $table = 'aper_form_junior_scorings';

    protected $fillable = [
        'uuid', 'aper_form_id', 'staff_id',
        'metric_id', 'score', 'assessor',
    ];

    protected $casts = ['score' => 'float'];

    protected static function booted(): void
    {
        static::creating(fn($m) => $m->uuid ??= (string) Str::uuid());
    }

    public function aperForm()   { return $this->belongsTo(AperForm::class); }
    public function staff()      { return $this->belongsTo(Staff::class, 'staff_id'); }
    public function metric()     { return $this->belongsTo(AperFormJuniorScoringGuide::class, 'metric_id'); }
    public function assessorStaff(){ return $this->belongsTo(Staff::class, 'assessor'); }
}
