<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AperFormSeniorPersonal extends Model
{
    use HasFactory;

    protected $table = 'aper_form_senior_personals';
    protected $fillable = ['uuid','aper_form_id','staff_id','surname','firstname','middlename','dob','retirement_date','marital_status_id','children','telephone_no','email_address','division_id','department_id','date_first_appointment','post_first_appointment','date_last_appointment','post_last_appointment','date_current_appointment','post_current_appointment','appointment_confirmed','confirmation_date','present_grade_level_salary'];
    protected $casts = ['dob'=>'date','retirement_date'=>'date','date_first_appointment'=>'date','date_last_appointment'=>'date','date_current_appointment'=>'date','confirmation_date'=>'date','appointment_confirmed'=>'boolean'];
    protected static function booted(): void { static::creating(fn($m) => $m->uuid ??= (string)Str::uuid()); }
    public function aperForm()     { return $this->belongsTo(AperForm::class); }
    public function staff()        { return $this->belongsTo(Staff::class); }
    public function maritalStatus(){ return $this->belongsTo(MaritalStatus::class); }
    public function division()     { return $this->belongsTo(Division::class); }
    public function department()   { return $this->belongsTo(Department::class); }
}
