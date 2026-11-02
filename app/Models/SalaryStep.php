<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SalaryStep extends Model
{
    use \App\Models\Concerns\HasUuid;
    use HasFactory;
    protected $fillable = ['uuid', 'salary_level_id', 'step'];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($m) => $m->uuid = $m->uuid ?? Str::uuid());
    }

    // salary_steps table has no salary_level_id FK — steps are standalone
    public function salaryLevel()   { return $this->belongsTo(SalaryLevel::class); }
    public function employments()    { return $this->hasMany(Employment::class); }
    public function promotions()     { return $this->hasMany(Promotion::class); }
    public function stepIncrements() { return $this->hasMany(StepIncrement::class); }
}
