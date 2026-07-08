<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AperFormJuniorScoringGuide extends Model
{
    use HasFactory;

    protected $table = 'aper_form_junior_scoring_guides';

    protected $fillable = ['uuid', 'metric', 'max_score'];

    protected static function booted(): void
    {
        static::creating(fn($m) => $m->uuid ??= (string) Str::uuid());
    }

    public function scorings() { return $this->hasMany(AperFormJuniorScoring::class, 'metric_id'); }
}
