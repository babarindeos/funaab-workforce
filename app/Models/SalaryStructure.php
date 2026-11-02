<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SalaryStructure extends Model
{
    use \App\Models\Concerns\HasUuid;
    use HasFactory;
    protected $fillable = ['uuid', 'salary_structure_name'];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($m) => $m->uuid = $m->uuid ?? Str::uuid());
    }

    public function designations() { return $this->hasMany(Designation::class); }
    public function salarySteps()  { return $this->hasMany(SalaryStep::class); }
}
