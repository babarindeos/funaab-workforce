<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AperFormSeniorScoring extends Model
{
    protected $table = 'aper_form_senior_scorings';

    protected $fillable = [
        'aper_form_id', 'staff_id',
        'metric_id', 'score', 'assessor',
    ];

    protected $casts = ['score' => 'float'];

    public function aperForm()    { return $this->belongsTo(AperForm::class); }
    public function staff()       { return $this->belongsTo(Staff::class, 'staff_id'); }
    public function metric()      { return $this->belongsTo(AperFormSeniorScoringGuide::class, 'metric_id'); }
    public function assessorStaff(){ return $this->belongsTo(Staff::class, 'assessor'); }
}
