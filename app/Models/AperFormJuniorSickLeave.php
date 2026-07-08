<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AperFormJuniorSickLeave extends Model
{
    use HasFactory;

    protected $table = 'aper_form_junior_sick_leaves';

    protected $fillable = [
        'uuid', 'aper_form_id', 'staff_id',
        'with_medical_certificate',
        'without_medical_certificate',
        'supervisor',
    ];

    protected static function booted(): void
    {
        static::creating(fn($m) => $m->uuid ??= (string) Str::uuid());
    }

    public function aperForm()        { return $this->belongsTo(AperForm::class); }
    public function staff()           { return $this->belongsTo(Staff::class, 'staff_id'); }
    public function supervisorStaff() { return $this->belongsTo(Staff::class, 'supervisor'); }
}
