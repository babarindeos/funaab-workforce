<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SenatorialDistrict extends Model
{
    use HasFactory;

    protected $table = 'senatorial_districts';

    protected $fillable = ['uuid', 'lga_id', 'senatorial_district_name'];

    protected static function booted(): void
    {
        static::creating(fn($m) => $m->uuid ??= (string) Str::uuid());
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function lga() { return $this->belongsTo(Lga::class); }
}
