<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AperFormSeniorScoringGuide extends Model
{
    protected $table = 'aper_form_senior_scoring_guides';

    protected $fillable = ['uuid', 'metric', 'max_score'];

    protected static function booted(): void
    {
        static::creating(fn($m) => $m->uuid ??= (string) Str::uuid());
    }

    public function scorings() { return $this->hasMany(AperFormSeniorScoring::class, 'metric_id'); }
}
