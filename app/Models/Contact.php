<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = ['staff_id', 'contact_type_id', 'contact_value'];

    public function staff()       { return $this->belongsTo(Staff::class); }
    public function contactType() { return $this->belongsTo(ContactType::class); }
}