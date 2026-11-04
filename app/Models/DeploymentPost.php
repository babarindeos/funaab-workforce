<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeploymentPost extends Model
{
    use \App\Models\Concerns\HasUuid;
    use HasFactory;

    protected $fillable = ['deployment_post'];

    public function deployments()
    {
        return $this->hasMany(Deployment::class, 'deployment_post_id');
    }
}
