<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AperFormSeniorCommunityService extends Model
{
    use HasFactory;

    protected $table = 'aper_form_senior_community_services';
    protected $fillable = ['uuid','aper_form_id','staff_id','service'];
    protected static function booted(): void { static::creating(fn($m) => $m->uuid ??= (string)Str::uuid()); }
    public function aperForm() { return $this->belongsTo(AperForm::class); }
    public function staff()    { return $this->belongsTo(Staff::class); }
}
