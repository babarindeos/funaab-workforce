<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AperFormJuniorDifficulty extends Model
{
    use \App\Models\Concerns\HasUuid;
    use HasFactory;

    protected $table = 'aper_form_junior_difficulties';

    // All columns except the key are mass-assignable; controllers only pass validated data.
    protected $guarded = ['id'];

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
