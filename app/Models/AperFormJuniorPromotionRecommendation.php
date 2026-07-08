<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AperFormJuniorPromotionRecommendation extends Model
{
    use HasFactory;

    protected $table = 'aper_form_junior_promotion_recommendations';

    protected $fillable = [
        'uuid', 'aper_form_id', 'staff_id',
        'endorse', 'reject', 'chairman',
    ];

    protected static function booted(): void
    {
        static::creating(fn($m) => $m->uuid ??= (string) Str::uuid());
    }

    public function aperForm()       { return $this->belongsTo(AperForm::class); }
    public function staff()          { return $this->belongsTo(Staff::class, 'staff_id'); }
    public function chairmanStaff()  { return $this->belongsTo(Staff::class, 'chairman'); }
}
