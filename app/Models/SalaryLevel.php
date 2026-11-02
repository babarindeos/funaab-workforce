<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SalaryLevel extends Model
{
    use \App\Models\Concerns\HasUuid;
    use HasFactory;
    protected $fillable = ['uuid', 'salary_structure_id', 'level'];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($m) => $m->uuid = $m->uuid ?? Str::uuid());
    }

    public function salarySteps()  { return $this->hasMany(SalaryStep::class); }
    public function designations() { return $this->hasMany(Designation::class); }
    public function salaryStructure() { return $this->belongsTo(SalaryStructure::class); }
}
