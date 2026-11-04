<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    use \App\Models\Concerns\HasUuid;

    protected $fillable = ['document_category_id', 'name', 'description', 'sort_order'];

    public function getRouteKeyName(): string { return 'uuid'; }

    public function category()  { return $this->belongsTo(DocumentCategory::class, 'document_category_id'); }
    public function documents() { return $this->hasMany(StaffDocument::class); }
}
