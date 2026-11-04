<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentCategory extends Model
{
    use \App\Models\Concerns\HasUuid;

    protected $fillable = ['name', 'description', 'sort_order'];

    public function getRouteKeyName(): string { return 'uuid'; }

    public function types() { return $this->hasMany(DocumentType::class)->orderBy('sort_order')->orderBy('name'); }
}
