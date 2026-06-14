<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cadre extends Model
{
    use HasFactory;
    protected $fillable = ['department_id', 'cadre_name'];

    public function department() { return $this->belongsTo(Department::class); }
    public function ranks()      { return $this->hasMany(CadreRank::class); }
}
