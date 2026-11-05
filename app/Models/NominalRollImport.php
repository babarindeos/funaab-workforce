<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NominalRollImport extends Model
{
    use \App\Models\Concerns\HasUuid;

    protected $guarded = ['id'];
    protected $casts = ['summary' => 'array', 'applied_at' => 'datetime'];

    public function getRouteKeyName(): string { return 'uuid'; }

    public function uploader() { return $this->belongsTo(User::class, 'uploaded_by'); }
    public function applier()  { return $this->belongsTo(User::class, 'applied_by'); }
}
