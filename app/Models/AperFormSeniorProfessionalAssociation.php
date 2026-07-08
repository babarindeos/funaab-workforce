<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AperFormSeniorProfessionalAssociation extends Model
{
    use HasFactory;

    protected $table = 'aper_form_senior_professional_associations';
    protected $fillable = ['uuid','aper_form_id','staff_id','membership'];
    protected static function booted(): void { static::creating(fn($m) => $m->uuid ??= (string)Str::uuid()); }
    public function aperForm() { return $this->belongsTo(AperForm::class); }
    public function staff()    { return $this->belongsTo(Staff::class); }
}
