<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    use \App\Models\Concerns\HasUuid;
    use HasFactory;
    protected $fillable = [
        'cadre_rank_id', 'salary_structure_id', 'salary_level_id', 'next_cadre_rank',
    ];

    public function cadreRank()      { return $this->belongsTo(CadreRank::class); }
    public function salaryStructure(){ return $this->belongsTo(SalaryStructure::class); }
    public function salaryLevel()    { return $this->belongsTo(SalaryLevel::class); }
    public function nextRank()       { return $this->belongsTo(CadreRank::class, 'next_cadre_rank'); }
    public function employments()    { return $this->hasMany(Employment::class); }
    public function promotions()     { return $this->hasMany(Promotion::class); }
}
