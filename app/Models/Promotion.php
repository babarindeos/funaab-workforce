<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use \App\Models\Concerns\HasUuid;
    use HasFactory;
    protected $fillable = ['staff_id', 'designation_id', 'salary_step_id', 'effective_date', 'remark'];
    protected $casts    = ['effective_date' => 'date'];

    public function staff()       { return $this->belongsTo(Staff::class); }
    public function designation() { return $this->belongsTo(Designation::class); }
    public function salaryStep()  { return $this->belongsTo(SalaryStep::class); }
}
