<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Leave extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id', 'leave_type_id', 'start_date', 'end_date',
        'resumption_date', 'approval_status', 'leave_year', 'remark',
    ];

    protected $casts = [
        'start_date'      => 'date',
        'end_date'        => 'date',
        'resumption_date' => 'date',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    // Days remaining until resumption
    public function getDaysRemainingAttribute()
    {
        if (!$this->resumption_date) return null;
        return max(0, now()->startOfDay()->diffInDays($this->resumption_date->startOfDay(), false));
    }

    // Total days of leave
    public function getTotalDaysAttribute()
    {
        if (!$this->start_date || !$this->end_date) return 0;
        return $this->start_date->diffInDays($this->end_date) + 1;
    }
}
