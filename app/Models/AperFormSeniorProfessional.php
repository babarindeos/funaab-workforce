<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AperFormSeniorProfessional extends Model
{
    use HasFactory;

    protected $table = 'aper_form_senior_professionals';
    protected $fillable = ['uuid','aper_form_id','staff_id','qualification','award_body','date_obtained'];
    protected static function booted(): void { static::creating(fn($m) => $m->uuid ??= (string)Str::uuid()); }
    public function aperForm() { return $this->belongsTo(AperForm::class); }
    public function staff()    { return $this->belongsTo(Staff::class); }
}
