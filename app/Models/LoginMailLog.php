<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginMailLog extends Model
{
    protected $guarded = ['id'];

    public function sender() { return $this->belongsTo(User::class, 'sent_by'); }
}
