<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employment extends Model
{
    use \App\Models\Concerns\HasUuid;
    use HasFactory;

    protected $fillable = [
        'staff_id', 'staff_category_id', 'employment_type_id', 'ippis_no', 'division_id',
        'department_id', 'highest_degree', 'date_public_first_appointment',
        'date_funaab_first_appointment', 'first_appointment_post',  'date_confirmation', 'designation_id', 'designation_title',
        'date_present_appointment', 'salary_structure_id', 'salary_step_id', 'created_by', 'updated_by'
    ];

    protected $casts = [
        'date_public_first_appointment'  => 'date',
        'date_funaab_first_appointment'  => 'date',
        'date_confirmation'              => 'date',
        'date_present_appointment'       => 'date',
    ];

    public function staff()        { return $this->belongsTo(Staff::class); }
    public function staffCategory(){ return $this->belongsTo(StaffCategory::class); }
    public function employmentType() { return $this->belongsTo(EmploymentType::class); }
    public function division()     { return $this->belongsTo(Division::class); }
    public function department()   { return $this->belongsTo(Department::class); }
    public function designation()  { return $this->belongsTo(Designation::class); }

    /** Designation from the progression set-up, else the text from the nominal roll. */
    public function getDesignationNameAttribute(): ?string
    {
        return $this->designation?->cadreRank?->cadre_rank_name ?? $this->designation_title;
    }
    public function salaryStructure(){ return $this->belongsTo(SalaryStructure::class); }
    public function salaryStep()   { return $this->belongsTo(SalaryStep::class); }
    public function degree()       { return $this->belongsTo(Degree::class, 'highest_degree'); }
}