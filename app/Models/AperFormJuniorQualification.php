<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AperFormJuniorQualification extends Model
{
    use HasFactory;

    protected $table = 'aper_form_junior_qualifications';

    protected $fillable = [
        'uuid', 'aper_form_id', 'staff_id',
        'institution', 'from_date', 'to_date',
        'qualification_obtained',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function aperForm() { return $this->belongsTo(AperForm::class); }
    public function staff()    { return $this->belongsTo(Staff::class); }
}
