<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * One row per staff member per APER form: where the form is in the
 * workflow, who signed each stage and when, and the Part C result.
 *
 * Junior:  draft → submitted → supervisor_done → hod_done → staff_commented → completed
 * Senior:  draft → submitted → hod_done → staff_commented → completed
 *
 * After the staff comment the form is "completed" straight away unless the
 * HOD recommended promotion; then it waits for the Departmental Assessment
 * Panel (status staff_commented) and becomes "completed" once scored.
 */
class AperSubmission extends Model
{
    use \App\Models\Concerns\HasUuid;

    public const DRAFT           = 'draft';
    public const SUBMITTED       = 'submitted';
    public const SUPERVISOR_DONE = 'supervisor_done';
    public const HOD_DONE        = 'hod_done';
    public const STAFF_COMMENTED = 'staff_commented';
    public const COMPLETED       = 'completed';

    public const LABELS = [
        self::DRAFT           => 'Draft – not yet submitted',
        self::SUBMITTED       => 'Submitted – awaiting assessment',
        self::SUPERVISOR_DONE => 'Supervisor done – awaiting HOD',
        self::HOD_DONE        => 'HOD done – awaiting staff comment',
        self::STAFF_COMMENTED => 'Awaiting Departmental Assessment Panel',
        self::COMPLETED       => 'Completed',
    ];

    protected $fillable = [
        'aper_form_id', 'staff_id', 'form_type', 'status', 'salary_level_id',
        'staff_submitted_at', 'supervisor_staff_id', 'supervisor_signed_at',
        'hod_staff_id', 'hod_signed_at', 'staff_commented_at',
        'panel_staff_id', 'panel_signed_at',
        'recommended_for_promotion', 'total_score', 'min_required',
        'meets_minimum', 'punctuality_ok',
    ];

    protected $casts = [
        'staff_submitted_at'        => 'datetime',
        'supervisor_signed_at'      => 'datetime',
        'hod_signed_at'             => 'datetime',
        'staff_commented_at'        => 'datetime',
        'panel_signed_at'           => 'datetime',
        'recommended_for_promotion' => 'boolean',
        'meets_minimum'             => 'boolean',
        'punctuality_ok'            => 'boolean',
        'total_score'               => 'float',
        'min_required'              => 'float',
    ];

    public function getRouteKeyName(): string { return 'uuid'; }

    public function aperForm()   { return $this->belongsTo(AperForm::class); }
    public function staff()      { return $this->belongsTo(Staff::class); }
    public function salaryLevel(){ return $this->belongsTo(SalaryLevel::class); }
    public function supervisor() { return $this->belongsTo(Staff::class, 'supervisor_staff_id'); }
    public function hod()        { return $this->belongsTo(Staff::class, 'hod_staff_id'); }
    public function panelChair() { return $this->belongsTo(Staff::class, 'panel_staff_id'); }

    public function getStatusLabelAttribute(): string
    {
        return self::LABELS[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    public function isDraft(): bool     { return $this->status === self::DRAFT; }
    public function isJunior(): bool    { return $this->form_type === 'junior'; }
    public function isSenior(): bool    { return $this->form_type === 'senior'; }

    /** Has the workflow reached (or passed) the given status? */
    public function reached(string $status): bool
    {
        return $this->rank($this->status) >= $this->rank($status);
    }

    private function rank(string $status): int
    {
        return array_search($status, array_keys(self::LABELS), true) ?: 0;
    }
}
