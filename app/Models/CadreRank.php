<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CadreRank extends Model
{
    use \App\Models\Concerns\HasUuid;
    use HasFactory;
    protected $fillable = ['cadre_id', 'cadre_rank_name'];

    public function cadre()       { return $this->belongsTo(Cadre::class); }
    public function designation() { return $this->hasOne(Designation::class); }
}
