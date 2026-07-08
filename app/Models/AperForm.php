<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AperForm extends Model
{
    use HasFactory;

    protected $table = 'aper_forms';

    protected $fillable = [
        'uuid',
        'name',
        'start_date',
        'end_date',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    // Auto-generate UUID on creation
    protected static function booted(): void
    {
        static::creating(function (AperForm $form) {
            if (empty($form->uuid)) {
                $form->uuid = (string) Str::uuid();
            }
        });
    }

    // Route model binding via uuid
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    // Computed status attribute
    public function getStatusAttribute(): string
    {
        $today = Carbon::today();
        return $today->between($this->start_date, $this->end_date) ? 'opened' : 'closed';
    }

    public function getIsOpenAttribute(): bool
    {
        return $this->status === 'opened';
    }

    // Scope: filter by status
    public function scopeOpened($query)
    {
        return $query->whereDate('start_date', '<=', today())
                     ->whereDate('end_date',   '>=', today());
    }

    public function scopeClosed($query)
    {
        return $query->where(function ($q) {
            $q->whereDate('end_date',   '<', today())
              ->orWhereDate('start_date', '>', today());
        });
    }

    // Scope: search by name
    public function scopeSearch($query, string $term)
    {
        return $query->where('name', 'like', "%{$term}%");
    }
}
