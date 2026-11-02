<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AperFormSeniorScoring extends Model
{
    use \App\Models\Concerns\HasUuid;
    protected $table = 'aper_form_senior_scorings';

    // All columns except the key are mass-assignable; controllers only pass validated data.
    protected $guarded = ['id'];

    protected $casts = ['score' => 'float'];

    public function aperForm()    { return $this->belongsTo(AperForm::class); }
    public function staff()       { return $this->belongsTo(Staff::class, 'staff_id'); }
    public function metric()      { return $this->belongsTo(AperFormSeniorScoringGuide::class, 'metric_id'); }
    public function assessorStaff(){ return $this->belongsTo(Staff::class, 'assessor'); }
}
