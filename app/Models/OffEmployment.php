<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OffEmployment extends Model
{
    use \App\Models\Concerns\HasUuid;
    use HasFactory;

    protected $fillable = [
        'staff_id', 'staff_status_id', 'effective_date', 'reason', 'document_reference',
    ];

    protected $casts = ['effective_date' => 'date'];

    public function staff()       { return $this->belongsTo(Staff::class); }
    public function staffStatus() { return $this->belongsTo(StaffStatus::class); }
}