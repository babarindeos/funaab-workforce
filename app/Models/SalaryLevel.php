<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SalaryLevel extends Model
{
    use HasFactory;
    protected $fillable = ['uuid', 'level'];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($m) => $m->uuid = $m->uuid ?? Str::uuid());
    }

    public function salarySteps()  { return $this->hasMany(SalaryStep::class); }
    public function designations() { return $this->hasMany(Designation::class); }
}
